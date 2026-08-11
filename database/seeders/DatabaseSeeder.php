<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ordre imposé par les clés étrangères, identique à celui des migrations
        $this->call([
            UserSeeder::class,
            EntiteSuspecteSeeder::class,
            SignalementSeeder::class,
            AlerteSeeder::class,
        ]);
    }
}