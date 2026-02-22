<?php
// Teste de registro de rotas
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock simples de Response
class Response {
    public static function error($msg, $code) {
        echo "Response::error($msg, $code)\n";
    }
    public static function success($data, $msg) {
        echo "Response::success(..., $msg)\n";
    }
}

// Mock AuthController
class AuthController {
    public static function login() {
        echo "AuthController::login() chamado\n";
    }
}

// Incluir Router
require_once 'api/v1/Router.php';

// Simular requisição POST /auth/login
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/SAW-main/api/v1/auth/login';

$router = new Router();

// Registrar rotas
$router->post('/auth/login', function () {
    AuthController::login();
});

echo "========================================\n";
echo "Rotas registradas:\n";

// Usar reflexão para acessar rotas privada
$reflection = new ReflectionClass($router);
$property = $reflection->getProperty('routes');
$property->setAccessible(true);
$routes = $property->getValue($router);

foreach ($routes as $method => $methodRoutes) {
    echo "\n$method:\n";
    foreach ($methodRoutes as $path => $callback) {
        echo "  $path\n";
    }
}

echo "\n========================================\n";
echo "Dispatch:\n";
$router->dispatch();
?>
