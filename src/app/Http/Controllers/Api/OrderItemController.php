<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderItemRequest;
use App\Services\OrderItemService;
use Illuminate\Http\Response;

class OrderItemController extends Controller
{
    protected $orderItemService;

    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function index()
    {
        $orderItems = $this->orderItemService->getAll();
        return response()->json($orderItems, Response::HTTP_OK);
    }

    public function store(OrderItemRequest $request)
    {
        try {
            $orderItem = $this->orderItemService->createOrderItem($request->validated());
            return response()->json($orderItem, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {
            $orderItem = $this->orderItemService->getOrderItem($id);
            return response()->json($orderItem, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(OrderItemRequest $request, $id)
    {
        try {
            $orderItem = $this->orderItemService->updateOrderItem($id, $request->validated());
            return response()->json($orderItem, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id)
    {
        try {
            $this->orderItemService->deleteOrderItem($id);
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
