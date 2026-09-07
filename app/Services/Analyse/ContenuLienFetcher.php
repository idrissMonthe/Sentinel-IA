<?php

namespace App\Services\Analyse;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContenuLienFetcher
{
    private const TAILLE_MAX_OCTETS = 500_000;
    private const TIMEOUT_SECONDES = 8;

    public function recuperer(string $url): ?string
    {
        if (! $this->urlEstSure($url)) {
            return null;
        }

        try {
            $reponse = $this->suivreRedirectionsSures($url);

            if ($reponse === null || $reponse->failed()) {
                return null;
            }

            $tailleAnnonce = (int) $reponse->header('Content-Length', 0);
            if ($tailleAnnonce > self::TAILLE_MAX_OCTETS) {
                return null;
            }

            return $this->extraireTexteUtile(substr($reponse->body(), 0, self::TAILLE_MAX_OCTETS));
        } catch (\Throwable $e) {
            Log::warning('Échec de récupération du lien à analyser.', ['url' => $url, 'erreur' => $e->getMessage()]);

            return null;
        }
    }

    private function suivreRedirectionsSures(string $url): ?\Illuminate\Http\Client\Response
    {
        $urlCourante = $url;

        for ($redirections = 0; $redirections <= 3; $redirections++) {
            // Chaque cible est contrôlée : une redirection ne doit pas contourner la protection SSRF.
            if (! $this->urlEstSure($urlCourante)) {
                return null;
            }

            $reponse = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; SentinelIABot/1.0)',
                ])
                ->timeout(self::TIMEOUT_SECONDES)
                ->withoutRedirecting()
                ->get($urlCourante);

            if (! $reponse->redirect()) {
                return $reponse;
            }

            $destination = $reponse->header('Location');
            if (blank($destination) || $redirections === 3) {
                return null;
            }

            $urlCourante = $this->resoudreRedirection($urlCourante, $destination);
        }

        return null;
    }

    private function resoudreRedirection(string $urlSource, string $destination): string
    {
        if (parse_url($destination, PHP_URL_SCHEME) !== null) {
            return $destination;
        }

        $source = parse_url($urlSource);
        $autorite = ($source['scheme'] ?? 'https').'://'.($source['host'] ?? '');
        if (isset($source['port'])) {
            $autorite .= ':'.$source['port'];
        }

        if (str_starts_with($destination, '//')) {
            return ($source['scheme'] ?? 'https').':'.$destination;
        }

        if (str_starts_with($destination, '/')) {
            return $autorite.$destination;
        }

        $repertoire = rtrim(dirname($source['path'] ?? '/'), '/');

        return $autorite.($repertoire === '' ? '/' : $repertoire.'/').$destination;
    }

    private function urlEstSure(string $url): bool
    {
        $parties = parse_url($url);

        if (! $parties || ! in_array($parties['scheme'] ?? '', ['http', 'https'], true) || empty($parties['host'])) {
            return false;
        }

        $host = $parties['host'];
        $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);

        // Bloque localhost, réseaux privés (10.x, 192.168.x, etc.) et réservés :
        // protection SSRF indispensable puisque l'URL vient de l'utilisateur.
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private function extraireTexteUtile(string $html): string
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        libxml_clear_errors();

        $titre = $doc->getElementsByTagName('title')->item(0)?->textContent ?? '(absent)';

        $metaDescription = '(absente)';
        foreach ($doc->getElementsByTagName('meta') as $meta) {
            if (strtolower($meta->getAttribute('name')) === 'description') {
                $metaDescription = $meta->getAttribute('content');
                break;
            }
        }

        $nombreFormulaires = $doc->getElementsByTagName('form')->length;

        $corps = preg_replace('/\s+/', ' ', trim(strip_tags($html)));
        $corps = mb_substr($corps, 0, 3000); // limite pour ne pas saturer le prompt IA

        return "Titre de la page : {$titre}\nDescription meta : {$metaDescription}\nNombre de formulaires présents : {$nombreFormulaires}\nExtrait du contenu textuel visible :\n{$corps}";
    }
}
