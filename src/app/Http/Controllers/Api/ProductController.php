<?php
namespace App\Http\Controllers\Api;

use App\Services\ProductService;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Obtener productos paginados.
     */
    public function index()
    {
        $page = request()->input('page', 1);
        $products = $this->productService->getProductsPage($page);
        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * Crear un nuevo producto.
     */
    public function store(ProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * Obtener un producto por su ID.
     */
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * Actualizar un producto.
     */
    public function update(ProductRequest $request, $id)
    {
        $updatedProduct = $this->productService->updateProduct($id, $request->validated());
        return response()->json($updatedProduct, Response::HTTP_OK);
    }

    /**
     * Eliminar un producto.
     */
    public function destroy($id)
    {
        $product=$this->productService->deleteProduct($id);
        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * Obtener un producto aleatorio.
     */
    public function random_product()
    {
        $product = $this->productService->getRandomProduct();
        return response()->json($product, Response::HTTP_OK);
    }
}