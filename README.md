## Récupérer le code source

Cloner le projet à l'adresse :
https://github.com/projet-full-stack/MuShare-backend

## Installer les dépendances

Ouvrir un terminal (en WSL si vous êtes sous Windows)
Exécuter la commande : 
```bash
composer install
```

## Générer les clés nécessaires à l'authentification

Exécuter la commande :
```bash
php bin/console lexik:jwt:generate-keypair
```

## Renseigner les variables d'environnement

Saisir vos données pour la base de données :
Créer un fichier .env.local et modifier la ligne DATABASE_URL

## Créer la base de données

Exécuter les commandes :
```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

## Générer les fixtures

Exécuter la commande :
```bash
php bin/console doctrine:fixtures:load 
```
Accepter de purger la base de données

## Lancer le serveur

Exécuter la commande :
```bash
symfony serve:start
```