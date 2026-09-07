<?php

namespace Tests\Unit;

use App\Services\Analyse\ContenuLienFetcher;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContenuLienFetcherTest extends TestCase
{
    public function test_redirect_to_private_network_is_not_followed(): void
    {
        Http::fake(['*' => Http::response('', 302, ['Location' => 'http://127.0.0.1/admin'])]);

        $this->assertNull((new ContenuLienFetcher())->recuperer('https://8.8.8.8/page'));
        Http::assertSentCount(1);
    }

    public function test_announced_response_larger_than_limit_is_rejected(): void
    {
        Http::fake(['*' => Http::response('<html></html>', 200, ['Content-Length' => '500001'])]);

        $this->assertNull((new ContenuLienFetcher())->recuperer('https://8.8.8.8/page'));
    }
}
