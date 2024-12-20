<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Ejecutar las semillas de la base de datos.
     *
     * @return void
     */
    public function run()
    {
        // Crear 3 categorías en español
        Category::create([
            'name' => 'Electrónica',
            'description' => 'Dispositivos y gadgets electrónicos de todo tipo.',
        ]);

        Category::create([
            'name' => 'Muebles',
            'description' => 'Muebles para el hogar y la oficina.',
        ]);

        Category::create([
            'name' => 'Ropa',
            'description' => 'Ropa para hombres y mujeres.',
        ]);
    }
}
