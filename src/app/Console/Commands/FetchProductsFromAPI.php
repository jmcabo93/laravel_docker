<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductSyncService;

class FetchProductsFromAPI extends Command
{
    protected $signature = 'cargar:productos';
    protected $description = 'Conecta a la API externa y guarda los productos en la base de datos';

    protected ProductSyncService $productSyncService;

    public function __construct(ProductSyncService $productSyncService)
    {
        parent::__construct();
        $this->productSyncService = $productSyncService;
    }

    public function handle()
    {
        try {
            $this->productSyncService->syncProducts();
            $this->info('Productos guardados correctamente en la base de datos.');
        } catch (\Exception $e) {
            $this->error("Error al sincronizar productos: {$e->getMessage()}");
        }
    }
}
