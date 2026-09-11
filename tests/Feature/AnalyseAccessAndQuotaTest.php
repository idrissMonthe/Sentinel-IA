<?php

namespace Tests\Feature;

use App\Models\Analyse;
use App\Models\User;
use App\Services\Analyse\QuotaAnalyseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyseAccessAndQuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_another_users_analysis(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $analyse = Analyse::create([
            'user_id' => $owner->id,
            'type' => 'texte',
            'date_analyse' => now(),
            'score_fiabilite' => 50,
            'conclusion' => 'Analyse de test.',
        ]);

        $this->actingAs($otherUser)
            ->get(route('analyses.show', $analyse))
            ->assertForbidden();
    }

    public function test_daily_quota_blocks_a_sixth_analysis_before_an_api_call(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            Analyse::create([
                'user_id' => $user->id,
                'type' => 'texte',
                'date_analyse' => now(),
                'score_fiabilite' => 50,
                'conclusion' => 'Analyse de test.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->actingAs($user)
            ->post(route('analyses.store'), ['type' => 'texte', 'contenu' => 'Bonjour'])
            ->assertSessionHasErrors('type');
    }

    public function test_unavailable_openai_returns_a_clear_error_without_creating_an_analysis(): void
    {
        config(['services.openai.api_key' => null]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('analyses.store'), ['type' => 'texte', 'contenu' => 'Bonjour'])
            ->assertRedirect()
            ->assertSessionHasErrors('ia');

        $this->assertDatabaseCount('analyses', 0);
        $this->assertFalse(app(QuotaAnalyseService::class)->quotaAtteint($user->fresh()));
    }

    public function test_zero_score_does_not_show_the_report_button(): void
    {
        $user = User::factory()->create();
        $analyse = Analyse::create([
            'user_id' => $user->id,
            'type' => 'texte',
            'date_analyse' => now(),
            'score_fiabilite' => 0,
            'conclusion' => 'Aucun indice fort détecté.',
        ]);

        $this->actingAs($user)
            ->get(route('analyses.show', $analyse))
            ->assertOk()
            ->assertDontSee('Signaler cette arnaque');
    }
}
