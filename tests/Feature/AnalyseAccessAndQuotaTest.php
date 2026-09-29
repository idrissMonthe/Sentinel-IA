<?php

namespace Tests\Feature;

use App\Models\Analyse;
use App\Models\User;
use App\Services\Analyse\QuotaAnalyseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnalyseAccessAndQuotaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['sentinel_ia.seuil_risque_eleve' => 70, 'sentinel_ia.seuil_risque_modere' => 40]);
    }

    public function test_configured_thresholds_control_the_button_color_and_recommendation(): void
    {
        config(['sentinel_ia.seuil_risque_eleve' => 80, 'sentinel_ia.seuil_risque_modere' => 50]);
        $user = User::factory()->create();
        $this->actingAs($user);
        foreach ([49 => 'safe', 50 => 'warning', 79 => 'warning', 80 => 'danger'] as $score => $color) {
            $analyse = Analyse::create(['user_id' => $user->id, 'type' => 'texte', 'date_analyse' => now(), 'score_fiabilite' => $score, 'conclusion' => 'Analyse de test.']);
            $response = $this->get(route('analyses.show', $analyse))->assertOk()->assertSee('score-circle '.$color, false);
            if ($score >= 80) {
                $response->assertSee('Signaler cette arnaque')->assertSee('Ce contenu présente de forts indices');
            } else {
                $response->assertDontSee('Signaler cette arnaque');
                $response->assertSee($score >= 50 ? 'Ce contenu est ambigu.' : 'Aucun indice fort détecté,');
            }
        }
    }

    public function test_successful_analysis_uses_claude_and_persists_the_result(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::preventStrayRequests();
        Http::fake(['api.anthropic.com/*' => Http::response([
            'stop_reason' => 'end_turn',
            'content' => [['type' => 'text', 'text' => '{"score_fiabilite":75,"conclusion":"Ne partagez pas votre code PIN."}']],
        ])]);
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('analyses.store'), ['type' => 'texte', 'contenu' => 'Partagez votre code PIN.'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('analyses', ['user_id' => $user->id, 'score_fiabilite' => 75, 'api_appel_effectue' => true]);
        Http::assertSentCount(1);
    }

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

    public function test_unavailable_claude_returns_a_clear_error_without_creating_an_analysis(): void
    {
        config(['services.anthropic.api_key' => null]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('analyses.store'), ['type' => 'texte', 'contenu' => 'Bonjour'])
            ->assertRedirect()
            ->assertSessionHasErrors('ia');

        $this->assertDatabaseCount('analyses', 0);
        $this->assertFalse(app(QuotaAnalyseService::class)->quotaAtteint($user->fresh()));
    }

    public function test_reporting_is_offered_only_at_the_high_risk_threshold(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        foreach ([40, 69.99, 70, 100] as $score) {
            $analyse = Analyse::create(['user_id' => $user->id, 'type' => 'texte', 'date_analyse' => now(), 'score_fiabilite' => $score, 'conclusion' => 'Analyse de test.']);
            $response = $this->get(route('analyses.show', $analyse))->assertOk()
                ->assertSee('Faire une autre analyse')->assertSee('Retour à mon historique');
            if ($score >= 70) {
                $response->assertSee('Signaler cette arnaque')->assertSee('score-circle danger', false);
            } else {
                $response->assertDontSee('Signaler cette arnaque')->assertSee('score-circle warning', false);
            }
        }
    }

    public function test_report_form_only_links_back_to_an_owned_analysis(): void
    {
        $user = User::factory()->create();
        $analyse = Analyse::create(['user_id' => $user->id, 'type' => 'texte', 'date_analyse' => now(), 'score_fiabilite' => 75, 'conclusion' => 'Analyse de test.']);
        $url = route('signalements.create', ['analyse_id' => $analyse->id]);
        $this->actingAs($user)->get($url)->assertOk()->assertSee('Retour au résultat de l’analyse');
        $this->actingAs(User::factory()->create())->get($url)->assertOk()->assertDontSee('Retour au résultat de l’analyse');
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
