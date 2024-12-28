<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Repositories\OrderItemRepository;
use Illuminate\Support\Facades\DB;

class OrderItemService
{
    protected $orderItemRepository;

    public function __construct(OrderItemRepository $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }


    public function getAll($perPage = 10)
    {
        return $this->orderItemRepository->paginate($perPage);
    }

    public function getOrderItem($id)
    {
        return $this->orderItemRepository->findById($id);
    }

    

    public function createOrderItem($data)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($data['product_id']);

            if ($product->stock < $data['quantity']) {
                throw new \Exception('Stock insuficiente');
            }

            $orderItem = $this->orderItemRepository->create([
                'order_id' => $data['order_id'],
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'price' => $product->price * $data['quantity'],
            ]);

            $product->decrement('stock', $data['quantity']);
            $this->updateOrderTotal($data['order_id']);

            DB::commit();

            return $orderItem;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateOrderItem($id, $data)
    {
        DB::beginTransaction();

        try {
            $orderItem = $this->orderItemRepository->findById($id);
            $product = Product::findOrFail($data['product_id']);

            if ($product->stock + $orderItem->quantity < $data['quantity']) {
                throw new \Exception('Stock insuficiente');
            }

            $product->increment('stock', $orderItem->quantity);
            $updatedOrderItem = $this->orderItemRepository->update($orderItem, [
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'price' => $product->price * $data['quantity'],
            ]);

            $product->decrement('stock', $data['quantity']);
            $this->updateOrderTotal($data['order_id']);

            DB::commit();

            return $updatedOrderItem;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteOrderItem($id)
    {
        DB::beginTransaction();

        try {
            $orderItem = $this->orderItemRepository->findById($id);
            $product = Product::findOrFail($orderItem->product_id);

            $product->increment('stock', $orderItem->quantity);
            $this->orderItemRepository->delete($orderItem);
            $this->updateOrderTotal($orderItem->order_id);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function updateOrderTotal($orderId)
    {
        $order = Order::with('orderItems')->findOrFail($orderId);
        $totalAmount = $order->orderItems->sum('price');
        $order->update(['total_amount' => $totalAmount]);
    }
}
