<?php

namespace App\Services\Analyse;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContenuLienFetcher
{
    private const TAILLE_MAX_OCTETS = 500_000;

    private const TEXTE_MAX_CARACTERES = 8000;

    private ?array $source = null;

    public function source(): ?array
    {
        return $this->source;
    }

    public function recuperer(string $url): ?string
    {
        $this->source = null;
        $url = trim($url);
        if (parse_url($url, PHP_URL_SCHEME) === null) {
            $url = 'https://'.ltrim($url, '/');
        }
        try {
            $courante = $url;
            for ($redirections = 0; $redirections <= 3; $redirections++) {
                $options = $this->optionsSures($courante);
                if ($options === null) {
                    return null;
                }
                $reponse = Http::withHeaders(['User-Agent' => 'SentinelIABot/1.0', 'Accept' => 'text/html, text/plain'])
                    ->withOptions($options + [
                        'verify' => config('sentinel_ia.web_ca_bundle') ?: true,
                        'on_headers' => function ($response) {
                            if ((int) $response->getHeaderLine('Content-Length') > self::TAILLE_MAX_OCTETS) {
                                throw new \RuntimeException('Page trop volumineuse.');
                            }
                        },
                        'progress' => function ($total, $downloaded) {
                            if ($downloaded > self::TAILLE_MAX_OCTETS) {
                                throw new \RuntimeException('Page trop volumineuse.');
                            }
                        },
                    ])->connectTimeout(5)->timeout(12)->withoutRedirecting()->get($courante);

                if ($reponse->redirect()) {
                    if (blank($reponse->header('Location')) || $redirections === 3) {
                        return null;
                    }
                    $courante = (string) UriResolver::resolve(new Uri($courante), new Uri($reponse->header('Location')));

                    continue;
                }
                if ($reponse->failed() || (int) $reponse->header('Content-Length') > self::TAILLE_MAX_OCTETS || strlen($reponse->body()) > self::TAILLE_MAX_OCTETS) {
                    return null;
                }
                $mime = strtolower(explode(';', $reponse->header('Content-Type'))[0]);
                if (! in_array($mime, ['', 'text/html', 'application/xhtml+xml', 'text/plain'], true)) {
                    return null;
                }
                $texte = $this->extraireTexteUtile($reponse->body(), $mime === 'text/plain');
                if ($texte === null) {
                    return null;
                }
                $this->source = ['url' => $url, 'url_finale' => $courante, 'caracteres' => mb_strlen($texte), 'limite_caracteres' => self::TEXTE_MAX_CARACTERES];

                return "Page effectivement récupérée : {$courante}\n{$texte}";
            }
        } catch (\Throwable $e) {
            // Ne pas conserver les paramètres d'URL, qui peuvent être confidentiels.
            Log::warning('Échec de récupération du lien à analyser.', ['host' => parse_url($url, PHP_URL_HOST), 'classe' => $e::class]);
        }

        return null;
    }

    private function optionsSures(string $url): ?array
    {
        $parts = parse_url($url);
        if (! $parts || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true) || empty($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }
        $host = trim($parts['host'], '[]');
        $port = $parts['port'] ?? ($parts['scheme'] === 'https' ? 443 : 80);
        if (! in_array($port, [80, 443], true)) {
            return null;
        }
        $literal = filter_var($host, FILTER_VALIDATE_IP);
        $ips = $literal ? [$host] : gethostbynamel($host);
        if (! $ips) {
            return null;
        }
        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return null;
            }
        }
        // Fixer l'IP contrôlée évite une nouvelle résolution DNS entre contrôle et connexion.
        if (! $literal) {
            if (! defined('CURLOPT_RESOLVE')) {
                return null;
            }

            return ['curl' => [CURLOPT_RESOLVE => ["{$host}:{$port}:{$ips[0]}"]]];
        }

        return [];
    }

    private function extraireTexteUtile(string $html, bool $plain): ?string
    {
        if (trim($html) === '') {
            return null;
        }
        $titre = '';
        $description = '';
        $formulaires = 0;
        if ($plain) {
            $body = $html;
        } else {
            $previous = libxml_use_internal_errors(true);
            try {
                $doc = new \DOMDocument;
                $doc->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NONET);
                $xpath = new \DOMXPath($doc);
                $titre = $doc->getElementsByTagName('title')->item(0)?->textContent ?? '';
                foreach ($doc->getElementsByTagName('meta') as $meta) {
                    if (strtolower($meta->getAttribute('name')) === 'description') {
                        $description = $meta->getAttribute('content');
                        break;
                    }
                }
                $formulaires = $doc->getElementsByTagName('form')->length;
                foreach ($xpath->query('//script|//style|//noscript|//template|//*[@hidden]|//*[@aria-hidden="true"]') as $node) {
                    $node->parentNode?->removeChild($node);
                }
                // Préserver les séparations entre paragraphes et cellules du HTML.
                foreach ($xpath->query('//p|//div|//li|//br|//h1|//h2|//h3|//td') as $node) {
                    $node->appendChild($doc->createTextNode(' '));
                }
                $body = $doc->getElementsByTagName('body')->item(0)?->textContent ?? '';
            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previous);
            }
        }
        $body = trim(preg_replace('/\s+/u', ' ', $body) ?? '');
        if ($body === '') {
            return null;
        }
        $excerpt = mb_substr($body, 0, self::TEXTE_MAX_CARACTERES);

        return 'Titre : '.mb_substr($titre, 0, 300)."\nDescription : ".mb_substr($description, 0, 600)
            ."\nFormulaires : {$formulaires}\nTexte extrait du HTML (JavaScript non exécuté) :\n{$excerpt}"
            .(mb_strlen($body) > self::TEXTE_MAX_CARACTERES ? "\n[Extrait tronqué]" : '');
    }
}
