<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // Importante para que funcione

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Hamburguesas'],
            ['name' => 'Hot Dogs'],
            ['name' => 'Pollo'],
            ['name' => 'Ensaladas'],
            ['name' => 'Acompañamientos'],
            ['name' => 'Bebidas'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}