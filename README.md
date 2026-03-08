# 🐘 PHP Learning API

> POC d'apprentissage PHP — API REST construite avec les bonnes pratiques PHP 8, Composer, PHPUnit et Docker.  
> Développé par un développeur Java pour monter en compétence PHP.

---

## 📋 Table des matières

- [Vue d'ensemble](#vue-densemble)
- [Stack technique](#stack-technique)
- [Structure du projet](#structure-du-projet)
- [Bonnes pratiques appliquées](#bonnes-pratiques-appliquées)
- [Installation locale (WAMP)](#installation-locale-wamp)
- [Installation avec Docker](#installation-avec-docker)
- [Commandes utiles Docker](#commandes-utiles-docker)
- [Tests](#tests)
- [Qualité de code](#qualité-de-code)
- [Routes API](#routes-api)
- [Appliquer Docker à un autre projet PHP](#appliquer-docker-à-un-autre-projet-php)

---

## Vue d'ensemble

API REST PHP qui expose des opérations CRUD sur une entité `User` (id, name, age).  
Ce projet est un POC d'apprentissage couvrant :

- PHP 8 orienté objet avec bonnes pratiques (PSR-4, strict_types, namespaces)
- Architecture en couches : Entity / Repository / Service / Controller
- Gestion des dépendances avec Composer
- Tests unitaires et d'intégration avec PHPUnit
- Conteneurisation complète avec Docker (Nginx + PHP-FPM + MySQL)

---

## Stack technique

| Technologie | Version | Rôle |
|-------------|---------|------|
| PHP | 8.3 (Docker) / 8.5 (WAMP) | Langage |
| Nginx | alpine | Serveur HTTP |
| PHP-FPM | 8.3 | Exécution PHP |
| MySQL | 8.0 | Base de données |
| Composer | 2.x | Gestionnaire de dépendances |
| PHPUnit | 13.x | Tests unitaires et d'intégration |
| PHPStan | 1.x | Analyse statique |
| vlucas/phpdotenv | 5.x | Variables d'environnement |
| Docker | 29.x | Conteneurisation |
| Docker Compose | v5.x | Orchestration |

---

## Structure du projet

```
php-learning-api/
├── docker/
│   ├── php/
│   │   └── Dockerfile          # Image PHP-FPM personnalisée
│   └── nginx/
│       └── default.conf        # Configuration Nginx
├── public/
│   └── index.php               # Point d'entrée unique (Front Controller)
├── src/
│   ├── Controller/
│   │   └── UserController.php  # Gestion des requêtes HTTP
│   ├── Database/
│   │   └── Database.php        # Connexion PDO (Singleton)
│   ├── Entity/
│   │   └── User.php            # Entité User
│   ├── Exception/
│   │   ├── InvalidUserDataException.php
│   │   └── UserNotFoundException.php
│   ├── Repository/
│   │   ├── UserRepositoryInterface.php  # Contrat du Repository
│   │   └── UserRepository.php           # Implémentation PDO/MySQL
│   └── Service/
│       └── UserService.php     # Logique métier
├── tests/
│   ├── Integration/
│   │   └── UserRepositoryTest.php  # Tests BDD réelle
│   └── Unit/
│       └── UserServiceTest.php     # Tests avec Mocks
├── .env.example                # Template des variables d'environnement
├── .env.docker                 # Variables pour Docker (non commité)
├── composer.json
├── composer.lock
├── docker-compose.yml
├── phpstan.neon                # Config analyse statique
└── phpunit.xml                 # Config tests
```

---

## Bonnes pratiques appliquées

### 1. `declare(strict_types=1)`
Activé sur chaque fichier PHP — impose un typage strict comme Java.
```php
<?php
declare(strict_types=1); // Toujours en ligne 2, juste après <?php
```

### 2. PSR-4 Autoloading
Plus de `require_once` — Composer résout automatiquement les classes via les namespaces.
```json
"autoload": {
    "psr-4": { "App\\": "src/" }
}
```
Règle : `src/Entity/User.php` → `namespace App\Entity` → `class User`

### 3. Interface sur le Repository
Le Service dépend de l'interface, pas de l'implémentation concrète.  
Permet de changer l'implémentation (MySQL, InMemory, API...) sans toucher au Service.
```php
// UserService dépend de l'interface
private UserRepositoryInterface $userRepository;
```

### 4. Injection de dépendances
Les dépendances sont injectées via le constructeur — jamais instanciées en dur dans les méthodes.
```php
public function __construct(UserRepositoryInterface $userRepository)
{
    $this->userRepository = $userRepository;
}
```

### 5. Exceptions métier avec Named Constructors
Messages centralisés et cohérents dans toute l'application.
```php
throw UserNotFoundException::withId($id);     // "Utilisateur avec l'ID 5 introuvable."
throw UserNotFoundException::withName($name); // "Utilisateur avec le nom 'Alice' introuvable."
```

### 6. Requêtes préparées PDO
Protection contre les injections SQL — jamais de concaténation directe.
```php
$stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute([':id' => $id]);
```

### 7. Variables d'environnement
Les credentials ne sont jamais en dur dans le code.
```
.env          → WAMP local (non commité)
.env.docker   → Docker (non commité)
.env.example  → Template commité sur Git
```

### 8. Codes HTTP sémantiques
```
200 OK          → GET réussi
201 Created     → POST réussi
400 Bad Request → Données manquantes ou invalides
404 Not Found   → Ressource inexistante
```

---

## Installation locale (WAMP)

### Prérequis
- WAMP avec PHP 8.x
- Composer 2.x
- MySQL avec la base `projet_from_scratch`

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/abenabbes/php-learning-api.git
cd php-learning-api

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
# Editer .env avec tes credentials MySQL

# 4. Créer la table users
# Dans phpMyAdmin ou MySQL CLI :
# CREATE TABLE users (
#     id   INT AUTO_INCREMENT PRIMARY KEY,
#     name VARCHAR(100) NOT NULL,
#     age  INT NOT NULL
# );
```

### Accès
```
http://localhost/php-learning-api/public/index.php/users
```

---

## Installation avec Docker

### Prérequis
- Docker Desktop 4.x+
- Docker Compose v2+

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/abenabbes/php-learning-api.git
cd php-learning-api

# 2. Créer le fichier .env.docker
cp .env.example .env.docker
# Contenu de .env.docker :
# DB_HOST=mysql
# DB_NAME=projet_from_scratch
# DB_USER=app
# DB_PASSWORD=app

# 3. Démarrer les conteneurs
docker compose up -d

# 4. Créer la table users
docker compose exec mysql mysql -u app -papp projet_from_scratch -e "
CREATE TABLE IF NOT EXISTS users (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age  INT NOT NULL
);"
```

### Accès
```
http://localhost:8080/users
```

---

## Commandes utiles Docker

### Démarrage / Arrêt

```bash
# Démarrer tous les conteneurs en arrière-plan
docker compose up -d

# Démarrer et voir les logs en temps réel
docker compose up

# Arrêter les conteneurs (données conservées)
docker compose down

# Arrêter et supprimer les volumes (données perdues !)
docker compose down -v

# Redémarrer un service spécifique
docker compose restart php
```

### Statut et logs

```bash
# Voir l'état des conteneurs
docker compose ps

# Voir les logs de tous les services
docker compose logs

# Voir les logs d'un service spécifique en temps réel
docker compose logs -f php
docker compose logs -f nginx
docker compose logs -f mysql
```

### Entrer dans un conteneur

```bash
# Entrer dans le conteneur PHP (shell sh sur Alpine)
docker compose exec php sh

# Entrer dans le conteneur MySQL
docker compose exec mysql mysql -u app -papp projet_from_scratch

# Lancer une commande dans le conteneur PHP sans y entrer
docker compose exec php php -v
docker compose exec php composer install
```

### Build et rebuild

```bash
# Rebuilder l'image PHP après modification du Dockerfile
docker compose build php

# Rebuilder et redémarrer
docker compose up -d --build

# Rebuilder sans cache (si des problèmes persistent)
docker compose build --no-cache php
```

### Nettoyage

```bash
# Supprimer les conteneurs arrêtés
docker container prune -f

# Supprimer les images non utilisées
docker image prune -f

# Supprimer toutes les images non utilisées (y compris les tags)
docker image prune -a -f

# Nettoyage complet (conteneurs, images, volumes, réseaux non utilisés)
docker system prune -a -f

# Voir l'espace disque utilisé par Docker
docker system df
```

### Réseau et volumes

```bash
# Lister les réseaux Docker
docker network ls

# Lister les volumes Docker
docker volume ls

# Supprimer un volume spécifique
docker volume rm php-learning-api_mysql_data
```

---

## Tests

### Lancer les tests

```bash
# Tous les tests
composer test

# Tests unitaires uniquement (pas de BDD)
composer test:unit

# Tests d'intégration (nécessite MySQL)
composer test:int
```

### Structure des tests

**Tests unitaires** (`tests/Unit/`) — rapides, sans dépendances externes  
Utilisent des Mocks PHPUnit pour simuler le Repository.

```bash
composer test:unit
# Tests: 4, Assertions: 13 — OK
```

**Tests d'intégration** (`tests/Integration/`) — testent la vraie BDD  
Nécessitent une base de données dédiée `projet_from_scratch_test`.

```bash
composer test:int
# Tests: 5, Assertions: 8 — OK
```

> ⚠️ Les tests d'intégration nettoient la table `users` avant chaque test.  
> Toujours utiliser une BDD de test dédiée, jamais la BDD de développement.

---

## Qualité de code

### PHPStan — Analyse statique

```bash
# Analyse au niveau 5
composer stan

# Résultat attendu :
# [OK] No errors
```

Le niveau 5 vérifie les types, les nullables et les appels de méthodes.  
Équivalent de SonarLint / SpotBugs en Java.

---

## Routes API

| Méthode | Route | Description | Code succès |
|---------|-------|-------------|-------------|
| `GET` | `/users` | Récupérer tous les utilisateurs | 200 |
| `GET` | `/users/{id}` | Récupérer un utilisateur par ID | 200 |
| `GET` | `/users/name/{name}` | Récupérer un utilisateur par nom | 200 |
| `POST` | `/users` | Créer un utilisateur | 201 |

### Exemples

**GET /users**
```json
[
  { "id": 1, "name": "Alice", "age": 30 },
  { "id": 2, "name": "Bob", "age": 25 }
]
```

**POST /users**
```json
// Request body
{ "name": "Alice", "age": 30 }

// Response 201
{ "id": 1, "name": "Alice", "age": 30 }
```

**Erreurs**
```json
// 404 - Not Found
{ "error": "Utilisateur avec l'ID 99 introuvable." }

// 400 - Bad Request
{ "error": "Les champs name et age sont obligatoires" }
```

---

## Appliquer Docker à un autre projet PHP

Ce setup Docker est réutilisable sur n'importe quel projet PHP. Voici le guide minimal.

### 1. Fichiers à copier

```
ton-projet/
├── docker/
│   ├── php/Dockerfile
│   └── nginx/default.conf
└── docker-compose.yml
```

### 2. Adapter le `Dockerfile`

```dockerfile
FROM php:8.3-fpm-alpine

# Ajouter les extensions dont ton projet a besoin
RUN docker-php-ext-install pdo pdo_mysql opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
EXPOSE 9000
```

### 3. Adapter `nginx/default.conf`

Changer uniquement la ligne `root` si ton point d'entrée n'est pas dans `public/` :
```nginx
root /var/www/html/public;  # Adapter selon ton projet
```

### 4. Adapter `docker-compose.yml`

Les seules choses à changer selon le projet :
```yaml
mysql:
  environment:
    MYSQL_DATABASE: nom_de_ta_bdd    # ← changer
    MYSQL_USER: app                   # ← optionnel
    MYSQL_PASSWORD: app               # ← optionnel
```

### 5. Créer `.env.docker`

```ini
DB_HOST=mysql      # Toujours "mysql" — nom du service Docker
DB_NAME=nom_de_ta_bdd
DB_USER=app
DB_PASSWORD=app
```

### 6. Lancer

```bash
docker compose up -d
docker compose ps   # Vérifier que les 3 services sont "Up"
```

> **Règle d'or :** `DB_HOST` est toujours le nom du service MySQL dans `docker-compose.yml`,  
> jamais `localhost`. C'est la différence fondamentale entre Docker et un environnement local.

---

*POC réalisé dans le cadre d'un parcours d'apprentissage PHP — transition Java → PHP*
