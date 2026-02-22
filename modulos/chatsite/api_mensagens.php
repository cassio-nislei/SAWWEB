<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID inválido ou não informado');
    }

    $idatendimento = intval($_GET['id']);
    
    if ($idatendimento <= 0) {
        throw new Exception('ID deve ser um número válido');
    }

    // Pega os dados da conversa
    $queryConversa = "SELECT * FROM tbatendimento WHERE id = '$idatendimento'";
    $resultConversa = mysqli_query($conexao, $queryConversa);
    
    if (!$resultConversa) {
        throw new Exception('Erro ao buscar conversa: ' . mysqli_error($conexao));
    }
    
    $conversa = mysqli_fetch_assoc($resultConversa);

    if (!$conversa) {
        throw new Exception('Conversa não encontrada (ID: ' . $idatendimento . ')');
    }

    // Pega as mensagens
    $queryMensagens = "
        SELECT 
            id,
            numero,
            seq,
            msg,
            nome_chat,
            id_atend,
            dt_msg,
            hr_msg
        FROM tbmsgatendimento 
        WHERE id = '$idatendimento' AND canal = 0
        ORDER BY seq ASC
    ";
    
    $resultMensagens = mysqli_query($conexao, $queryMensagens);
    
    if (!$resultMensagens) {
        throw new Exception('Erro ao buscar mensagens: ' . mysqli_error($conexao));
    }
    
    $mensagens = array();

    while ($row = mysqli_fetch_assoc($resultMensagens)) {
        $mensagens[] = $row;
    }

    // Marca as mensagens como notificadas
    $updateQuery = "UPDATE tbmsgatendimento SET notificada = true WHERE id = '$idatendimento' AND canal = 0 AND id_atend > 0";
    mysqli_query($conexao, $updateQuery);

    echo json_encode([
        'success' => true,
        'conversa' => $conversa,
        'mensagens' => $mensagens
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
