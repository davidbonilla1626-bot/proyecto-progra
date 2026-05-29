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
    // Crear un usuario de prueba para entrar siempre con los mismos datos
    \App\Models\User::factory()->create([
        'name' => 'David Bonilla',
        'email' => 'davidbonilla1626@gmail.com',
        'password' => bcrypt('password123'), // Contraseña del cliente de prueba
        'role' => 'user',
    ]);

    // Crear el usuario administrador solicitado
    \App\Models\User::factory()->create([
        'name' => 'Administrador QuickBite',
        'email' => 'admin@quickbite.com',
        'password' => bcrypt('restaurante2026'), // Clave requerida para el administrador
        'role' => 'admin',
    ]);

    // Llamar a tus otros seeders
    $this->call([
        CategorySeeder::class,
        ProductSeeder::class,
    ]);

      
  }
}
    
