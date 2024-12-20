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
     * Almacenar un nuevo item en una orden.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
    */
    
    public function index()
    {
    try {
        // Obtener los items de la orden
        $orderItems = OrderItem::with('product')->paginate(10);

        // Si no se encuentran items
        if ($orderItems->isEmpty()) {
            return response()->json(['message' => 'No hay items en esta orden'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($orderItems, Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al obtener los items de la orden: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
     * Almacenar un nuevo item en una orden.
     *
     * @param  \App\Http\Requests\OrderItemRequest  $request
     * @return \Illuminate\Http\Response
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
     * Actualizar un item de la orden.
     *
     * @param  \App\Http\Requests\OrderItemRequest  $request
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\Response
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
     * Eliminar un item de la orden.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
