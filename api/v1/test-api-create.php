<?php
/**
 * Teste da API - Criar Atendimento
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
    // Dados
    $numero = "11999999995";
    $nome = "Cliente Teste 5";
    $idAtende = "1";
    $nomeAtende = "Operador 1";
    $situacao = "P";
    $canal = 1;
    $setor = 1;

    // Debug
    error_log("Tentando criar atendimento...");
    
    $result = Atendimento::create($numero, $nome, $idAtende, $nomeAtende, $situacao, $canal, $setor);

    if ($result) {
        Response::success($result, "Atendimento criado com sucesso", 201);
    } else {
        error_log("Erro ao criar: " . Database::getLastError());
        Response::error("Erro ao criar atendimento: " . Database::getLastError(), 500);
    }

    Database::close();

} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    Response::internalError("Erro: " . $e->getMessage());
}
?>
