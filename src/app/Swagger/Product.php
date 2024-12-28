<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"name","description", "price", "stock", "category_id"},
 *     @OA\Property(property="name", type="string", description="Nombre del producto", example="Smartphone"),
 *     @OA\Property(property="description", type="string", description="Descripción del producto", example="Electronics"),
 *     @OA\Property(property="price", type="number", format="float", description="Precio del producto", example=100),
 *     @OA\Property(property="stock", type="integer", description="Cantidad de stock disponible" ,example=200),
 *     @OA\Property(property="category_id", type="integer", description="ID de la categoría a la que pertenece el producto" ,example=1),
 *     @OA\Property(property="image", type="string", description="Imagen del producto", nullable=true,example="")
 * )

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
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Producto no encontrado"
     *     )
     * )

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
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se puede eliminar el producto porque está asociado a una o más órdenes."
     *     )
     * )
     
     * @OA\Get(
     *     path="/api/random_product",
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

class Product
{
   
}
