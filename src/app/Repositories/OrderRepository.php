<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderRepository
{
    public function getAllOrders($pagination = 10)
    {
        return Order::with('orderItems.product')->paginate($pagination);
    }

    public function findOrderById($id)
    {
        return Order::with('orderItems.product')->findOrFail($id);
    }

    public function createOrder(array $data)
    {
        return Order::create($data);
    }

    public function updateOrder(Order $order, array $data)
    {
        return $order->update($data);
    }

    public function deleteOrder(Order $order)
    {
        return $order->delete();
    }

    public function createOrderItem(array $data)
    {
        return OrderItem::create($data);
    }

    public function deleteOrderItems(Order $order)
    {
        return $order->orderItems()->delete();
    }

    public function findProductById($id)
    {
        return Product::find($id);
    }

    public function adjustProductStock(Product $product, $quantity)
    {
        return $product->decrement('stock', $quantity);
    }

    public function restoreProductStock(Product $product, $quantity)
    {
        return $product->increment('stock', $quantity);
    }
}
