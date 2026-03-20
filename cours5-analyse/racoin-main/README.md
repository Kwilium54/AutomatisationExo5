## Racoin

Site de petites annonces pour la région Grand-Est / Bourgogne.

## Lancer le projet

**Prérequis :** Docker et Docker Compose installés.

```bash
# Installer les dépendances PHP
docker run --rm -v $(pwd):/app composer install

# Copier et adapter la config (laisser les valeurs par défaut si vous utilisez Docker)
cp config/config.ini.example config/config.ini

# Démarrer l'application (PHP + MySQL)
docker compose up -d
```

L'application est accessible sur [http://localhost:8080](http://localhost:8080).

La base de données est automatiquement initialisée au premier démarrage via les fichiers `sql/`.

> `config/config.ini` contient les credentials DB, il ne doit pas être commité.