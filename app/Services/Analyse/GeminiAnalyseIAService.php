<?php

namespace App\Services\Analyse;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAnalyseIAService implements AnalyseIAService
{
    public function __construct(private RulesBasedAnalyseIAService $fallback)
    {
    }

    public function analyser(string $type, string $contenu): array
{
    try {
        $parts = $type === 'image'
            ? $this->construirePartsImage($contenu)
            : [['text' => $this->construirePrompt($type, $contenu)]];

        $reponse = Http::timeout(20) // un peu plus long : l'upload d'image prend plus de temps
            ->retry(1, 500)
            ->post(
                sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    config('services.gemini.model'),
                    config('services.gemini.key')
                ),
                [
                    'contents' => [['parts' => $parts]],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

        if ($reponse->failed()) {
            throw new \RuntimeException('Gemini a répondu avec le statut '.$reponse->status());
        }

        $texteBrut = $reponse->json('candidates.0.content.parts.0.text');
        $donnees = json_decode($texteBrut, associative: true, flags: JSON_THROW_ON_ERROR);

        return [(float) $donnees['score_fiabilite'], (string) $donnees['conclusion']];
    } catch (\Throwable $e) {
        Log::warning('Échec appel Gemini, bascule sur l\'analyse locale de secours.', [
            'erreur' => $e->getMessage(),
        ]);

        return $this->fallback->analyser($type, $contenu);
    }
}

// $cheminFichier : chemin absolu du fichier temporaire uploadé
private function construirePartsImage(string $cheminFichier): array
{
    $donneesImage = base64_encode(file_get_contents($cheminFichier));
    $mimeType = mime_content_type($cheminFichier) ?: 'image/jpeg';

    return [
        ['text' => $this->construirePrompt('image', '(voir image jointe)')],
        ['inline_data' => ['mime_type' => $mimeType, 'data' => $donneesImage]],
    ];
}

    private function construirePrompt(string $type, string $contenu): string
    {
        return <<<PROMPT
Tu es un module de détection d'arnaques numériques pour le Cameroun (fraude Mobile Money,
fausses offres d'emploi, loteries fictives, arnaques sentimentales).
Analyse le contenu suivant, de type "{$type}", et réponds UNIQUEMENT avec un objet JSON strict,
sans texte ni balise markdown autour, au format exact :
{"score_fiabilite": <entier entre 0 et 100, 100 = très probablement une arnaque>, "conclusion": "<2 phrases maximum, en français, expliquant le score>"}

Contenu à analyser :
"""
{$contenu}
"""
PROMPT;
    }
}