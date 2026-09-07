<?php

namespace App\Services\Analyse;

class RulesBasedAnalyseIAService implements AnalyseIAService
{
    // Catégories pondérées : chaque catégorie a un poids différent car un indicateur
    // fort (demande de code secret) ne doit pas peser pareil qu'un indicateur faible
    // et ambigu (le mot "gratuit" seul, qui peut être parfaitement légitime).
    private const CATEGORIES = [
        'mobile_money' => [
            'poids' => 30,
            'mots' => [
                'code secret', 'code pin', 'compte bloqué', 'agent mtn', 'agent orange money',
                'sim swap', 'vérification de compte', 'confirmez votre code', 'numéro de retrait',
                'débloquer votre compte', 'suspendu', 'mobile money',
            ],
            'conseil' => "Ne communiquez JAMAIS votre code secret Mobile Money à qui que ce soit, y compris à une personne se présentant comme agent MTN/Orange. Ces opérateurs ne le demandent jamais par téléphone ou SMS.",
        ],
        'loterie_heritage' => [
            'poids' => 25,
            'mots' => [
                'gagné', 'félicitations', 'loterie', 'héritage', 'tirage au sort', 'vous avez été sélectionné',
                'réclamer votre prix', 'bénéficiaire', 'notification officielle de gain',
            ],
            'conseil' => "Aucune loterie légitime ne vous contacte à l'improviste pour réclamer un prix, surtout si vous n'y avez jamais participé. Ne payez jamais de \"frais de déblocage\" pour recevoir un gain.",
        ],
        'emploi_bourse' => [
            'poids' => 22,
            'mots' => [
                'offre d\'emploi', 'frais de dossier', 'frais de formation', 'bourse d\'étude',
                'recrutement urgent', 'sans expérience', 'salaire élevé garanti', 'entretien sans déplacement',
            ],
            'conseil' => "Un employeur ou un établissement sérieux ne demande jamais de paiement avant l'embauche ou l'admission. Vérifiez toujours l'existence légale de l'organisme via son site officiel avant de répondre.",
        ],
        'sentimental' => [
            'poids' => 20,
            'mots' => [
                'je t\'aime déjà', 'rencontre en ligne', 'besoin urgent d\'argent', 'coincé à l\'aéroport',
                'problème de visa', 'je ne peux pas t\'appeler', 'envoie-moi de l\'argent',
            ],
            'conseil' => "Méfiez-vous de toute personne rencontrée en ligne qui demande de l'argent, surtout en invoquant une urgence empêchant un appel vidéo. Ne transférez jamais de fonds à quelqu'un que vous n'avez jamais vu en personne.",
        ],
        'phishing_urgence' => [
            'poids' => 18,
            'mots' => [
                'urgent', 'immédiatement', 'dernier délai', 'cliquez ici', 'vérifiez maintenant',
                'action requise', 'compte sera fermé', 'expire aujourd\'hui',
            ],
            'conseil' => "La pression et l'urgence artificielle sont des techniques classiques de manipulation. Prenez toujours le temps de vérifier une information par un canal officiel avant d'agir.",
        ],
    ];

    // Indicateurs faibles : significatifs seulement en combinaison avec d'autres,
    // jamais suffisants seuls pour justifier un score élevé.
    private const INDICATEURS_FAIBLES = ['gratuit', 'offre exceptionnelle', 'transfert erreur'];

    // Motifs regex pour les liens : détecte des schémas structurels, pas juste des mots.
    private const TLD_SUSPECTS = ['/\.tk$/i', '/\.ga$/i', '/\.cf$/i', '/\.ml$/i', '/\.gq$/i'];

