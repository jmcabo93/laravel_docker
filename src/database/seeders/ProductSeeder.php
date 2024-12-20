<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Ejecutar las semillas de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        // Crear una instancia de Faker
        $faker = Faker::create();

        // Obtener todas las categorías
        $categories = Category::all();

        foreach ($categories as $category) {
            // Crear 3 productos aleatorios por categoría
            for ($i = 0; $i < 3; $i++) {
                Product::create([
                    'name' => $faker->words(7, true).' '.$category->name, 
                    'description' => $faker->sentence(20),
                    'price' => $faker->randomFloat(2, 10, 500),
                    'stock' => $faker->numberBetween(10, 100),
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
