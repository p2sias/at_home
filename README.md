# Installation

1. npm install

2. composer install

3. php artisan key:generate

4. remplissage du .ENV avec vos informations de base de donnée

5. php artisan migrate

6. php artisan serve

## Compte administrateur

Un compte super-administrateur est automatiquement créé :

email: super.admin@athome.com
password: adminadmin

pour creer des utilisateurs, utilisez soit la page d'inscription ou alors cliquez sur le signe + dans la page de gestion utilisateurs sur le compte super-admin

## Documentation

Vous retrouverez la documentation du projet dans le dossier DOCUMENTATION se trouvant sur la racine du projet

## Architecture et autres

La partie web se trouve dans ressource/js/...

Si vous voulez effectuer des modifications sur la partie web, entrez la commande `npm run watch` (compilation du projet Vue à chaques CTRL+S)
