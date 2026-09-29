<?php

$seuilEleve = filter_var(env('SENTINEL_IA_SEUIL_RISQUE_ELEVE', 70), FILTER_VALIDATE_INT, [
    'options' => ['default' => 70, 'min_range' => 1, 'max_range' => 100],
]);
$seuilModere = filter_var(env('SENTINEL_IA_SEUIL_RISQUE_MODERE', 40), FILTER_VALIDATE_INT, [
    'options' => ['default' => 40, 'min_range' => 0, 'max_range' => 100],
]);

return [
    'web_ca_bundle' => env('SENTINEL_WEB_CA_BUNDLE', env('ANTHROPIC_CA_BUNDLE')),
    'quota_analyses_jour' => env('SENTINEL_IA_QUOTA_ANALYSES_JOUR', 5),
    'seuil_risque_eleve' => $seuilEleve,
    'seuil_risque_modere' => min($seuilModere, $seuilEleve),
];
