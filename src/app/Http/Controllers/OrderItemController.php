<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Http\Requests\OrderItemRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    /**
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
     */
    public function index()
    {

            // Obtener los items de la orden
            $orderItems = OrderItem::with('product')->paginate(10);
            return response()->json($orderItems, Response::HTTP_OK);
        
    }

    /**
     * @OA\Post(
     *     path="/api/order-items",
     *     summary="Almacenar un nuevo item en una orden",
     *     description="Crea un nuevo item en la orden, verificando el stock disponible y creando la relación con el producto",
     *     security={{"bearerAuth": {}}},
     *     tags={"OrderItem"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrderItem")
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
     */
    public function store(OrderItemRequest $request)
    {
        DB::beginTransaction();

        try {
            // Buscar el producto
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
            }

            // Verificar si hay stock suficiente
            if ($product->stock < $request->quantity) {
                return response()->json(['message' => 'Stock insuficiente'], Response::HTTP_BAD_REQUEST);
            }

            // Crear el item de la orden
            $orderItem = OrderItem::create([
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price * $request->quantity, // Precio por cantidad
            ]);

            // Reducir el stock del producto
            $product->decrement('stock', $request->quantity);

            DB::commit();

            return response()->json($orderItem, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear el item de la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
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
     *         @OA\JsonContent(ref="#/components/schemas/OrderItem")
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
     */
    public function update(OrderItemRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // Buscar el item de la orden
            $orderItem = OrderItem::findOrFail($id);

            // Obtener el producto asociado
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
            }

            // Verificar si la cantidad solicitada es mayor al stock disponible
            if ($product->stock + $orderItem->quantity < $request->quantity) {
                return response()->json(['message' => 'Stock insuficiente'], Response::HTTP_BAD_REQUEST);
            }

            // Restaurar el stock del producto para la cantidad que se modificó
            $product->increment('stock', $orderItem->quantity);

            // Actualizar el item de la orden
            $orderItem->update([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price * $request->quantity,
            ]);

            // Reducir el stock del producto con la nueva cantidad
            $product->decrement('stock', $request->quantity);

            DB::commit();

            return response()->json($orderItem, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el item de la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
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
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Buscar el item de la orden
            $orderItem = OrderItem::findOrFail($id);

            // Obtener el producto asociado
            $product = Product::find($orderItem->product_id);

            // Reponer el stock del producto
            $product->increment('stock', $orderItem->quantity);

            // Eliminar el item de la orden
            $orderItem->delete();

            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar el item de la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
