<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\OrderService;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Response;
use App\Http\Requests\OrderRequest;

class OrderControllerTest extends TestCase
{
    protected $orderServiceMock;
    protected $orderController;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear el mock del servicio
        $this->orderServiceMock = Mockery::mock(OrderService::class);

        // Crear el controlador inyectando el servicio mockeado
        $this->orderController = new OrderController($this->orderServiceMock);
    }

    public function test_index_returns_paginated_orders()
    {
        // Configurar el mock para retornar datos simulados
        $mockOrders = [
            'data' => [
                ['id' => 1, 'status' => 'pending', 'total_amount' => 100],
                ['id' => 2, 'status' => 'completed', 'total_amount' => 200],
            ],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
            ],
        ];

        $this->orderServiceMock
            ->shouldReceive('getAllOrders')
            ->once()
            ->andReturn($mockOrders);

        // Llamar al método y verificar la respuesta
        $response = $this->orderController->index();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($mockOrders, $response->getData(true));
    }


    public function test_store_creates_new_order()
    {
    $mockRequestData = [
        'user_id' => 1,
        'products' => [
            ['product_id' => 1, 'quantity' => 2],
        ],
    ];

    $mockOrder = [
        'id' => 1,
        'user_id' => 1,
        'status' => 'pending',
        'total_amount' => 200,
    ];

    $this->orderServiceMock
        ->shouldReceive('createOrder')
        ->once()
        ->with($mockRequestData)
        ->andReturn($mockOrder);

    // Crear una instancia de OrderRequest con los datos simulados
    $request = OrderRequest::create('/orders', 'POST', $mockRequestData);

    // Llamar al método store
    $response = $this->orderController->store($request);

    $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    $this->assertEquals($mockOrder, $response->getData(true));
    }


    public function test_status_order_returns_order_status()
    {
    $mockStatus = 'pending';

    $this->orderServiceMock
        ->shouldReceive('findOrderById')
        ->once()
        ->with(1)
        ->andReturn((object) ['status' => $mockStatus]);

    // Simular una solicitud HTTP GET al controlador
    $response = $this->orderController->status_order(1);

    $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    $this->assertEquals($mockStatus, $response->getData());
    }



}
