<?php

namespace App\Console\Commands;

use App\Models\ConsommationIA;
use Illuminate\Console\Command;

class TokenUsage extends Command
{
    protected $signature = 'sentinel:tokens {--days=7 : Nombre de jours, de 1 à 365}';

    protected $description = 'Afficher la consommation Claude par jour et par modèle';

    public function handle(): int
    {
        $days = filter_var($this->option('days'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 365]]);
        if ($days === false) {
            $this->error('--days doit être compris entre 1 et 365.');

            return self::FAILURE;
        }
        $rows = ConsommationIA::where('created_at', '>=', today()->subDays($days - 1))
            ->selectRaw('DATE(created_at) as jour, modele, COUNT(*) as appels, SUM(input_tokens) as entrees, SUM(output_tokens) as sorties, SUM(cache_creation_input_tokens) as ecrits, SUM(cache_read_input_tokens) as lus')
            ->groupByRaw('DATE(created_at), modele')->orderBy('jour')->get();
        $this->table(['Jour', 'Modèle', 'Appels', 'Entrée hors cache', 'Écrits cache', 'Lus cache', 'Sortie', 'Total tokens'], $rows->map(fn ($row) => [
            $row->jour, $row->modele, $row->appels, $row->entrees, $row->ecrits, $row->lus, $row->sorties,
            $row->entrees + $row->ecrits + $row->lus + $row->sorties,
        ])->all());
        $this->line('Mesures reçues de Claude depuis l’activation du suivi, analyses et reformulations comprises.');
        $this->line('Un total de tokens n’est pas un montant facturé. Un cache à zéro est normal pour les prompts courts.');

        return self::SUCCESS;
    }
}
