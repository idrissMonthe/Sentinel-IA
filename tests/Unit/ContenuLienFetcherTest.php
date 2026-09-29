<?php

namespace Tests\Unit;

use App\Services\Analyse\ContenuLienFetcher;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContenuLienFetcherTest extends TestCase
{
    public function test_bare_address_is_fetched_as_https_and_scripts_are_excluded(): void
    {
        Http::preventStrayRequests();
        Http::fake(['https://8.8.8.8/page' => Http::response('<html><head><title>Boutique</title><style>secret-css</style></head><body><h1>Bienvenue</h1><p>Paiement Mobile Money</p><script>secret-script</script></body></html>', 200, ['Content-Type' => 'text/html'])]);
        $fetcher = new ContenuLienFetcher;
        $text = $fetcher->recuperer('8.8.8.8/page');
        $this->assertStringContainsString('Paiement Mobile Money', $text);
        $this->assertStringNotContainsString('secret-script', $text);
        $this->assertStringNotContainsString('secret-css', $text);
        $this->assertSame('https://8.8.8.8/page', $fetcher->source()['url_finale']);
        Http::assertSentCount(1);
    }

    public function test_relative_redirect_is_resolved_and_final_url_is_recorded(): void
    {
        Http::fake([
            'https://8.8.8.8/folder/page' => Http::response('', 302, ['Location' => '../home']),
            'https://8.8.8.8/home' => Http::response('<p>Contenu réel</p>'),
        ]);
        $fetcher = new ContenuLienFetcher;
        $this->assertNotNull($fetcher->recuperer('https://8.8.8.8/folder/page'));
        $this->assertSame('https://8.8.8.8/home', $fetcher->source()['url_finale']);
    }

    public function test_private_addresses_and_credentials_are_never_requested(): void
    {
        Http::fake();
        foreach (['http://127.0.0.1', 'http://192.168.1.1', 'http://[::1]', 'http://169.254.169.254', 'https://user:pass@8.8.8.8', 'ftp://8.8.8.8'] as $url) {
            $this->assertNull((new ContenuLienFetcher)->recuperer($url));
        }
        Http::assertNothingSent();
    }

    public function test_script_only_and_non_text_responses_are_not_analyzed(): void
    {
        Http::fake(['https://8.8.8.8/js' => Http::response('<script>app()</script>'), 'https://8.8.8.8/pdf' => Http::response('%PDF', 200, ['Content-Type' => 'application/pdf'])]);
        $this->assertNull((new ContenuLienFetcher)->recuperer('https://8.8.8.8/js'));
        $this->assertNull((new ContenuLienFetcher)->recuperer('https://8.8.8.8/pdf'));
    }

    public function test_oversize_body_without_content_length_is_rejected(): void
    {
        Http::fake(['*' => Http::response(str_repeat('a', 500001))]);
        $this->assertNull((new ContenuLienFetcher)->recuperer('https://8.8.8.8'));
    }

    public function test_redirect_to_private_network_is_not_followed(): void
    {
        Http::fake(['*' => Http::response('', 302, ['Location' => 'http://127.0.0.1/admin'])]);

        $this->assertNull((new ContenuLienFetcher)->recuperer('https://8.8.8.8/page'));
        Http::assertSentCount(1);
    }

    public function test_announced_response_larger_than_limit_is_rejected(): void
    {
        Http::fake(['*' => Http::response('<html></html>', 200, ['Content-Length' => '500001'])]);

        $this->assertNull((new ContenuLienFetcher)->recuperer('https://8.8.8.8/page'));
    }
}
