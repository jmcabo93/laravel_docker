<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return response()->json($orders, Response::HTTP_OK);
    }

    public function store(OrderRequest $request)
    {
            $order = $this->orderService->createOrder($request->all());
            return response()->json($order, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $order = $this->orderService->getOrderById($id);
        return response()->json($order, Response::HTTP_OK);
    }

    public function update(OrderRequest $request, $id)
    {
            $order = $this->orderService->updateOrder($id, $request->all());
            return response()->json($order, Response::HTTP_OK);
    }

    public function destroy($id)
    {
            $order=$this->orderService->deleteOrder($id);
            return response()->json($order, Response::HTTP_OK);

    }

    public function cancel_order($id)
    {

            $order = $this->orderService->cancelOrder($id);
            return response()->json($order, Response::HTTP_OK);

    }

    public function status_order($id)
   {
    
        $order = $this->orderService->getOrderById($id);
        return response()->json($order->status, Response::HTTP_OK);

    }
}
