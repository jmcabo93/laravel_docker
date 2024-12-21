<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OrderItem",
 *     type="object",
 *     required={"order_id", "product_id", "quantity", "price"},
 *     @OA\Property(property="order_id",type="integer",description="ID de la orden",example=101),
 *     @OA\Property(property="product_id",type="integer",description="ID del producto",example=202),
 *     @OA\Property(property="quantity",type="integer",description="Cantidad de productos",example=2),
 *     @OA\Property(property="price",type="number",format="float",description="Preciototal",example=50.00),
 *     @OA\Property(property="product",type="object",description="Detalles del producto",ref="#/components/schemas/Product"),
 * )
 */

class OrderItem
{
   
}

/**
 * @OA\Schema(
 *     schema="OrderItemPost",
 *     type="object",
 *     required={"order_id", "product_id", "quantity", "price"},
 *     @OA\Property(property="order_id",type="integer",description="ID de la orden",example=101),
 *     @OA\Property(property="product_id",type="integer",description="ID del producto",example=202),
 *     @OA\Property(property="quantity",type="integer",description="Cantidad de productos",example=2),
 * )
 */

class OrderItemPost
{
   
}