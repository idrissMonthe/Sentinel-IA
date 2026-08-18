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

        $reponse = Http::withoutVerifying() // un peu plus long : l'upload d'image prend plus de temps
            ->timeout(20)
            ->retry(1, 500)
            ->post(
                sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    env('GEMINI_MODEL','gemini-3.6-flash'),
                    env('GEMINI_API_KEY')
                ),
                [
                    'contents' => [['parts' => $parts]],
                    'generationConfig' => [
                        'temperature' => 0.3,
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
{"score_fiabilite": <entier entre 0 et 100, 100 = très probablement une arnaque>, "conclusion": "<3 phrases maximum, en français, expliquant le score et en donnant des mesures à suivre pour ne pas se faire arnaquer en rapport avec le type de l'arnaque>"}

Contenu à analyser :
"""
{$contenu}
"""
PROMPT;
    }
    public function ameliorerRedaction(string $type, string $contenuBrut): string
{
    try {
        $reponse = Http::timeout(15)
            ->retry(1, 500)
            ->post(
                sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    env('GEMINI_MODEL','gemini-3.6-flash'),
                    env('GEMINI_API_KEY')
                ),
                [
                    'contents' => [
                        ['parts' => [['text' => $this->construirePromptRedaction($type, $contenuBrut)]]],
                    ],
                    'generationConfig' => ['temperature' => 0.3],
                    // Pas de responseMimeType JSON ici : contrairement à analyser(), on veut
                    // du texte brut directement exploitable, pas une structure à parser.
                ]
            );

        if ($reponse->failed()) {
            throw new \RuntimeException('Gemini a répondu avec le statut '.$reponse->status());
        }

        $texte = $reponse->json('candidates.0.content.parts.0.text');

        if (blank($texte)) {
            throw new \RuntimeException('Réponse Gemini vide.');
        }

        return trim($texte);
    } catch (\Throwable $e) {
        Log::warning('Échec appel Gemini (aide à la rédaction), bascule sur le mode dégradé.', [
            'erreur' => $e->getMessage(),
        ]);

        return $this->fallback->ameliorerRedaction($type, $contenuBrut);
    }
}

private function construirePromptRedaction(string $type, string $contenuBrut): string
{
    return <<<PROMPT
Tu aides un utilisateur camerounais à rédiger un signalement clair pour une plateforme de
lutte contre les arnaques numériques (Sentinel IA).

Voici ses notes brutes, à propos d'un contenu de type "{$type}" :
"""
{$contenuBrut}
"""

Réécris ces notes en une description claire et bien structurée (3 à 6 phrases), en français,
destinée à être lue par un modérateur humain. Règles strictes :
- N'invente AUCUN fait, nom, montant ou détail absent des notes originales.
- N'évalue PAS s'il s'agit réellement d'une arnaque, ne donne aucun score : ton seul rôle est
  d'améliorer la clarté du texte, pas de l'analyser.
- Réponds uniquement avec le texte reformulé, sans préambule ni commentaire.
PROMPT;
}
}