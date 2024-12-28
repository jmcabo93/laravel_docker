<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders($pagination = 10)
    {
        return $this->orderRepository->getAllOrders($pagination);
    }

    public function getOrderById($id)
    {
      return $this->orderRepository->findOrderById($id);
    }

    public function createOrder(array $requestData)
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->createOrder([
                'user_id' => $requestData['user_id'] ?? auth()->id(),
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($requestData['products'] as $productData) {
                $product = $this->orderRepository->findProductById($productData['product_id']);

                if ($product->stock < $productData['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto " . $productData['product_id']);
                }

                $price = $product->price * $productData['quantity'];
                $this->orderRepository->createOrderItem([
                    'order_id' => $order->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                ]);

                $totalAmount += $price;
                $this->orderRepository->adjustProductStock($product, $productData['quantity']);
            }

            $this->orderRepository->updateOrder($order, ['total_amount' => $totalAmount]);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

   public function updateOrder($id, array $requestData)
{
    try {
        // Iniciar la transacción dentro del try
        DB::beginTransaction();

        // Buscar la orden
        $order = $this->orderRepository->findOrderById($id);
        
        // Restaurar el stock de los productos eliminados de la orden (incrementar el stock)
        foreach ($order->orderItems as $orderItem) {
            $product = $this->orderRepository->findProductById($orderItem->product_id);
            // Restaurar (incrementar) el stock de los productos que fueron eliminados de la orden
            $this->orderRepository->restoreProductStock($product, $orderItem->quantity);
        }

        // Eliminar los productos actuales de la orden
        $this->orderRepository->deleteOrderItems($order);

        // Inicializar el monto total
        $totalAmount = 0;

        // Procesar los nuevos productos de la solicitud
        foreach ($requestData['products'] as $productData) {
            $product = $this->orderRepository->findProductById($productData['product_id']);

            // Verificar si hay suficiente stock para el producto
            if ($product->stock < $productData['quantity']) {
                throw new \Exception("Stock insuficiente para el producto " . $productData['product_id']);
            }

            // Calcular el precio total del producto
            $price = $product->price * $productData['quantity'];
            
            // Crear el item de la orden
            $this->orderRepository->createOrderItem([
                'order_id' => $order->id,
                'product_id' => $productData['product_id'],
                'quantity' => $productData['quantity'],
                'price' => $price,
            ]);

            // Actualizar el total de la orden
            $totalAmount += $price;

            // Reducir (decrementar) el stock del producto para los nuevos productos agregados
            $this->orderRepository->adjustProductStock($product, $productData['quantity']);
        }

        // Actualizar la orden con el nuevo total y estado
        $this->orderRepository->updateOrder($order, [
            'status' => $requestData['status'],
            'total_amount' => $totalAmount,
        ]);

        // Confirmar la transacción si todo ha ido bien
        DB::commit();

        return $order;
    } catch (\Exception $e) {
        // Si hay algún error, deshacer todos los cambios
        DB::rollBack();
        throw $e; // Lanzar el error para ser manejado por el controlador
    }
}




    public function deleteOrder($id)
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->findOrderById($id);

            if ($order->status != 'canceled') {
                foreach ($order->orderItems as $orderItem) {
                    $product = $this->orderRepository->findProductById($orderItem->product_id);
                    $this->orderRepository->restoreProductStock($product, $orderItem->quantity);
                }
            }

            $this->orderRepository->deleteOrderItems($order);
            $this->orderRepository->deleteOrder($order);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancelOrder($id)
    {
        DB::beginTransaction();

        try {
            $order = $this->orderRepository->findOrderById($id);

            if ($order->status != 'canceled') {
                foreach ($order->orderItems as $orderItem) {
                    $product = $this->orderRepository->findProductById($orderItem->product_id);
                    $this->orderRepository->restoreProductStock($product, $orderItem->quantity);
                }

                $this->orderRepository->updateOrder($order, ['status' => 'canceled']);
            }

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
