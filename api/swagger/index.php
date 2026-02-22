<?php
/**
 * SAW API - Swagger Documentation Server
 * 
 * Este arquivo serve a documentação Swagger UI da API
 * Acesse em: http://localhost/SAW-main/api/swagger/
 * 
 * Data: 19/11/2025
 */

header('Content-Type: application/json; charset=utf-8');

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Obtém o path solicitado
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rotas suportadas
if ($request === '/SAW-main/api/swagger/' || $request === '/SAW-main/api/swagger/index.php') {
    // Retorna a página HTML
    header('Content-Type: text/html; charset=utf-8');
    include __DIR__ . '/swagger-ui.html';
    exit;
}

if ($request === '/SAW-main/api/swagger/swagger.json' || $request === '/SAW-main/api/swagger/spec.json') {
    // Retorna o arquivo Swagger JSON
    header('Content-Type: application/json; charset=utf-8');
    include __DIR__ . '/swagger.json';
    exit;
}

// Se nenhuma rota corresponde, redireciona para a UI
header('Location: ' . dirname($_SERVER['REQUEST_URI']) . '/');
exit;
?>
