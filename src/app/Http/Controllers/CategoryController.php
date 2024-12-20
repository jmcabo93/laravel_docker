<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return response()->json($categories, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return response()->json($category, Response::HTTP_CREATED);
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
            $category = Category::findOrFail($id);
                        
            // Devolver los detalles del producto en formato JSON
            return response()->json($category, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el producto, devolver error 404 en formato JSON
            return response()->json([
                'message' => 'Categoría no encontrada.',
                'error' => 'Categoría no existe en la base de datos.'
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
    public function update(CategoryRequest $request, $id)
    {
        // Obtener la categoría
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return response()->json($category, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
        // Obtener la categoría
        $category = Category::findOrFail($id);

        // Verificar si la categoría tiene productos relacionados
        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque tiene productos asociados.',
                'error_code' => 'CATEGORY_HAS_PRODUCTS'
            ], 400); // Bad Request
        }

          // Si no hay productos asociados, proceder con la eliminación
          $category->delete();

          return response()->json(['message' => 'Categoría eliminada correctamente.'], 200);
       } catch (\Exception $e) {
        return response()->json(['message' => 'Error al eliminar la categoría: ' . $e->getMessage()], 500);
       }
    }
}
