<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;

class FetchProductsFromAPIRepository
{
    public function saveProduct(array $productData): void
    {
        Product::updateOrCreate(
            ['id' => $productData['id']],
            [
                'name' => $productData['title'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $this->generateRandomStock(),
                'image' => $productData['image'],
                'category_id' => $this->getRandomCategoryId(),
            ]
        );
    }

    protected function generateRandomStock(): int
    {
        return rand(10, 100);
    }

    protected function getRandomCategoryId(): int
    {
        return Category::inRandomOrder()->first()->id;
    }
}
