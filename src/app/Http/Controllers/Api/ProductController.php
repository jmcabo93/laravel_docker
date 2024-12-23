<?php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Tienda",
 *     version="1.0.0",
 *     description="Esta es la documentación de la API que permite gestionar categorías, productos y órdenes en una tienda.",
 *     @OA\Contact(
 *         email="jmcabo93@gmail.com"
 *     )
 * )
 */

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Productos"},
     *     summary="Obtiene una lista de productos",
     *     description="Devuelve una lista de productos paginados.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron productos"
     *     )
     * )
     */
    public function index()
    {
        // Obtenemos la página solicitada
        $page = request()->input('page', 1);

        // Obtener los productos desde la caché
        $products = Cache::tags(['products'])->remember("products_page_{$page}", 3600, function () use ($page) {
            return Product::paginate(10, ['*'], 'page', $page); // 10 productos por página
        });

        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Productos"},
     *     summary="Crea un nuevo producto",
     *     description="Almacena un nuevo producto en la base de datos.",
     *     security={{"bearerAuth": {}}},     
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Producto creado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta"
     *     )
     * )
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        
        // Limpiamos la caché de los productos
        Cache::tags(['products'])->flush();

        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Productos"},
     *     summary="Obtiene un producto específico",
     *     description="Devuelve los detalles de un producto por su ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto encontrado",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        // Intentamos obtener el producto desde la caché con tag
        $product = Cache::tags(['products'])->remember("product_{$id}", 3600, function () use ($id) {
            return Product::findOrFail($id);
        });

        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Productos"},
     *     summary="Actualiza un producto existente",
     *     description="Actualiza los datos de un producto por su ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto actualizado exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
     public function update(ProductRequest $request, $id)
     {
         // Intentamos encontrar el producto por ID
         $product = Product::findOrFail($id);
     
         // Actualizamos los datos del producto
         $product->update($request->validated());
     
         // Eliminamos el producto de la caché (para que se recargue con la versión actualizada)
         Cache::tags(['products'])->forget("product_{$id}");
     
         // Limpiamos la caché de los productos
         Cache::tags(['products'])->flush();
         return response()->json($product, Response::HTTP_OK);
     }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Productos"},
     *     summary="Elimina un producto",
     *     description="Elimina un producto de la base de datos por su ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Producto eliminado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado"
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        // Limpiamos la caché de los productos
        Cache::tags(['products'])->flush();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *     path="/api/products/random",
     *     tags={"Productos"},
     *     summary="Obtiene un producto aleatorio",
     *     description="Devuelve un producto aleatorio de la base de datos.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Producto aleatorio",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No hay productos disponibles"
     *     )
     * )
     */
    public function random_product()
    {
        // Obtenemos un producto aleatorio sin usar caché
        $product = Product::inRandomOrder()->first();

        if (!$product) {
            return response()->json(['message' => 'No hay productos disponibles.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($product, Response::HTTP_OK);
    }
}
