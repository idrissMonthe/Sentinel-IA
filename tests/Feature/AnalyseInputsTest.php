<?php

namespace Tests\Feature;

use App\Models\Analyse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnalyseInputsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['services.anthropic.api_key' => 'test-key']);
    }

    private function aiResponse()
    {
        return Http::response(['stop_reason' => 'end_turn', 'content' => [['type' => 'text', 'text' => '{"score_fiabilite":65,"conclusion":"Vérifiez le destinataire du paiement."}']]]);
    }

    public function test_image_with_stale_text_is_sent_as_an_image_only(): void
    {
        Http::fake(['api.anthropic.com/*' => $this->aiResponse()]);
        $image = UploadedFile::fake()->createWithContent('capture.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScL0XQAAAABJRU5ErkJggg=='));
        $this->actingAs(User::factory()->create())->post(route('analyses.store'), ['type' => 'image', 'contenu' => 'Ancien texte à ignorer', 'fichier' => $image])
            ->assertSessionHasNoErrors()->assertRedirect();
        Http::assertSent(fn ($request) => $request->data()['messages'][0]['content'][1]['type'] === 'image'
            && ! str_contains(json_encode($request->data()), 'Ancien texte'));
        $this->assertDatabaseCount('analyses', 1);
    }

    public function test_text_path_cannot_replace_an_image_upload(): void
    {
        Http::fake();
        $this->actingAs(User::factory()->create())->post(route('analyses.store'), ['type' => 'image', 'contenu' => '/etc/passwd'])
            ->assertSessionHasErrors('fichier');
        Http::assertNothingSent();
    }

    public function test_link_content_reaches_claude_and_is_identified_in_the_report(): void
    {
        Http::fake(['https://8.8.8.8/' => Http::response('<h1>Boutique test</h1><p>Envoyez votre code PIN.</p>'), 'api.anthropic.com/*' => $this->aiResponse()]);
        $this->actingAs(User::factory()->create())->post(route('analyses.store'), ['type' => 'lien', 'contenu' => '8.8.8.8/'])
            ->assertSessionHasNoErrors()->assertRedirect();
        Http::assertSent(fn ($request) => $request->url() === 'https://api.anthropic.com/v1/messages'
            && str_contains($request->data()['messages'][0]['content'], 'Envoyez votre code PIN.'));
        $analyse = Analyse::firstOrFail();
        $this->assertSame('https://8.8.8.8/', $analyse->source_web['url_finale']);
        $this->get(route('analyses.show', $analyse))->assertOk()->assertSee('Source utilisée')->assertSee('Score de risque');
    }

    public function test_failed_fetch_does_not_spend_tokens_or_create_a_domain_only_analysis(): void
    {
        Http::fake(['https://8.8.8.8/' => Http::response('', 403)]);
        $this->actingAs(User::factory()->create())->post(route('analyses.store'), ['type' => 'lien', 'contenu' => '8.8.8.8/'])
            ->assertSessionHasErrors('contenu');
        Http::assertSentCount(1);
        $this->assertDatabaseCount('analyses', 0);
    }
}
