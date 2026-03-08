<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\UserController;

use Dotenv\Dotenv;

// Chargement des variables d'environnement
// Détecte l'environnement et charge le bon fichier .env
// APP_ENV=docker → chargé depuis docker-compose.yml
// Sinon → .env normal (WAMP)
$appEnv  = getenv('APP_ENV');
$envFile = $appEnv === 'docker' ? '.env.docker' : '.env';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../', $envFile);
$dotenv->load();

$uri      = $_SERVER['REQUEST_URI'];
$method   = $_SERVER['REQUEST_METHOD'];
$path     = parse_url($uri, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// Détecte automatiquement le bon index selon l'environnement
// Docker  : /users/1        → segments[0] = users
// WAMP    : /php-learning-api/public/index.php/users/1 → segments[3] = users
$appEnv = getenv('APP_ENV');
$offset = $appEnv === 'docker' ? 0 : 3;

$route  = $segments[$offset]     ?? null;
$param1 = $segments[$offset + 1] ?? null;
$param2 = $segments[$offset + 2] ?? null;

$userController = new UserController();

if ($route === 'users' && $method === 'GET' && $param1 === null) {
    // GET /users → tous les users
    $userController->findAllUsers();

} elseif ($route === 'users' && $method === 'GET' && $param1 === 'name' && $param2 !== null) {
    // GET /users/name/Alice → user par nom
    $userController->findUserGetByName($param2);

} elseif ($route === 'users' && $method === 'GET' && $param1 !== null) {
    // GET /users/3 → user par ID
    $userController->findUserGetById((int) $param1);

} elseif ($route === 'users' && $method === 'POST') {
    // POST /users → créer un user
    $userController->createUserPost();

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}