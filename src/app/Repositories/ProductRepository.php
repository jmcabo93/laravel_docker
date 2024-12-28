<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginate($perPage, $page)
    {
        return Product::paginate($perPage, ['*'], 'page', $page);
    }

    public function findById($id)
    {
        return Product::findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data)
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product)
    {
       return $product->delete();
    }

    public function getRandom()
    {
        return Product::inRandomOrder()->first();
    }

}
