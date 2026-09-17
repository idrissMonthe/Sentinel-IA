<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visiteur_peut_creer_un_compte(): void
    {
        $reponse = $this->post('/inscription', [
            'nom' => 'Monthe',
            'prenom' => 'Idriss',
            'email' => 'test@example.com',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ]);

        $reponse->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertGuest();
    }

    public function test_inscription_accepte_un_mot_de_passe_de_8_chiffres(): void
    {
        $reponse = $this->post('/inscription', [
            'nom' => 'Monthe',
            'prenom' => 'Idriss',
            'email' => 'test@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $reponse->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_connexion_avec_bons_identifiants_reussit(): void
    {
        $user = User::factory()->create(['password' => 'motdepasse123']);

        $reponse = $this->post('/connexion', [
            'email' => $user->email,
            'password' => 'motdepasse123',
        ]);

        $reponse->assertRedirect(route('accueil'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_compte_bloque_ne_peut_pas_se_connecter(): void
    {
        $user = User::factory()->create([
            'password' => 'motdepasse123',
            'statut' => 'bloque',
        ]);

        $reponse = $this->post('/connexion', [
            'email' => $user->email,
            'password' => 'motdepasse123',
        ]);

        $reponse->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_5_tentatives_echouees_bloquent_automatiquement_le_compte(): void
    {
        $user = User::factory()->create(['password' => 'motdepasse123']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/connexion', ['email' => $user->email, 'password' => 'mauvais']);
        }

        $this->assertSame('bloque', $user->fresh()->statut);
    }
}
