<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     required={"user_id", "products"},
 *     @OA\Property(property="user_id", type="integer", description="ID del usuario", example=1),
  *    @OA\Property(property="status", type="string", description="Estado de la orden", example="pending"),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"product_id", "quantity"},
 *             @OA\Property(property="product_id", type="integer", description="ID del producto", example=1),
 *             @OA\Property(property="quantity", type="integer", description="Cantidad del producto", example=2)
 *         )
 *     )
 * )
 */

class Order
{
   
}


