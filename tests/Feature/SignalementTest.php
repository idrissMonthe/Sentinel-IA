<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Signalement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignalementTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_ne_peut_pas_creer_un_signalement(): void
    {
        $response = $this->post('/signalements', [
            'type' => 'email',
            'valeur' => 'arnaque@example.com',
            'description' => 'Tentative de fraude par email.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('signalements', 0);
    }

    public function test_un_utilisateur_connecte_peut_signaler_une_entite_suspecte(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/signalements', [
            'type' => 'email',
            'valeur' => 'arnaque@example.com',
            'description' => 'Cette adresse demande un paiement urgent par transfert.',
            'ville' => 'Douala',
        ]);

        $signalement = Signalement::firstOrFail();

        $response->assertRedirect(route('signalements.show', $signalement));
        $this->assertDatabaseHas('entite_suspectes', [
            'type' => 'email',
            'valeur' => 'arnaque@example.com',
        ]);
        $this->assertDatabaseHas('signalements', [
            'user_id' => $user->id,
            'description' => 'Cette adresse demande un paiement urgent par transfert.',
            'ville' => 'Douala',
        ]);
    }

    public function test_deux_signalements_identiques_reutilisent_la_meme_entite_suspecte(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $donnees = [
            'type' => 'numero',
            'valeur' => '699123456',
            'description' => 'Numéro utilisé pour demander un paiement.',
        ];

        $this->actingAs($user)->post('/signalements', $donnees);
        $this->actingAs($user)->post('/signalements', $donnees);

        $this->assertDatabaseCount('entite_suspectes', 1);
        $this->assertDatabaseCount('signalements', 2);
    }
}
