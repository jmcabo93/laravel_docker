<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;


class CategoryController extends Controller
{
     
    public function index()
    {
        $categories = Category::paginate(10);
        return response()->json($categories, Response::HTTP_OK);
    }
 
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return response()->json($category, Response::HTTP_CREATED);
    }
 
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return response()->json($category, Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Categoría no encontrada.',
                'error' => 'Categoría no existe en la base de datos.'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return response()->json($category, Response::HTTP_OK);
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->products()->count() > 0) {
                return response()->json([
                    'message' => 'No se puede eliminar la categoría porque tiene productos asociados.',
                    'error_code' => 'CATEGORY_HAS_PRODUCTS'
                ], 400);
            }

            $category->delete();
            return response()->json(['message' => 'Categoría eliminada correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la categoría: ' . $e->getMessage()], 500);
        }
    }
}