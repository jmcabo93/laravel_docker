<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     required={"name", "description"},
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="description", type="string", example="Devices and gadgets related to electronics")
 * )

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

class Category
{
   
}
