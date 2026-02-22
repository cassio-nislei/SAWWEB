<?php
/**
 * Teste direto de criação de atendimento
 */

define('DB_HOST', '104.234.173.105');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');
define('DB_CHARSET', 'utf8mb4');
define('API_VERSION', '1.0');

header('Content-Type: application/json; charset=utf-8');

// Carrega classes
require_once 'Database.php';
require_once 'Response.php';
require_once 'models/Atendimento.php';

try {
    // Dados do teste
    $numero = "11987654323";
    $nome = "Teste Cliente 3";
    $idAtende = "1";
    $nomeAtende = "Operador 1";
    $situacao = "P";
    $canal = 1;
    $setor = 1;

    // Tenta criar
    $result = Atendimento::create($numero, $nome, $idAtende, $nomeAtende, $situacao, $canal, $setor);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Atendimento criado com sucesso',
            'data' => $result
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao criar: ' . Database::getLastError(),
            'data' => null
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    Database::close();

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
