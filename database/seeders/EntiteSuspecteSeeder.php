<?php

namespace Database\Seeders;

use App\Models\EntiteSuspecte;
use Illuminate\Database\Seeder;

class EntiteSuspecteSeeder extends Seeder
{
    public function run(): void
    {
        // Données réalistes ancrées dans les arnaques fréquentes au Cameroun
        // (Mobile Money, fausses offres d'emploi, loteries fictives), pas des exemples génériques.
        $entites = [
            ['type' => 'numero', 'valeur' => '677123456', 'nombre_signalement' => 12],
            ['type' => 'numero', 'valeur' => '699887766', 'nombre_signalement' => 8],
            ['type' => 'numero', 'valeur' => '655001122', 'nombre_signalement' => 3],
            ['type' => 'email', 'valeur' => 'recrutement.mtn-cm@gmail.com', 'nombre_signalement' => 6],
            ['type' => 'email', 'valeur' => 'loterie-orange-cameroun@yahoo.fr', 'nombre_signalement' => 9],
            ['type' => 'lien', 'valeur' => 'https://mtn-mobilemoney-bonus.tk', 'nombre_signalement' => 15],
            ['type' => 'lien', 'valeur' => 'https://orange-cm-cadeaux.ga', 'nombre_signalement' => 4],
            ['type' => 'numero', 'valeur' => '620334455', 'nombre_signalement' => 1],
        ];

        foreach ($entites as $entite) {
            EntiteSuspecte::create($entite);
        }
    }
}