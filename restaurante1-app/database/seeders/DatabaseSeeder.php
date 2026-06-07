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
      // Llamar a los seeders de categorías y productos solamente (sin crear cuentas predefinidas)
      $this->call([
          CategorySeeder::class,
          ProductSeeder::class,
      ]);
  }
}
