<?php

namespace App\Services\Analyse;

interface AnalyseIAService
{
    /**
     * Envoie le contenu à l'IA et retourne [score_fiabilite (0-100), conclusion (texte)].
     * Les images sont transmises directement à la vision du fournisseur ; les liens sont enrichis par le contrôleur.
    */
    public function analyser(string $type, string $contenu): array;
    public function ameliorerRedaction(string $type, string $contenuBrut): string;

    /** Indique si la dernière opération a obtenu une réponse du fournisseur distant. */
    public function appelDistantEffectue(): bool;
}
