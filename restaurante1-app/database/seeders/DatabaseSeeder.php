<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
  public function run(): void
  {
      // Llamar a los seeders de categorías, productos y promociones
      $this->call([
          CategorySeeder::class,
          ProductSeeder::class,
          PromotionSeeder::class,
      ]);

      // Seed default opening and closing times
      \App\Models\Setting::updateOrCreate(['key' => 'opening_time'], ['value' => '08:00']);
      \App\Models\Setting::updateOrCreate(['key' => 'closing_time'], ['value' => '22:00']);

      // Crear cuentas de prueba predefinidas
      \App\Models\User::updateOrCreate(
          ['email' => 'admin@quickbite.com'],
          [
              'name' => 'Admin QuickBite',
              'password' => \Illuminate\Support\Facades\Hash::make('password'),
              'role' => 'admin'
          ]
      );

      \App\Models\User::updateOrCreate(
          ['email' => 'cocina@quickbite.com'],
          [
              'name' => 'Chef QuickBite',
              'password' => \Illuminate\Support\Facades\Hash::make('password'),
              'role' => 'employee'
          ]
      );

      \App\Models\User::updateOrCreate(
          ['email' => 'cliente@quickbite.com'],
          [
              'name' => 'Cliente Frecuente',
              'password' => \Illuminate\Support\Facades\Hash::make('password'),
              'role' => 'user'
          ]
      );
  }
}
