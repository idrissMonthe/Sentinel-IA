# Sentinel IA — Claude Haiku 4.5

L'application utilise exclusivement `ClaudeAnalyseIAService`, injecté par `AppServiceProvider` : analyses texte, email, numéro, lien, image et reformulation des signalements. Les appels utilisent `https://api.anthropic.com/v1/messages`. Le service, la commande de diagnostic et les tests OpenAI ont été remplacés. Aucun repli automatique n'est activé.

## Configuration

Le bloc Anthropic est rempli dans `.env.example`. La clé réelle appartient uniquement au fichier `.env`, ignoré par Git :

```dotenv
ANTHROPIC_API_KEY="votre_cle_anthropic"
ANTHROPIC_WORKSPACE_ID=
ANTHROPIC_MODEL=claude-haiku-4-5-20251001
ANTHROPIC_TIMEOUT=60
ANTHROPIC_MAX_TOKENS=1024
ANTHROPIC_RETRY_ATTEMPTS=2
ANTHROPIC_RETRY_DELAY_MS=250
ANTHROPIC_CA_BUNDLE=
```

Claude 2 et 2.1 sont retirés depuis le 21 juillet 2025. Haiku 4.5 a été choisi pour cette intégration. [Cycle de vie des modèles](https://platform.claude.com/docs/en/about-claude/model-deprecations).

**Clé multi-workspace :** renseigner `ANTHROPIC_WORKSPACE_ID` avec l'ID trouvé dans la console Anthropic, Settings > Workspaces. Le service l'envoie dans l'en-tête `anthropic-workspace-id`. Une clé rattachée à un seul workspace peut laisser cette variable vide. [Authentification Anthropic](https://platform.claude.com/docs/en/manage-claude/authentication).

```powershell
php artisan config:clear
php artisan sentinel:claude-check
php artisan sentinel:claude-check --live
```

Sans `--live`, seule la configuration est contrôlée. Avec `--live`, une analyse synthétique potentiellement facturable est envoyée ; aucune ligne d'analyse n'est créée dans la base.

## Historique des premiers tests de connexion

Le service injecté a été vérifié : `App\Services\Analyse\ClaudeAnalyseIAService`.
La clé est présente. Le premier appel était bloqué par l'absence de certificats racines dans PHP (cURL 60). Un bundle local a été constitué à partir des certificats publics déjà approuvés par Windows, dans `storage/app/private/anthropic-ca.pem`, ignoré par Git. Son chemin est configuré uniquement dans `.env`. La vérification TLS reste activée.

Le workspace est désormais renseigné. Les derniers essais renvoient HTTP 503 sur l'analyse, sur GET /v1/models et sur un message minimal sans schéma JSON. Les réponses portent l'en-tête Cloudflare, sans code d'erreur Anthropic ni request-id : l'origine exacte (API ou intermédiaire réseau) reste indéterminée. Ce blocage initial a été résolu depuis : un essai réel réussi avec contenu web est décrit à la fin de ce guide. La commande distingue maintenant cette indisponibilité des erreurs de clé et indique les prochaines étapes. Réessayer après une minute ; si le problème persiste, comparer depuis un autre réseau autorisé et consulter https://status.claude.com.

Sur une autre machine, configurer les autorités de certification de PHP normalement, ou utiliser `ANTHROPIC_CA_BUNDLE` avec un bundle approuvé localement. Ne pas recopier aveuglément le chemin propre à cette machine.

## Administrateur

Utiliser une adresse email à laquelle vous avez accès : la connexion envoie un code de vérification.

```powershell
php artisan sentinel:admin-create "votre-email@domaine.com"
```

La commande crée un compte actif avec le rôle `ADMINISTRATEUR`, génère un mot de passe aléatoire de 20 caractères et l'affiche une seule fois dans le terminal sous `Mot de passe : ...`. La base ne conserve que son hash. Aucun mot de passe fixe n'est défini dans le dépôt. Un compte existant n'est ni modifié, ni promu, ni réinitialisé.

Options facultatives : `--nom="Votre nom" --prenom="Votre prénom"`.
La configuration email de `.env` doit permettre la réception du code. Avec `MAIL_MAILER=log`, le message apparaît dans les journaux locaux plutôt que dans une boîte email.

## Tests

```powershell
php -d extension=pdo_sqlite vendor/phpunit/phpunit/phpunit
```

Les tests utilisent SQLite en mémoire et des réponses HTTP simulées. Ils vérifient notamment le protocole Anthropic, les images, le format JSON, les réponses refusées/tronquées, l'en-tête workspace, le parcours HTTP d'analyse avec persistance, les quotas et la création sécurisée d'un administrateur.

Les scores historiques conservent leur sens : malgré son nom, `score_fiabilite` est un score de risque, élevé en présence d'indices d'arnaque.

## Contenu réellement analysé

- **Texte, numéro, email** : uniquement les éléments saisis. Aucun registre de réputation, WHOIS ou base de fraude externe n'est consulté.
- **Site / domaine / URL** : une adresse sans protocole reçoit `https://`. Le serveur récupère la page et suit au maximum trois redirections contrôlées, extrait titre, description, nombre de formulaires et jusqu'à 8 000 caractères de texte du HTML, sans scripts ni styles. Claude reçoit ce texte comme donnée non fiable. Les connexions privées/locales sont bloquées, l'IP du domaine est fixée après validation, TLS est vérifié, le téléchargement est limité à 500 Ko. `SENTINEL_WEB_CA_BUNDLE` peut définir un bundle CA propre aux sites ; par défaut le bundle Anthropic local est utilisé s'il est configuré.
- **Capture d'écran** : l'image JPG/PNG validée (5 Mo maximum) est envoyée à la vision de Claude. L'ancien texte du formulaire est désactivé et ignoré côté serveur ; il ne peut pas servir de chemin de fichier.

Il ne s'agit pas d'une exploration de tout le domaine : seule la page demandée est récupérée, JavaScript n'est pas exécuté et les pages nécessitant une connexion ne sont pas accessibles. Si la récupération échoue ou ne donne pas de texte, l'analyse est bloquée avant tout appel IA, avec une invitation à envoyer une capture ou du texte. Le rapport affiche désormais la source utilisée et le libellé « Score de risque » (ce score est une estimation, pas une probabilité de fraude vérifiée).

## Prompt caching et consommation

Le cache automatique de 5 minutes est activé avec `ANTHROPIC_PROMPT_CACHING=true`. Il ajoute `cache_control: {type: ephemeral}` aux requêtes d'analyse et de reformulation. Pour le désactiver, mettre la variable à `false`, puis exécuter `php artisan config:clear`.

Le cache n'est pas un outil de mesure : les tokens sont suivis séparément à partir de `usage` dans les réponses Anthropic. Les compteurs sont conservés dans `consommations_ia`, sans contenu utilisateur ni clé API, y compris lorsqu'une réponse reçue est ensuite rejetée pour format invalide. Les échecs sans compteurs ne sont pas mesurés ; une panne de la table de suivi est journalisée sans faire perdre l'analyse. Le suivi n'est pas rétroactif et ne remplace pas la facturation de la console Anthropic.

Pour suivre l'évolution : menu administrateur **Consommation IA** (`/admin/consommation-ia`), ou :

```powershell
php artisan sentinel:tokens --days=30
```

Les colonnes distinguent entrée hors cache, écriture de cache, lecture de cache et sortie. Total des entrées = entrée hors cache + écriture + lecture. Haiku 4.5 exige au moins **4 096 tokens dans le préfixe éligible** ; un texte court peut donc conserver des compteurs de cache à zéro malgré l'activation. La réutilisation demande un préfixe identique et dépend de la durée de vie du cache. Ne pas allonger artificiellement les prompts pour atteindre ce seuil. Référence : [prompt caching Anthropic](https://platform.claude.com/docs/en/build-with-claude/prompt-caching).

Validation du 29 septembre 2026 : 68 tests, 187 assertions. Essai réel réussi sur le contenu public de `kirovadigital.com` : 5 826 caractères récupérés, 2 279 tokens d'entrée et 183 de sortie, aucun token en cache. Cet essai technique est comptabilisé dans le suivi ; il ne crée pas de rapport utilisateur. Les nouveaux rapports de lien afficheront leur source, les anciens ne sont pas réanalysés automatiquement.
