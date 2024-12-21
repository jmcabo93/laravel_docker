<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"name", "price", "stock", "category_id"},
 *     @OA\Property(property="id", type="integer", description="ID del producto"),
 *     @OA\Property(property="name", type="string", description="Nombre del producto"),
 *     @OA\Property(property="description", type="string", description="Descripción del producto"),
 *     @OA\Property(property="price", type="number", format="float", description="Precio del producto"),
 *     @OA\Property(property="stock", type="integer", description="Cantidad de stock disponible"),
 *     @OA\Property(property="category_id", type="integer", description="ID de la categoría a la que pertenece el producto"),
 *     @OA\Property(property="image", type="string", description="Imagen del producto", nullable=true)
 * )
 */
class Product
{
    // No necesitas implementar nada aquí, solo estamos usando las anotaciones para la documentación de Swagger
}
