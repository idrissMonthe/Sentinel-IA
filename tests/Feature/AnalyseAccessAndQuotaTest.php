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

    public function test_local_fallback_without_an_openai_response_does_not_consume_quota(): void
    {
        config(['services.openai.api_key' => null]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('analyses.store'), ['type' => 'texte', 'contenu' => 'Bonjour'])
            ->assertRedirect();

        $analyse = $user->fresh()->analyses()->firstOrFail();

        $this->assertFalse($analyse->api_appel_effectue);
        $this->assertFalse(app(QuotaAnalyseService::class)->quotaAtteint($user->fresh()));
    }
}
