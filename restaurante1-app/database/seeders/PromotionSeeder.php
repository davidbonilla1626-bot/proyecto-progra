<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Promoción porcentual del 10%
        Promotion::updateOrCreate(
            ['code' => 'PROMO10'],
            [
                'type' => 'percent',
                'value' => 10.00,
                'expires_at' => now()->addMonths(6),
            ]
        );

        // Promoción fija de $5.00
        Promotion::updateOrCreate(
            ['code' => 'BIENVENIDO'],
            [
                'type' => 'fixed',
                'value' => 5.00,
                'expires_at' => now()->addMonths(6),
            ]
        );

        // Promoción porcentual del 20%
        Promotion::updateOrCreate(
            ['code' => 'DESCUENTO20'],
            [
                'type' => 'percent',
                'value' => 20.00,
                'expires_at' => now()->addMonths(6),
            ]
        );
    }
}
