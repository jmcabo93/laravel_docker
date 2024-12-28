<?php

namespace App\Repositories;

use App\Models\OrderItem;

class OrderItemRepository
{
    public function paginate($perPage)
    {
        return OrderItem::with('product')->paginate($perPage);
    }

    public function findById($id)
    {
        return OrderItem::with('product')->findOrFail($id);
    }

    public function create(array $data)
    {
        return OrderItem::create($data);
    }

    public function update(OrderItem $orderItem, array $data)
    {
        $orderItem->update($data);
        return $orderItem;
    }

    public function delete(OrderItem $orderItem)
    {
       return $orderItem->delete();
    }
}
