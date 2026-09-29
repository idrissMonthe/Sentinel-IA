<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ClaudeDiagnosticTest extends TestCase
{
    public function test_workspace_requirement_is_reported_without_exposing_the_key(): void
    {
        config(['services.anthropic.api_key' => 'test-key', 'services.anthropic.workspace_id' => null]);
        Http::fake(['api.anthropic.com/*' => Http::response(['error' => ['type' => 'invalid_request_error', 'message' => 'Include anthropic-workspace-id']], 400)]);
        $this->artisan('sentinel:claude-check --live')
            ->expectsOutput('Cette clé exige ANTHROPIC_WORKSPACE_ID dans .env (console Anthropic > Settings > Workspaces).')
            ->assertFailed();
        Http::assertSentCount(1);
    }

    public function test_empty_503_reports_temporary_unavailability_without_claiming_an_auth_error(): void
    {
        config(['services.anthropic.api_key' => 'test-key', 'services.anthropic.retry_delay_ms' => 0, 'services.anthropic.retry_attempts' => 2]);
        Http::fake(['api.anthropic.com/*' => Http::response('', 503)]);
        $this->artisan('sentinel:claude-check --live')
            ->expectsOutput('HTTP 503 : Service distant temporairement indisponible. Réessayez dans une minute et consultez https://status.claude.com.')
            ->expectsOutput('Réponse sans code d’erreur Anthropic : l’origine exacte (API ou intermédiaire réseau) reste indéterminée.')
            ->assertFailed();
        Http::assertSentCount(2);
    }

    public function test_overload_reports_the_provider_request_id(): void
    {
        config(['services.anthropic.api_key' => 'test-key', 'services.anthropic.retry_delay_ms' => 0, 'services.anthropic.retry_attempts' => 2]);
        Http::fake(['api.anthropic.com/*' => Http::response(['error' => ['type' => 'overloaded_error']], 529, ['request-id' => 'req_test'])]);
        $this->artisan('sentinel:claude-check --live')
            ->expectsOutput('HTTP 529 : API Claude temporairement surchargée. Réessayez dans une minute.')
            ->expectsOutput('Identifiant de requête : req_test')
            ->assertFailed();
        Http::assertSentCount(2);
    }

    public function test_credential_validation_failure_is_not_reported_as_generic_overload(): void
    {
        config(['services.anthropic.api_key' => 'test-key', 'services.anthropic.retry_attempts' => 1]);
        Http::fake(['api.anthropic.com/*' => Http::response('credential validation failed', 503)]);
        $this->artisan('sentinel:claude-check --live')
            ->expectsOutput('HTTP 503 : le service distant signale « credential validation failed ».')
            ->assertFailed();
        Http::assertSentCount(1);
    }

    public function test_missing_key_is_reported_without_network_access(): void
    {
        config(['services.anthropic.api_key' => null]);
        Http::preventStrayRequests();
        $this->artisan('sentinel:claude-check')->assertFailed();
        Http::assertNothingSent();
    }

    public function test_config_check_does_not_call_claude(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::preventStrayRequests();
        $this->artisan('sentinel:claude-check')->assertSuccessful();
        Http::assertNothingSent();
    }

    public function test_live_check_validates_the_application_response_contract(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::fake(['api.anthropic.com/*' => Http::response(['stop_reason' => 'end_turn', 'content' => [['type' => 'text', 'text' => '{"score_fiabilite":90,"conclusion":"Ne communiquez jamais votre code PIN."}']]])]);
        $this->artisan('sentinel:claude-check --live')->expectsOutput('Claude opérationnel : réponse reçue et format validé.')->assertSuccessful();
        Http::assertSentCount(1);
    }

    public function test_invalid_key_has_an_actionable_error_and_is_not_retried(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::fake(['api.anthropic.com/*' => Http::response(['error' => ['type' => 'authentication_error', 'message' => 'Secret test-key']], 401)]);
        $this->artisan('sentinel:claude-check --live')->expectsOutput('HTTP 401 : Clé invalide ou révoquée : vérifiez la clé du projet Claude.')->assertFailed();
        Http::assertSentCount(1);
    }
}
