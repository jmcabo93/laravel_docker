<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Services\ProductCacheService;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class ProductService
{
    protected $repository;
    protected $cacheService;

    public function __construct(ProductRepository $repository, ProductCacheService $cacheService)
    {
        $this->repository = $repository;
        $this->cacheService = $cacheService;
    }

    /**
     * Obtener productos paginados, primero se intenta desde caché.
     */
    public function getProductsPage($page)
    {   
        $this->cacheService->clearCache();
        return $this->cacheService->getProductsPage($page, function () use ($page) {
            return $this->repository->paginate(10, $page); // Si no está en caché, obtener desde el repositorio
        });
    }

    /**
     * Crear un producto y actualizar la caché.
     */
    public function createProduct(array $data)
    {
        $product = $this->repository->create($data);
        $this->cacheService->clearCache(); // Limpiar caché después de crear un producto
        return $product;
    }

    /**
     * Obtener un producto por su ID desde la caché o repositorio.
     */
    public function getProductById($id)
    {
        try{
        return $this->cacheService->getProduct($id, function () use ($id) {
            return $this->repository->findById($id); // Si no está en caché, obtener desde el repositorio
        });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Producto no existe en la base de datos.',
                'error' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Actualizar un producto y actualizar la caché.
     */
    public function updateProduct($id, array $data)
    {   
        try{
        $product = $this->repository->findById($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Producto no existe en la base de datos.',
                'error' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
        $updatedProduct = $this->repository->update($product, $data);
        $this->cacheService->forgetProduct($id); // Limpiar el producto de la caché
        $this->cacheService->clearCache(); // Limpiar toda la caché de productos
        return $updatedProduct;
    }

    /**
     * Eliminar un producto y limpiar la caché.
    */
    
    public function deleteProduct($id)
    {   
        
        try{
        $product = $this->repository->findById($id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Producto no existe en la base de datos.',
                'error' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        $this->cacheService->forgetProduct($id); // Limpiar caché de producto
        $this->cacheService->clearCache(); // Limpiar toda la caché de productos
        
        // Verificar si el producto tiene OrderItems asociados
        if ($product->orderItems()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el producto porque está asociado a una o más órdenes.',
                'error_code' =>  Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }
        return $this->repository->delete($product);

    }

    /**
     * Obtener un producto aleatorio desde el repositorio.
     */
    public function getRandomProduct()
    {
       return $this->repository->getRandom();

    }
}
