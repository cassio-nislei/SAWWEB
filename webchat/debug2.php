<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    echo json_encode(['step' => '1_definindo_flag']);
    
    define('AJAX_CALL', true);
    
    echo json_encode(['step' => '2_incluindo_padrao']);
    
    require_once(__DIR__ . "/../includes/padrao.inc.php");
    
    echo json_encode(['step' => '3_padrao_incluido', 'conexao_existe' => isset($conexao)]);
    
    if (!isset($_SESSION["usuariosaw"]["id"])) {
        $_SESSION["usuariosaw"]["id"] = 0;
    }
    
    echo json_encode(['step' => '4_sessao_ok', 'usuario_id' => $_SESSION["usuariosaw"]["id"]]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}
?>
