<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Repositories\FetchProductsFromAPIRepository;

class ProductSyncService
{
    protected FetchProductsFromAPIRepository $productRepository;

    public function __construct(FetchProductsFromAPIRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function syncProducts()
    {
        $response = Http::get('https://fakestoreapi.com/products');

        if ($response->successful()) {
            $products = $response->json();
            foreach ($products as $productData) {
                $this->productRepository->saveProduct($productData);
            }
        } else {
            throw new \Exception('No se pudo conectar a la API o la respuesta fue incorrecta.');
        }
    }
}
