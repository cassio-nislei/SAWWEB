<?php
// Simular requisição POST /auth/login
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/SAW-main/api/v1/auth/login';
$_SERVER['HTTP_AUTHORIZATION'] = '';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Preparar body JSON
$input = json_encode([
    'usuario' => 'admin',
    'senha' => '123456'
]);

// Stream input
$GLOBALS['php_input'] = $input;

// Mock file_get_contents para php://input
function file_get_contents_mock($filename) {
    if ($filename === 'php://input') {
        return $GLOBALS['php_input'];
    }
    return file_get_contents($filename);
}

// Não conseguimos mockar file_get_contents, então vamos tentar outra abordagem
// Incluir o index.php diretamente
ob_start();
include 'api/v1/index.php';
$output = ob_get_clean();

echo $output;
?>
