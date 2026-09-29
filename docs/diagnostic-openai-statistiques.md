> Archive du diagnostic initial. L'intégration OpenAI a depuis été remplacée par Claude : voir [le guide actuel](claude.md).

# Diagnostic Sentinel IA — 29 septembre 2026

## Résultat

La page `/statistiques` renvoyait une erreur SQL car la base MySQL locale utilisait un ancien schéma. La table `signalements` ne contenait que `id`, `titre`, `contenu`, `est_publie`, `created_at` et `updated_at`. Les colonnes métier de `users` étaient également absentes. Les migrations initiales étaient déjà marquées comme exécutées : modifier leurs fichiers ne met pas à jour une base existante.

La migration `2026_09_29_000000_reconcile_legacy_schema.php` ajoute les colonnes manquantes, conserve les anciennes données et reprend `users.name` dans `nom`, ainsi que `signalements.contenu` dans `description`. Elle a été appliquée à la base locale. Aucun compte propriétaire n'est inventé pour d'anciens signalements ; les relations ajoutées sont nullables. Le retour arrière de cette migration conserve volontairement les colonnes, car leur présence initiale dépend de la base installée.

Les répartitions publiques par type et ville prennent désormais en compte les signalements validés. Les types comptent les entités distinctes ayant un signalement validé, les villes comptent les signalements validés. Le cache de statistiques est renouvelé après une heure ; les changements ne sont donc pas instantanés. Après recalcul local, `/statistiques` répond HTTP 200 ; les trois indicateurs principaux valent actuellement zéro.

## Activer OpenAI

`AppServiceProvider` utilise déjà `OpenAIAnalyseIAService`, via `POST https://api.openai.com/v1/chat/completions`. Le fichier Gemini mentionné dans l'éditeur n'est pas présent dans l'arborescence examinée. Aucun repli automatique vers le service à règles n'est activé.

Lors du diagnostic, Laravel ne chargeait **aucune clé OpenAI**. Dans le fichier `.env` local, renseigner :

```dotenv
OPENAI_API_KEY="votre_cle_du_projet_openai"
OPENAI_MODEL=gpt-5-mini
OPENAI_TIMEOUT=60
OPENAI_RETRY_ATTEMPTS=2
OPENAI_RETRY_DELAY_MS=250
```

La valeur `votre_cle_du_projet_openai` doit être remplacée par la vraie clé. Ne pas la publier ni la placer dans le JavaScript. Le délai de 60 secondes est un réglage proposé pour tolérer les réponses lentes ; la valeur par défaut actuelle reste 15 secondes.

Pour créer une clé, ouvrir [les clés API du projet OpenAI](https://platform.openai.com/api-keys), sélectionner le projet concerné et créer une clé secrète. Vérifier également les crédits et limites de ce projet dans les paramètres de la plateforme. Voir le [guide officiel de démarrage](https://developers.openai.com/api/docs/quickstart).

Après avoir enregistré `.env` :

```powershell
php artisan config:clear
php artisan sentinel:openai-check
php artisan sentinel:openai-check --live
```

La première vérification ne fait aucun appel réseau. `--live` envoie un texte fictif au modèle configuré et vérifie le format de la réponse ; cet appel peut être facturé. La commande n'affiche jamais la clé et ne crée aucune analyse dans la base métier.

- HTTP 401 : clé invalide, supprimée ou incorrecte.
- HTTP 403/404 : vérifier les permissions du projet et l'accès au modèle.
- HTTP 429 : vérifier le code d'erreur, les crédits et les limites de dépenses ou de débit.
- Connexion impossible : vérifier réseau, certificats TLS et délai maximal. Ne pas désactiver TLS.

Référence : [codes d'erreur officiels OpenAI](https://developers.openai.com/api/docs/guides/error-codes).

L'intégration demande maintenant explicitement une conclusion de une à trois phrases, conformément à sa validation. Les erreurs HTTP sont journalisées avec leur statut, code et identifiant de requête, sans corps de réponse susceptible de contenir une clé ou le contenu utilisateur.

## Validation

43 tests et 105 assertions passent, incluant les tests simulés OpenAI, les statistiques, la migration d'un ancien schéma et le parcours de connexion avec code de vérification. Le test de connexion attendait auparavant une authentification immédiate, alors que l'application exige un code.

Le PHP CLI local n'active pas `pdo_sqlite` par défaut. Le pilote existe et a été activé uniquement pour la commande de test, sans modifier `php.ini` :

```powershell
php -d extension=pdo_sqlite vendor/phpunit/phpunit/phpunit
```

Les tests utilisent SQLite en mémoire, pas la base MySQL locale. Aucun appel réel OpenAI n'a été validé faute de clé configurée.

## Autres points observés à traiter séparément

- `routes/web.php` déclare deux fois les routes de mot de passe oublié/réinitialisation. Les dernières déclarations remplacent les premières, ce qui rend le comportement difficile à suivre.
- `Analyse::utilisateur()` et `Signalement::utilisateur()` utilisent `belongsTo(User::class)` sans préciser `user_id`. Le nom de relation implique `utilisateur_id`, absent du schéma. Les parcours reposant sur ces relations doivent être corrigés.
- Le quota quotidien compte les analyses enregistrées avec succès. Les appels facturés mais échouant à la validation et les requêtes simultanées ne sont pas couverts par un compteur atomique.
- Le champ historique `score_fiabilite` représente en réalité un score de risque : une valeur élevée signifie davantage d'indices d'arnaque. Les écrans et intégrations doivent conserver cette interprétation.

Cette revue porte sur les statistiques, le schéma local, l'intégration OpenAI et les tests existants. Elle ne constitue pas un audit exhaustif de toutes les fonctionnalités.

