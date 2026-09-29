<?php

return [
    'web_ca_bundle' => env('SENTINEL_WEB_CA_BUNDLE', env('ANTHROPIC_CA_BUNDLE')),
    'quota_analyses_jour' => env('SENTINEL_IA_QUOTA_ANALYSES_JOUR', 5),
];
