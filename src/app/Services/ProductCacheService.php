<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ProductCacheService
{
    public function getProductsPage($page, $callback)
    {
        return Cache::tags(['products'])->remember("products_page_{$page}", 3600, $callback);
    }

    public function getProduct($id, $callback)
    {
        return Cache::tags(['products'])->remember("product_{$id}", 3600, $callback);
    }

    public function clearCache()
    {
        Cache::tags(['products'])->flush();
    }

    public function forgetProduct($id)
    {
        Cache::tags(['products'])->forget("product_{$id}");
    }
}