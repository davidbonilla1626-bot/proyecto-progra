<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Función rápida para obtener el ID de la categoría por su nombre
        $getCatId = fn($name) => Category::where('name', $name)->first()->id;

        $products = [
            // --- HAMBURGUESAS ---
            ['category_id' => $getCatId('Hamburguesas'), 'name' => 'Hamburguesa Gran Megabyte', 'description' => 'La clásica de la casa: doble carne de res, queso cheddar fundido.', 'price' => 14.99, 'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500'],
            ['category_id' => $getCatId('Hamburguesas'), 'name' => 'Cyber-Bacon Pro Max', 'description' => 'Hamburguesa premium con tiras de bacon crujiente.', 'price' => 15.50, 'image' => 'https://images.unsplash.com/photo-1585238341267-1cfec2046a55?w=500'],
            ['category_id' => $getCatId('Hamburguesas'), 'name' => 'Hamburguesa Glitch Veggie', 'description' => 'Medallón de garbanzos, aguacate fresco y hummus.', 'price' => 13.00, 'image' => 'https://images.unsplash.com/photo-1512152272829-e3139592d56f?w=500'],
            ['category_id' => $getCatId('Hamburguesas'), 'name' => 'La Torre Superusuario (Root)', 'description' => 'Triple carne, huevo frito, aros de cebolla.', 'price' => 18.00, 'image' => 'https://images.unsplash.com/photo-1596662951482-0c4ba74a6df6?w=500'],

            // --- HOT DOGS ---
            ['category_id' => $getCatId('Hot Dogs'), 'name' => 'Hot Dog Supersónico 5G', 'description' => 'Salchicha jumbo de 30cm y relish especial.', 'price' => 9.50, 'image' => 'https://images.unsplash.com/photo-1612392062631-94dd858cba88?w=500'],
            ['category_id' => $getCatId('Hot Dogs'), 'name' => 'Nitro Chilli Dog', 'description' => 'Chilli con carne picante y jalapeños.', 'price' => 11.00, 'image' => 'https://images.unsplash.com/photo-1619740455993-9e47519a8844?w=500'],

            // --- POLLO ---
            ['category_id' => $getCatId('Pollo'), 'name' => 'Alitas Terabyte BBQ', 'description' => '10 alitas bañadas en BBQ coreana ahumada.', 'price' => 12.99, 'image' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?w=500'],
            ['category_id' => $getCatId('Pollo'), 'name' => 'Sándwich Infinite Loop', 'description' => 'Pollo frito extra crujiente y mayonesa picante.', 'price' => 13.50, 'image' => 'https://images.unsplash.com/photo-1606755962773-53240004f14a?w=500'],

            // --- ENSALADAS ---
            ['category_id' => $getCatId('Ensaladas'), 'name' => 'Ensalada Clean Code', 'description' => 'Mix de verdes orgánicos, quinoa y arándanos.', 'price' => 10.99, 'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500'],

            // --- ACOMPAÑAMIENTOS ---
            ['category_id' => $getCatId('Acompañamientos'), 'name' => 'Papas Overclocked', 'description' => 'Papas fritas con queso fundido y bacon.', 'price' => 6.99, 'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=500'],
            ['category_id' => $getCatId('Acompañamientos'), 'name' => 'Nuggets Cuánticos', 'description' => '6 piezas de pollo crujiente.', 'price' => 7.50, 'image' => 'https://images.unsplash.com/photo-1562967914-608f82629710?w=500'],
            ['category_id' => $getCatId('Acompañamientos'), 'name' => 'Aros de Token Ring', 'description' => 'Aros de cebolla tempurizados circulares.', 'price' => 5.50, 'image' => 'https://images.unsplash.com/photo-1639024471283-03518883512d?w=500'],

            // --- BEBIDAS ---
            ['category_id' => $getCatId('Bebidas'), 'name' => 'Turbo Batido Choco-Script', 'description' => 'Chocolate belga con trozos de brownie.', 'price' => 7.25, 'image' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500'],
            ['category_id' => $getCatId('Bebidas'), 'name' => 'Soda Azul Eléctrico', 'description' => 'Infusión de arándano azul burbujeante.', 'price' => 4.50, 'image' => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?w=500'],
            ['category_id' => $getCatId('Bebidas'), 'name' => 'Cola Clásica Legacy', 'description' => 'La receta de siempre.', 'price' => 3.00, 'image' => 'https://images.unsplash.com/photo-1581006852262-e4307cf6283a?w=500'],
            ['category_id' => $getCatId('Bebidas'), 'name' => 'Nitro Cold Brew', 'description' => 'Café extraído en frío con nitrógeno.', 'price' => 6.00, 'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500'],
        ];

        foreach ($products as $product) {
            $product['stock'] = rand(5, 25);
            Product::create($product);
        }
    }
}