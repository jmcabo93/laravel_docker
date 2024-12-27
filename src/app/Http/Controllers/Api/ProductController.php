<?php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;



class ProductController extends Controller
{
    
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

   
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        
        // Limpiamos la caché de los productos
        Cache::tags(['products'])->flush();

        return response()->json($product, Response::HTTP_CREATED);
    }

    /**

     */
    public function show($id)
    {
        // Intentamos obtener el producto desde la caché con tag
        $product = Cache::tags(['products'])->remember("product_{$id}", 3600, function () use ($id) {
            return Product::findOrFail($id);
        });

        return response()->json($product, Response::HTTP_OK);
    }


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

 
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        // Limpiamos la caché de los productos
        Cache::tags(['products'])->flush();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

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
