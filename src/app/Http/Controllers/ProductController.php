<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(10);  // 10 productos por página
        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // Buscar el producto por ID, lanzará excepción si no lo encuentra
            $product = Product::findOrFail($id);
            
            // Devolver los detalles del producto en formato JSON
            return response()->json($product, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el producto, devolver error 404 en formato JSON
            return response()->json([
                'message' => 'Producto no encontrado.',
                'error' => 'Producto no existe en la base de datos.'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());
        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Display a random product.
     *
     * @return \Illuminate\Http\Response
     */
    public function random_product()
    {
         try {
            // Obtener un producto aleatorio
            $product = Product::inRandomOrder()->first();

            // Verificar si existe el producto
            if (!$product) {
                return response()->json(['message' => 'No hay productos disponibles.'], Response::HTTP_NOT_FOUND);
            }

            // Retornar el producto
            return response()->json($product, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el producto aleatorio: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
