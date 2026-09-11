<?php

namespace Tests\Feature;

use App\Enums\StatutSignalement;
use App\Enums\UserRole;
use App\Models\EntiteSuspecte;
use App\Models\Signalement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_signalement_is_validated_only_once(): void
    {
        $moderateur = User::factory()->create(['role' => UserRole::MODERATEUR]);
        $declarant = User::factory()->create();
        $entite = EntiteSuspecte::create([
            'type' => 'email',
            'valeur' => 'suspect@example.com',
        ]);
        $signalement = Signalement::create([
            'user_id' => $declarant->id,
            'entite_suspecte_id' => $entite->id,
            'description' => 'Description de test',
            'statut' => StatutSignalement::EN_ATTENTE,
        ]);

        $this->actingAs($moderateur)
            ->patch(route('moderation.valider', $signalement))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('signalements', [
            'id' => $signalement->id,
            'statut' => StatutSignalement::VALIDE->value,
        ]);
        $this->assertDatabaseHas('entite_suspectes', [
            'id' => $entite->id,
            'nombre_signalement' => 1,
        ]);

        $this->actingAs($moderateur)
            ->patch(route('moderation.valider', $signalement))
            ->assertSessionHasErrors('moderation');

        $this->assertDatabaseHas('entite_suspectes', [
            'id' => $entite->id,
            'nombre_signalement' => 1,
        ]);

        $this->actingAs($moderateur)
            ->patch(route('moderation.rejeter', $signalement))
            ->assertSessionHasErrors('moderation');
    }
}
