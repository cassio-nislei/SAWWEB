<?php
// Teste direto de roteamento
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular requisição POST /auth/login
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/SAW-main/api/v1/auth/login';

// Incluir Router
require_once 'api/v1/Router.php';

$router = new Router();

// Testar construção do caminho
echo "Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";

// Usar reflexão para acessar currentPath privada
$reflection = new ReflectionClass($router);
$property = $reflection->getProperty('currentPath');
$property->setAccessible(true);
echo "CurrentPath: " . $property->getValue($router) . "\n";

// Testar se caminho está correto
if ($property->getValue($router) === '/auth/login') {
    echo "✅ Caminho correto!\n";
} else {
    echo "❌ Caminho incorreto\n";
}
?>
