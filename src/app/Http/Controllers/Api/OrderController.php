<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Operaciones sobre las órdenes"
 * )
 */
class OrderController extends Controller
{
    /**
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
     */
    public function index()
    {
        $orders = Order::with('orderItems.product')->paginate(10);
        return response()->json($orders, Response::HTTP_OK);
    }

    /**
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
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Crear la orden
            $order = Order::create([
                'user_id' => $request->user_id,
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            // Inicializar el total_amount
            $totalAmount = 0;

            // Iterar por los productos seleccionados en la orden
            foreach ($request->products as $productData) {
                // Verificar el stock disponible del producto
                $product = Product::find($productData['product_id']);
                if (!$product || $product->stock < $productData['quantity']) {
                    return response()->json([
                        'message' => 'Stock insuficiente para el producto ' . $productData['product_id'],
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Calcular el precio total del producto por cantidad
                $price = $product->price * $productData['quantity'];

                // Crear los items de la orden
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                ]);

                // Actualizar el total de la orden
                $totalAmount += $price;

                // Reducir el stock del producto
                $product->decrement('stock', $productData['quantity']);
            }

            // Actualizar el total_amount de la orden
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            // Retornar la orden creada
            return response()->json($order, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
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
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $order->load('orderItems.product');
        return response()->json($order, Response::HTTP_OK);
    }

    /**
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
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $totalAmount = 0;
            $existingOrderItems = $order->orderItems;

            foreach ($existingOrderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    $product->increment('stock', $orderItem->quantity);
                }
            }

            $order->orderItems()->delete();

            foreach ($request->products as $productData) {
                $product = Product::find($productData['product_id']);
                if (!$product || $product->stock < $productData['quantity']) {
                    return response()->json([
                        'message' => 'Stock insuficiente para el producto ' . $productData['product_id'],
                    ], Response::HTTP_BAD_REQUEST);
                }

                $price = $product->price * $productData['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                ]);

                $totalAmount += $price;
                $product->decrement('stock', $productData['quantity']);
            }

            $order->update([
                'status' => $request->status,
                'total_amount' => $totalAmount,
            ]);

            DB::commit();

            return response()->json($order, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
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
     *         response=204,
     *         description="Orden eliminada exitosamente"
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            $orderItems = $order->orderItems;

            if ($order->status != 'canceled') {
                foreach ($orderItems as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    $product->increment('stock', $orderItem->quantity);
                }
            }

            $order->orderItems()->delete();
            $order->delete();

            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
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
     */
    public function status_order($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order->status, Response::HTTP_OK);
    }

    /**
     * Cancelar una orden.
     *
     * @OA\Post(
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
     *         response=204,
     *         description="Orden cancelada exitosamente"
     *     )
     * )
     */
    public function cancel_order($id)
    {
        DB::beginTransaction();

        try {
            $order = Order::findOrFail($id);
            if ($order->status != 'canceled') {
                $orderItems = $order->orderItems;

                foreach ($orderItems as $orderItem) {
                    $product = Product::find($orderItem->product_id);
                    $product->increment('stock', $orderItem->quantity);
                }

                $order->update(['status' => 'canceled']);
            }

            DB::commit();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al cancelar la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
