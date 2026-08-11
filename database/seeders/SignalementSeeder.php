<?php

namespace Database\Seeders;

use App\Enums\StatutSignalement;
use App\Enums\UserRole;
use App\Models\EntiteSuspecte;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Database\Seeder;

class SignalementSeeder extends Seeder
{
    public function run(): void
    {
        $declarants = User::where('role', UserRole::UTILISATEUR)->get();
        $moderateur = User::where('role', UserRole::MODERATEUR)->first();

        $descriptions = [
            'On m\'a contacté en me disant que j\'avais gagné une bourse et qu\'il fallait payer des frais de dossier via Mobile Money.',
            'La personne se faisait passer pour un agent MTN et me demandait mon code secret pour "débloquer" mon compte.',
            'Offre d\'emploi trop belle pour être vraie, demande un paiement de "frais de formation" avant tout entretien.',
            'Message annonçant un gain à une loterie à laquelle je n\'ai jamais participé.',
            'Lien reçu par SMS prétendant offrir un bonus Mobile Money, redirige vers un faux formulaire bancaire.',
        ];

        EntiteSuspecte::all()->each(function (EntiteSuspecte $entite) use ($declarants, $moderateur, $descriptions) {
            // 1 à 3 signalements par entité, à des statuts variés pour une démo réaliste
            $nombre = random_int(1, 3);

            for ($i = 0; $i < $nombre; $i++) {
                $statut = fake()->randomElement([
                    StatutSignalement::VALIDE,
                    StatutSignalement::VALIDE,
                    StatutSignalement::EN_ATTENTE,
                    StatutSignalement::REJETE,
                ]);

                Signalement::create([
                    'user_id' => $declarants->random()->id,
                    'moderateur_id' => $statut !== StatutSignalement::EN_ATTENTE ? $moderateur->id : null,
                    'entite_suspecte_id' => $entite->id,
                    'analyse_id' => null, // pas d'analyse IA réelle générée pour les données de seed
                    'description' => fake()->randomElement($descriptions),
                    'ville' => fake()->randomElement(['Douala', 'Yaoundé', 'Bafoussam', 'Garoua', 'Bamenda']),
                    'statut' => $statut,
                ]);
            }
        });
    }
}