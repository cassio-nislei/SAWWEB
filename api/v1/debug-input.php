<?php
// Teste de debug - mostrar o que está chegando

// Cache do input uma única vez
if (!isset($GLOBALS['_php_input_cache'])) {
    $GLOBALS['_php_input_cache'] = file_get_contents('php://input');
}

header('Content-Type: application/json');

$response = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? 'not set',
    'input_raw' => $GLOBALS['_php_input_cache'],
    'input_decoded' => json_decode($GLOBALS['_php_input_cache'], true),
    'input_length' => strlen($GLOBALS['_php_input_cache']),
    'get_data' => $_GET,
    'post_data' => $_POST
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
