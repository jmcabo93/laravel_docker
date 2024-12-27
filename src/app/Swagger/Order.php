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

     * Obtener la lista de todas las órdenes (paginadas).
     *
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Obtener la lista de todas las órdenes",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de órdenes paginada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="per_page", type="integer", example=10)
     *         )
     *     )
     * )

     * Almacenar una nueva orden.
     *
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Almacenar una nueva orden",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Orden creada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Stock insuficiente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Stock insuficiente para el producto 123")
     *         )
     *     )
     * )

     * Mostrar los detalles de una orden específica.
     *
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Mostrar los detalles de una orden específica",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la orden",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )

     * Actualizar los datos de una orden.
     *
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Actualizar los datos de una orden",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden actualizada exitosamente",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Stock insuficiente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Stock insuficiente para el producto 123")
     *         )
     *     )
     * )

     * Eliminar una orden.
     *
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Eliminar una orden",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden eliminada exitosamente",
               @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )

     * Obtener el estado de una orden.
     *
     * @OA\Get(
     *     path="/api/orders/{id}/status",
     *     summary="Obtener el estado de una orden",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado de la orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="pending")
     *         )
     *     )
     * )

     * Cancelar una orden.
     *
     * @OA\Get(
     *     path="/api/orders/{id}/cancel",
     *     summary="Cancelar una orden",
     *     tags={"Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden cancelada exitosamente",
     *          @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )

     
 */

class Order
{
   
}


