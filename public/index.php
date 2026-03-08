<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\UserController;

use Dotenv\Dotenv;

// Chargement des variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$uri      = $_SERVER['REQUEST_URI'];
$method   = $_SERVER['REQUEST_METHOD'];
$path     = parse_url($uri, PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

// URL : /php-learning-api/public/index.php/users
$route    = $segments[3] ?? null;  // "users"
$param1   = $segments[4] ?? null;  // "3" ou "name"
$param2   = $segments[5] ?? null;  // "Alice" si route /users/name/Alice

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