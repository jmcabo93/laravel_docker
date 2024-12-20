<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Obtener la lista de todas las órdenes (paginadas).
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('orderItems.product')->paginate(10);
        return response()->json($orders, Response::HTTP_OK);
    }

    /**
     * Almacenar una nueva orden.
     *
     * @param  \App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Crear la orden
            $order = Order::create([
                'user_id' => $request->user_id,
                'status' =>  'pending',
                'total_amount' =>0,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Obtener la orden por ID
        $order = Order::findOrFail($id);

        // Cargar los items de la orden
        $order->load('orderItems.product');

        // Retornar la orden con los productos asociados
        return response()->json($order, Response::HTTP_OK);
    }

    /**
     * Actualizar los datos de una orden.
     *
     * @param  \App\Http\Requests\OrderRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Buscar la orden por ID
            $order = Order::findOrFail($id);

            // Calcular el total_amount para la orden
            $totalAmount = 0;

            // Recuperar los OrderItems actuales de la orden para poder restablecer el stock
            $existingOrderItems = $order->orderItems;

            // Primero, restauramos el stock de los productos que están en la orden
            foreach ($existingOrderItems as $orderItem) {
                $product = Product::find($orderItem->product_id);
                if ($product) {
                    // Reponer el stock del producto según la cantidad original en el OrderItem
                    $product->increment('stock', $orderItem->quantity);
                }
            }

            // Eliminar los OrderItems actuales
            $order->orderItems()->delete();

            // Iterar por los productos del request para crear los nuevos OrderItems
            foreach ($request->products as $productData) {
                // Obtener el producto correspondiente
                $product = Product::find($productData['product_id']);
                if (!$product || $product->stock < $productData['quantity']) {
                    return response()->json([
                        'message' => 'Stock insuficiente para el producto ' . $productData['product_id'],
                    ], Response::HTTP_BAD_REQUEST);
                }

                // Calcular el precio total del producto por cantidad
                $price = $product->price * $productData['quantity'];

                // Crear los nuevos items de la orden
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                ]);

                // Sumar al total_amount
                $totalAmount += $price;

                // Reducir el stock del producto
                $product->decrement('stock', $productData['quantity']);
            }

            // Actualizar la orden con el nuevo total_amount y status
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Buscar la orden por ID
            $order = Order::findOrFail($id);
            
            
            // Recuperar los items de la orden antes de eliminarla
            $orderItems = $order->orderItems;
            
            // Verificar que no se haya cancelado antes para que no aumente el stock
            if($order->status != 'canceled'){

            // Eliminar los items de la orden
            foreach ($orderItems as $orderItem) {
                // Restaurar el stock del producto
                $product = Product::find($orderItem->product_id);
                $product->increment('stock', $orderItem->quantity);
            }
            }

            // Eliminar los items de la orden
            $order->orderItems()->delete();

            // Eliminar la orden
            $order->delete();
            
            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function status_order($id)
    {
        // Obtener la orden por ID
        $order = Order::findOrFail($id);

        // Retornar la orden
        return response()->json($order->status, Response::HTTP_OK);
    }

    public function cancel_order($id)
    {
        DB::beginTransaction();

        try {


            // Buscar la orden por ID
            $order = Order::findOrFail($id);
            
            // Verificar que no se haya cancelado antes para que no aumente el stock
            if($order->status != 'canceled'){

            // Recuperar los items de la orden antes de eliminarla
            $orderItems = $order->orderItems;

            foreach ($orderItems as $orderItem) {
                // Restaurar el stock del producto
                $product = Product::find($orderItem->product_id);
                $product->increment('stock', $orderItem->quantity);
            }

            

            // Cancelar la orden
            $order->update([
                'status' => 'canceled',
            ]);
            }
            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al cancelar la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
