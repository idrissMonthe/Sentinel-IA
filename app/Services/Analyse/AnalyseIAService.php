<?php

namespace App\Services\Analyse;

interface AnalyseIAService
{
    /**
     * Envoie le contenu à l'IA et retourne [score_fiabilite (0-100), conclusion (texte)].
     * Le type détermine si un passage OCR est nécessaire avant l'appel IA
     * (cf. <<include>> Analyser une image -> Extraire le texte du diagramme de cas d'utilisation).
     */
    public function analyser(string $type, string $contenu): array;
}