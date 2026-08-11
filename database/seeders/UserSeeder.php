<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Comptes fixes, faciles à retenir pour la démo/soutenance
        User::factory()->administrateur()->create([
            'nom' => 'Monthe', 'prenom' => 'Idriss',
            'email' => 'admin@sentinelia.test',
        ]);

        User::factory()->moderateur()->create([
            'nom' => 'Ngono', 'prenom' => 'Paul',
            'email' => 'moderateur@sentinelia.test',
        ]);

        User::factory()->create([
            'nom' => 'Fotso', 'prenom' => 'Aline',
            'email' => 'utilisateur@sentinelia.test',
        ]);

        // Utilisateurs aléatoires pour peupler la base (déclarants des signalements)
        User::factory()->count(15)->create();
    }
}