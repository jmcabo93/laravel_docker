<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;


class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Lista de categorías",
     *     description="Obtiene una lista paginada de todas las categorías disponibles.",
     *     security={{"bearerAuth": {}}},
     *     tags={"Categorías"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de categorías paginada.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category")),
     *             @OA\Property(property="links", type="object", @OA\AdditionalProperties(type="string")),
     *             @OA\Property(property="meta", type="object", @OA\AdditionalProperties(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta."
     *     )
     * )
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return response()->json($categories, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Crear una nueva categoría",
     *     description="Crea una nueva categoría proporcionando un nombre y una descripción.",
     *     security={{"bearerAuth": {}}},
     *     tags={"Categorías"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Categoría creada correctamente.",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos incorrectos o incompletos."
     *     )
     * )
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return response()->json($category, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Mostrar una categoría específica",
     *     description="Obtiene los detalles de una categoría específica mediante su ID.",
     *     security={{"bearerAuth": {}}},
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría encontrada.",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada."
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Actualizar una categoría existente",
     *     description="Actualiza los datos de una categoría existente mediante su ID.",
     *     security={{"bearerAuth": {}}},
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría actualizada correctamente.",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos incorrectos o incompletos."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada."
     *     )
     * )
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());
        return response()->json($category, Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Eliminar una categoría",
     *     description="Elimina una categoría específica mediante su ID. Si tiene productos asociados, no se puede eliminar.",
     *     security={{"bearerAuth": {}}},
     *     tags={"Categorías"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la categoría",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Categoría eliminada correctamente."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Categoría no se puede eliminar debido a productos asociados."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Categoría no encontrada."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor."
     *     )
     * )
     */
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