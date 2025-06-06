﻿# Projet Web Symfony

## Description
Ce projet est une application web développée avec Symfony 7.2, utilisant Tailwind CSS pour le design et suivant une approche "mobile-first".

## Prérequis
- PHP 8.4.3
- Composer 2.8.5
- Node.js (pour Tailwind CSS)
- Symfony CLI

## Installation

1. Cloner le projet
```bash
git clone [url-du-projet]
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Installer les dépendances JavaScript
```bash
npm install
```

4. Configurer la base de données
- Copier le fichier `.env` en `.env.local`
- Modifier les paramètres de connexion à la base de données

5. Créer la base de données et exécuter les migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. Compiler les assets
```bash
php bin/console tailwind:build --watch
```

## Structure du Projet

### Backend
- `/src/Controller/` - Contrôleurs de l'application
- `/src/Entity/` - Entités Doctrine
- `/src/Repository/` - Repositories pour l'accès aux données
- `/src/Form/` - Types de formulaires
- `/src/Services/` - Services métier
- `/src/Security/` - Classes liées à la sécurité

### Frontend
- `/assets/` - Fichiers source JavaScript et CSS
- `/templates/` - Templates Twig
- `/public/` - Fichiers statiques et point d'entrée

### Tests
- `/tests/Unit/` - Tests unitaires
- `/tests/Integration/` - Tests d'intégration
- `/tests/Functional/` - Tests fonctionnels

## Standards de Codage
- PSR-12 pour PHP
- Conventions de nommage :
  - Variables et fonctions : camelCase
  - Classes : PascalCase
  - Noms explicites reflétant l'objectif

## Design
- Utilisation de Tailwind CSS
- Approche "Mobile First"
- Points de rupture :
  - Mobile : jusqu'à 767px
  - Tablette : 768px à 991px
  - Desktop : 992px et plus
- Palette de couleurs limitée à 3 teintes
- Maximum 2 polices différentes
- Importance des espaces négatifs
- Hiérarchie visuelle claire
- Accessibilité optimisée

## Tests
- PHPUnit pour les tests unitaires
- Objectif de couverture de code : 80%
- Exécution des tests à chaque commit

## Commandes Utiles

### Développement
```bash
# Lancer le serveur de développement
symfony server:start

# Compiler les assets en mode développement
npm run dev-server

# Compiler les assets pour la production
npm run build
```

### Tests
```bash
# Exécuter les tests
php bin/phpunit
```

### Base de données
```bash
# Créer une migration
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate
```

## Sécurité
- Utilisation de requêtes préparées pour prévenir les injections SQL
- Gestion des erreurs avec try-catch
- Authentification et autorisation gérées par Symfony Security



