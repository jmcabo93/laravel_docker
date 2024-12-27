<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
     
    public function index()
    {
        $orders = Order::with('orderItems.product')->paginate(10);
        return response()->json($orders, Response::HTTP_OK);
    }

     public function store(OrderRequest $request)
    {
        DB::beginTransaction();

        try {
            
            // Crear la orden
            $order = Order::create([
                'user_id' => $request->user_id ?? auth()->id(),// Usuario autenticado o user_id
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            // Inicializar el total_amount
            $totalAmount = 0;

            // Iterar por los productos seleccionados en la orden
            foreach ($request->products as $productData) {
                // Verificar el stock disponible del producto
                $product = Product::find($productData['product_id']);
                if ($product->stock < $productData['quantity']) {
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

     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $order->load('orderItems.product');
        return response()->json($order, Response::HTTP_OK);
    }

    
    public function update(OrderRequest $request, $id)
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

            return response()->json(null, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**

     */
    public function status_order($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order->status, Response::HTTP_OK);
    }

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
            return response()->json($order, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al cancelar la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
