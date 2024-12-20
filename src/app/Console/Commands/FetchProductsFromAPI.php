<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\{Product,Category};

class FetchProductsFromAPI extends Command
{
    /**
     * El nombre y la firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'cargar:productos';  // Definir el nombre del comando

    /**
     * La descripción del comando.
     *
     * @var string
     */
    protected $description = 'Conecta a la API externa y guarda los productos en la base de datos';

    /**
     * Ejecutar el comando de consola.
     *
     * @return void
     */
    public function handle()
    {
        // Llamar a la API externa para obtener los productos
        $response = Http::get('https://fakestoreapi.com/products');

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $products = $response->json();  // Convertir la respuesta en un array

            // Iterar sobre los productos y guardarlos en la base de datos
            foreach ($products as $productData) {
                Product::updateOrCreate(
                    ['id' => $productData['id']],  // Verificar si el producto ya existe
                    [
                        'name' => $productData['title'],  // El nombre del producto
                        'description' => $productData['description'],  // La descripción
                        'price' => $productData['price'],  // El precio
                        'stock' => rand(10, 100),  // Stock aleatorio (ajusta como sea necesario)
                        'image' => $productData['image'],  // Imagen del producto
                        'category_id' => Category::inRandomOrder()->first()->id, //
                    ]
                );
            }

            $this->info('Productos guardados correctamente en la base de datos.');
        } else {
            $this->error('No se pudo conectar a la API o la respuesta fue incorrecta.');
        }
    }
}
