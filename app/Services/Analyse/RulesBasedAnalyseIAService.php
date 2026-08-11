<?php

namespace App\Services\Analyse;

class RulesBasedAnalyseIAService implements AnalyseIAService
{
    // Vocabulaire volontairement ancré dans les arnaques fréquentes au Cameroun
    // (Mobile Money, fausses offres, loteries) plutôt qu'une liste générique.
    private const MOTS_CLES_SUSPECTS = [
        'urgent', 'gagné', 'félicitations', 'virement immédiat', 'mobile money',
        'code secret', 'compte bloqué', 'héritage', 'loterie', 'gratuit',
        'cliquez ici', 'offre exceptionnelle', 'transfert erreur', 'agent mtn',
        'agent orange money', 'sim swap',
    ];

    public function analyser(string $type, string $contenu): array
    {
         if ($type === 'image') {
        return [0.0, 'Analyse indisponible : le service IA est actuellement injoignable et ce type de contenu ne peut pas être analysé en mode local dégradé.'];
    }
    
        $texte = mb_strtolower($contenu);
        $occurrences = 0;

        foreach (self::MOTS_CLES_SUSPECTS as $motCle) {
            if (str_contains($texte, $motCle)) {
                $occurrences++;
            }
        }

        $score = min(100, $occurrences * 20);
        $conclusion = $occurrences > 0
            ? "Analyse locale (mode dégradé) : {$occurrences} indicateur(s) suspect(s) détecté(s) par mots-clés."
            : 'Analyse locale (mode dégradé) : aucun indicateur suspect détecté par la liste de mots-clés locale.';

        return [$score, $conclusion];
    }
}