    public function analyser(string $type, string $contenu): array
    {
        if ($type === 'image') {
            return [0.0, 'Analyse indisponible : le service IA est actuellement injoignable et ce type de contenu ne peut pas être analysé en mode local dégradé.'];
        }

        $texte = mb_strtolower($contenu);
        $score = 0;
        $categoriesDetectees = [];

        foreach (self::CATEGORIES as $cle => $categorie) {
            $occurrences = 0;
            foreach ($categorie['mots'] as $motCle) {
                if (str_contains($texte, mb_strtolower($motCle))) {
                    $occurrences++;
                }
            }

            if ($occurrences > 0) {
                // Le poids de la catégorie s'applique une fois pour la première occurrence,
                // puis chaque occurrence supplémentaire ajoute un tiers du poids
                // (rendements décroissants : 3 mots de la même catégorie ne doivent pas
                // à eux seuls saturer le score à 100).
                $score += $categorie['poids'] + max(0, $occurrences - 1) * ($categorie['poids'] / 3);
                $categoriesDetectees[$cle] = $categorie;
            }
        }

        foreach (self::INDICATEURS_FAIBLES as $motFaible) {
            if (str_contains($texte, $motFaible)) {
                $score += 5; // poids volontairement faible, ne déclenche jamais seul une alerte forte
            }
        }

        if ($type === 'lien') {
            $score += $this->analyserStructureLien($contenu);
        }

        $score = (int) min(100, round($score));

        return [$score, $this->construireConclusion($score, $categoriesDetectees)];
    }

    private function analyserStructureLien(string $contenu): int
    {
        $url = $this->extraireUrl($contenu);
        $bonus = 0;

        foreach (self::TLD_SUSPECTS as $motif) {
            if (preg_match($motif, parse_url($url, PHP_URL_HOST) ?? '')) {
                $bonus += 15;
                break;
            }
        }

        $host = parse_url($url, PHP_URL_HOST) ?? '';

        // Usurpation de marque : domaine contenant un nom de marque connue combiné
        // à un tiret, signature typique des faux domaines ("mtn-mobilemoney-bonus.tk")
        if (preg_match('/\b(mtn|orange|camtel|nexttel)\b.*-/i', $host)) {
            $bonus += 15;
        }

        // URL utilisant une IP brute au lieu d'un nom de domaine : quasi jamais légitime
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $bonus += 20;
        }

        return $bonus;
    }

    private function extraireUrl(string $contenu): string
    {
        if (preg_match('/https?:\/\/[^\s<>"\']+/i', $contenu, $correspondances)) {
            return $correspondances[0];
        }

        return $contenu;
    }

    private function construireConclusion(int $score, array $categoriesDetectees): string
    {
        $prefixe = "Analyse locale (mode dégradé, service IA indisponible) : ";

        if (empty($categoriesDetectees)) {
            return $prefixe.'aucun indicateur suspect détecté par les règles locales. Cela ne garantit pas la légitimité du contenu — restez prudent et relancez une analyse IA complète dès que possible.';
        }

        $nomsCategories = [
            'mobile_money' => 'fraude Mobile Money',
            'loterie_heritage' => 'fausse loterie ou héritage',
            'emploi_bourse' => 'fausse offre d\'emploi ou de bourse',
            'sentimental' => 'arnaque sentimentale',
            'phishing_urgence' => 'technique de pression/urgence',
        ];

        $categoriePrincipale = array_key_first($categoriesDetectees);
        $libelles = array_map(fn ($cle) => $nomsCategories[$cle], array_keys($categoriesDetectees));

        return sprintf(
            '%sindicateurs correspondant à : %s. %s',
            $prefixe,
            implode(', ', $libelles),
            $categoriesDetectees[$categoriePrincipale]['conseil']
        );
    }

    public function ameliorerRedaction(string $type, string $contenuBrut): string
    {
        $texte = trim($contenuBrut);
        $texte = mb_strtoupper(mb_substr($texte, 0, 1)).mb_substr($texte, 1);

        if (! in_array(mb_substr($texte, -1), ['.', '!', '?'], true)) {
            $texte .= '.';
        }

        return $texte."\n\n(Mode dégradé : reformulation automatique indisponible pour le moment, texte affiché après mise en forme minimale seulement.)";
    }

    public function appelDistantEffectue(): bool
    {
        return false;
    }
}
