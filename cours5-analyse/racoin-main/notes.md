# Notes — Racoin

## Fiche d'identification

- **Nom :** Racoin
- **But :** site de petites annonces type LeBonCoin, pour la région Grand-Est / Bourgogne
- **Langages :** PHP, HTML/CSS/JS, SQL
- **Frameworks :** Slim 2, Twig 1, Eloquent 4.2.9
- **BDD :** MySQL

---

## Analyse théorique

L'appli permet de déposer, modifier et supprimer des annonces. Pas de compte utilisateur, chaque annonce est protégée par un mot de passe choisi à la création.

Pour faire tourner le projet :
- `composer install`
- créer `config/config.ini` (le fichier n'est pas dans le repo, juste un placeholder vide)
- avoir une base MySQL et importer les fichiers SQL du dossier `sql/`
- lancer avec `php -S localhost:8080` ou Docker

---

## Maintenance

### Ce qui est obsolète

| Dépendance | Version | Remarque |
|---|---|---|
| Slim | 2.x | plus maintenu depuis ~2016, version actuelle : 4 |
| Twig | ~1.0 | EOL 2022, version actuelle : 3 |
| Eloquent | 4.2.9 | très vieux (~2014), version actuelle : 11 |
| Autoload PSR-0 | — | déprécié depuis 2014, remplacé par PSR-4 |

### Todo list

| Tâche | Effort /10 | Impact /10 |
|---|---|---|
| Ajouter MySQL dans docker-compose + créer config.ini | 2 | 10 |
| Corriger bug params inversés dans `item::edit()` | 1 | 8 |
| Réparer le middleware CSRF (commenté) | 2 | 9 |
| Remplacer `md5(uniqid(rand()))` par `random_bytes` | 1 | 7 |
| Corriger clé primaire `ApiKey` (`id_key` → `id_apikey`) | 1 | 6 |
| Mettre à jour Twig 1 → 3 | 5 | 8 |
| Mettre à jour Slim 2 → 4 | 8 | 8 |
| Mettre à jour Eloquent 4 → 11 | 7 | 7 |
| Passer l'autoload en PSR-4 | 2 | 4 |
| Dédupliquer la fonction `isEmail()` (définie 2 fois) | 1 | 5 |

---

## Actions réalisées

### Mise en place de l'environnement local

- Ajout d'un service MySQL 8 dans `docker-compose.yml` : le `docker-compose` d'origine ne contenait qu'un service PHP sans base de données, l'application ne pouvait donc pas démarrer.
- Les fichiers SQL sont montés automatiquement dans `/docker-entrypoint-initdb.d/` : la BDD est initialisée au premier `docker compose up` sans manipulation manuelle.
- Création de `config/config.ini` (ignoré par git) et d'un `config/config.ini.example` à committer comme référence.
- Mise à jour du `README.md` avec les instructions de démarrage.

### Corrections de bugs et sécurité

- **Paramètres inversés dans `item::edit()`** : dans `index.php`, l'appel passait `$id` avant `$allPostVars` alors que la méthode attend l'inverse — la modification d'annonce échouait systématiquement.
- **Clé primaire `ApiKey`** : le modèle déclarait `id_key` alors que la colonne en BDD s'appelle `id_apikey` — la génération de clé API ne fonctionnait pas.
- **Token de session** : remplacement de `md5(uniqid(rand(), TRUE))` par `bin2hex(random_bytes(16))` — l'ancienne méthode n'est pas cryptographiquement sûre.
- **Fonction `isEmail()` en double** : définie dans `addItem` et dans `item`, ce qui pouvait causer une fatal error. Remplacée par `filter_var($email, FILTER_VALIDATE_EMAIL)` dans les deux cas, ce qui est aussi plus fiable.

### Mise à jour des dépendances

- **PHP 7.4 → 8.2** : PHP 7.4 est EOL depuis fin 2022. Mis à jour dans le `Dockerfile`.
- **Slim 2 → 3** : Slim 2 n'est plus maintenu depuis ~2016. Migration vers Slim 3 : nouvelle syntaxe des routes (`{param}` au lieu de `:param`), signatures des callbacks (`$request, $response, $args`), réécriture des réponses JSON de l'API. Le saut vers Slim 4 aurait nécessité l'introduction d'un container PSR-11 complet, trop invasif pour cette étape.
- **Twig 1 → 3** : Twig 1 est EOL depuis 2022. Dans tous les controllers, `$twig->loadTemplate('x.html.twig')->render($vars)` remplacé par `$twig->render('x.html.twig', $vars)`.
- **Eloquent 4.2.9 → ^10.0** : version ~2014. Eloquent 10 requiert PHP 8.1+ — compatible avec notre PHP 8.2. L'API Capsule (utilisée dans `db/connection.php`) est restée stable.
- **Autoload PSR-0 → PSR-4** : PSR-0 est déprécié depuis 2014. Mis à jour dans `composer.json`, les namespaces correspondaient déjà à la structure de fichiers donc aucun autre changement nécessaire.

