<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Alerte;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlerteSeeder extends Seeder
{
    public function run(): void
    {
        $moderateur = User::where('role', UserRole::MODERATEUR)->first();

        $alertes = [
            [
                'titre' => 'Recrudescence des faux agents Mobile Money',
                'contenu' => 'Plusieurs signalements récents concernant des individus se faisant passer pour des agents MTN/Orange Money et demandant des codes secrets. Ne communiquez jamais votre code secret à quiconque.',
                'est_publiee' => true,
            ],
            [
                'titre' => 'Fausses offres de bourses à l\'étranger',
                'contenu' => 'Attention aux offres de bourses demandant un paiement de "frais de dossier" par avance. Les établissements sérieux ne procèdent jamais ainsi.',
                'est_publiee' => true,
            ],
            [
                'titre' => 'Brouillon en cours de rédaction',
                'contenu' => 'Alerte en préparation sur une nouvelle vague de faux liens de loterie.',
                'est_publiee' => false, // démontre le cas "brouillon" du diagramme d'activité
            ],
        ];

        foreach ($alertes as $alerte) {
            Alerte::create([...$alerte, 'moderateur_id' => $moderateur->id]);
        }
    }
}