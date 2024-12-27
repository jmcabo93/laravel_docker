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

      * @OA\Get(
     *     path="/api/order-items",
     *     summary="Obtener los items de una orden",
     *     description="Obtiene todos los items de una orden con los productos relacionados",
     *     security={{"bearerAuth": {}}},
     *     tags={"OrderItem"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de items de la orden",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrderItem")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No hay items en esta orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No hay items en esta orden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al obtener los items de la orden")
     *         )
     *     )
     * )

     * @OA\Post(
     *     path="/api/order-items",
     *     summary="Almacenar un nuevo item en una orden",
     *     description="Crea un nuevo item en la orden, verificando el stock disponible y creando la relación con el producto",
     *     security={{"bearerAuth": {}}},
     *     tags={"OrderItem"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderItemPost")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item de la orden creado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/OrderItem")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Stock insuficiente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Stock insuficiente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al crear el item de la orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al crear el item de la orden")
     *         )
     *     )
     * )

    * @OA\Get(
    *     path="/api/order-items/{id}",
    *     summary="Obtener un item específico de una orden",
    *     description="Obtiene los detalles de un item de la orden, incluyendo la relación con el producto",
    *     security={{"bearerAuth": {}}},
    *     tags={"OrderItem"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID del item de la orden",
    *         @OA\Schema(type="integer")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Detalles del item de la orden",
    *         @OA\JsonContent(ref="#/components/schemas/OrderItem")
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Item de la orden no encontrado",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Item de la orden no encontrado")
    *         )
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Error al obtener el item de la orden",
    *         @OA\JsonContent(
    *             @OA\Property(property="message", type="string", example="Error al obtener el item de la orden")
    *         )
    *     )
    * )

     * @OA\Put(
     *     path="/api/order-items/{id}",
     *     summary="Actualizar un item de la orden",
     *     description="Actualiza los detalles de un item en la orden, verificando el stock disponible",
     *     security={{"bearerAuth": {}}},
     *     tags={"OrderItem"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del item de la orden",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderItemPost")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item de la orden actualizado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/OrderItem")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Producto no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Stock insuficiente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Stock insuficiente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al actualizar el item de la orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al actualizar el item de la orden")
     *         )
     *     )
     * )

     * @OA\Delete(
     *     path="/api/order-items/{id}",
     *     summary="Eliminar un item de la orden",
     *     description="Elimina un item de la orden y restaura el stock del producto",
     *     security={{"bearerAuth": {}}},
     *     tags={"OrderItem"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del item de la orden",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Item de la orden eliminado con éxito"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al eliminar el item de la orden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al eliminar el item de la orden")
     *         )
     *     )
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