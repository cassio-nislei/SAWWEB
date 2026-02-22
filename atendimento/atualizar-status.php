<?php
require_once("../includes/padrao.inc.php");

// Receber dados
$numero = isset($_POST['numero']) ? $_POST['numero'] : null;
$situacao = isset($_POST['situacao']) ? $_POST['situacao'] : null;

// Validar dados
if (!$numero || !$situacao) {
    http_response_code(400);
    echo json_encode(['erro' => 'Parâmetros inválidos']);
    exit;
}

try {
    // Atualizar status do atendimento
    $sql = "UPDATE tbatendimento 
            SET situacao = :situacao 
            WHERE numero = :numero";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':situacao', $situacao, PDO::PARAM_STR);
    $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
    
    $result = $stmt->execute();
    
    if ($result) {
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado para ANDAMENTO']);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Erro ao atualizar status']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao processar: ' . $e->getMessage()]);
}
?>
