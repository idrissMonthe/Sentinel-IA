<?php

namespace App\Console\Commands;

use App\Services\Analyse\AnalyseIAIndisponibleException;
use App\Services\Analyse\ClaudeAnalyseIAService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class DiagnoseClaude extends Command
{
    protected $signature = 'sentinel:claude-check {--live : Effectuer une analyse de test facturable avec Claude}';

    protected $description = 'Vérifier Claude sans afficher la clé API';

    public function handle(ClaudeAnalyseIAService $service): int
    {
        $this->line('Modèle : '.config('services.anthropic.model'));
        $this->line('Délai maximal par tentative : '.config('services.anthropic.timeout').' s');
        if (blank(config('services.anthropic.api_key'))) {
            $this->error('Clé absente : renseignez ANTHROPIC_API_KEY dans .env puis lancez php artisan config:clear.');

            return self::FAILURE;
        }
        $this->info('Clé configurée (valeur masquée).');
        if (! $this->option('live')) {
            $this->line('Aucun appel réseau effectué. Ajoutez --live pour tester une analyse facturable.');

            return self::SUCCESS;
        }
        try {
            $service->analyser('texte', 'Un inconnu demande mon code PIN Mobile Money pour débloquer un gain.');
            $this->info('Claude opérationnel : réponse reçue et format validé.');

            return self::SUCCESS;
        } catch (AnalyseIAIndisponibleException $exception) {
            $cause = $exception->getPrevious();
            if ($cause instanceof RequestException) {
                $status = $cause->response->status();
                $code = $cause->response->json('error.type');
                if ($status === 503 && trim($cause->response->body()) === 'credential validation failed') {
                    $this->error('HTTP 503 : le service distant signale « credential validation failed ».');
                    $this->line('Vérifiez les permissions de la clé pour les modèles et son workspace dans la console Anthropic.');
                    $this->line('Si ces paramètres sont corrects, contactez le support Anthropic avec la date de cet essai, sans transmettre la clé.');

                    return self::FAILURE;
                }
                if ($status === 400 && str_contains((string) $cause->response->json('error.message'), 'anthropic-workspace-id')) {
                    $this->error('Cette clé exige ANTHROPIC_WORKSPACE_ID dans .env (console Anthropic > Settings > Workspaces).');

                    return self::FAILURE;
                }
                $message = match ($status) {
                    400 => 'Requête refusée : vérifiez le modèle et le solde de crédits dans la console Anthropic.',
                    401 => 'Clé invalide ou révoquée : vérifiez la clé du projet Claude.',
                    403 => 'Accès refusé : vérifiez les permissions du projet et les restrictions d’accès.',
                    404 => 'Modèle introuvable ou inaccessible : vérifiez ANTHROPIC_MODEL.',
                    500, 502, 503, 504 => 'Service distant temporairement indisponible. Réessayez dans une minute et consultez https://status.claude.com.',
                    529 => 'API Claude temporairement surchargée. Réessayez dans une minute.',
                    429 => 'Vérifiez les crédits, limites de dépenses et limites de débit du projet Claude.',
                    default => 'Consultez le statut HTTP et le code d’erreur dans storage/logs/laravel.log.',
                };
                $this->error("HTTP {$status} : {$message}");
                if ($status >= 500 && blank($code)) {
                    $this->warn('Réponse sans code d’erreur Anthropic : l’origine exacte (API ou intermédiaire réseau) reste indéterminée.');
                }
                $requestId = $cause->response->header('request-id');
                if (is_string($requestId) && preg_match('/^req_[A-Za-z0-9_-]{1,100}$/D', $requestId)) {
                    $this->line('Identifiant de requête : '.$requestId);
                }
                if (is_string($code) && preg_match('/^[a-z_]{1,80}$/D', $code)) {
                    $this->line('Code Claude : '.$code);
                }
            } elseif ($cause instanceof ConnectionException) {
                $this->error('Connexion impossible ou délai dépassé : vérifiez réseau, certificats TLS et ANTHROPIC_TIMEOUT.');
            } else {
                $this->error('La réponse ne respecte pas le format attendu par Sentinel IA.');
            }

            return self::FAILURE;
        }
    }
}
