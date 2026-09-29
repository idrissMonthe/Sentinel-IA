<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ConsommationIA;
use App\Models\User;
use App\Services\Analyse\AnalyseIAIndisponibleException;
use App\Services\Analyse\ClaudeAnalyseIAService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TokenUsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_cache_and_all_usage_categories_are_recorded_even_for_a_truncated_response(): void
    {
        config(['services.anthropic.api_key' => 'test-key', 'services.anthropic.prompt_caching' => true]);
        Http::fake(['api.anthropic.com/*' => Http::response([
            'model' => 'claude-haiku-4-5-20251001', 'stop_reason' => 'max_tokens', 'content' => [],
            'usage' => ['input_tokens' => 10, 'output_tokens' => 20, 'cache_creation_input_tokens' => 4096, 'cache_read_input_tokens' => 200],
        ])]);
        try {
            (new ClaudeAnalyseIAService)->analyser('texte', 'Test');
            $this->fail('La réponse tronquée doit être refusée.');
        } catch (AnalyseIAIndisponibleException) {
            $this->assertDatabaseHas('consommations_ia', ['operation' => 'analyse', 'input_tokens' => 10, 'output_tokens' => 20, 'cache_creation_input_tokens' => 4096, 'cache_read_input_tokens' => 200]);
        }
        Http::assertSent(fn ($request) => $request->data()['cache_control'] === ['type' => 'ephemeral']);
        $this->assertSame(0, Artisan::call('sentinel:tokens', ['--days' => 1]));
        $this->assertStringContainsString('4326', Artisan::output());
    }

    public function test_tracking_reformulation_works_with_cache_disabled(): void
    {
        config(['services.anthropic.api_key' => 'test-key', 'services.anthropic.prompt_caching' => false]);
        Http::fake(['api.anthropic.com/*' => Http::response(['stop_reason' => 'end_turn', 'content' => [['type' => 'text', 'text' => 'Demande de paiement reçue.']], 'usage' => ['input_tokens' => 100, 'output_tokens' => 15]])]);
        (new ClaudeAnalyseIAService)->ameliorerRedaction('texte', 'Demande paiement');
        Http::assertSent(fn ($request) => ! isset($request->data()['cache_control']));
        $this->assertDatabaseHas('consommations_ia', ['operation' => 'reformulation', 'input_tokens' => 100, 'cache_read_input_tokens' => 0]);
    }

    public function test_token_dashboard_is_reserved_for_admins(): void
    {
        $this->get('/admin/consommation-ia')->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get('/admin/consommation-ia')->assertForbidden();
        $admin = User::factory()->create(['role' => UserRole::ADMINISTRATEUR]);
        ConsommationIA::create(['operation' => 'analyse', 'modele' => 'claude-haiku-4-5-20251001', 'input_tokens' => 321]);
        $this->actingAs($admin)->get('/admin/consommation-ia')->assertOk()->assertSee('321')->assertSee('Consommation IA');
    }

    public function test_daily_report_excludes_old_calls_and_rejects_invalid_periods(): void
    {
        ConsommationIA::forceCreate(['operation' => 'analyse', 'modele' => 'ancien-modele', 'input_tokens' => 999, 'created_at' => now()->subDays(10)]);
        Artisan::call('sentinel:tokens', ['--days' => 1]);
        $this->assertStringNotContainsString('ancien-modele', Artisan::output());
        $this->artisan('sentinel:tokens --days=0')->assertFailed();
    }
}
