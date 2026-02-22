<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

header('Content-Type: application/json');

try {
    if (!isset($_POST['id_atendimento']) || !isset($_POST['mensagem'])) {
        throw new Exception('Parâmetros incompletos (id_atendimento e mensagem são obrigatórios)');
    }

    $idatendimento = intval($_POST['id_atendimento']);
    $mensagem = mysqli_real_escape_string($conexao, trim($_POST['mensagem']));
    
    if ($idatendimento <= 0) {
        throw new Exception('ID de atendimento inválido');
    }
    
    if (empty($mensagem)) {
        throw new Exception('Mensagem não pode estar vazia');
    }

    $nome_atendente = isset($_SESSION["usuariosaw"]["nome"]) ? $_SESSION["usuariosaw"]["nome"] : 'Atendente';
    $idatendente = isset($_SESSION["usuariosaw"]["id"]) ? intval($_SESSION["usuariosaw"]["id"]) : 0;

    // Buscar o número da conversa
    $queryNum = "SELECT numero FROM tbatendimento WHERE id = '$idatendimento'";
    $resultNum = mysqli_query($conexao, $queryNum);
    
    if (!$resultNum) {
        throw new Exception('Erro ao buscar número da conversa: ' . mysqli_error($conexao));
    }
    
    $rowNum = mysqli_fetch_assoc($resultNum);
    
    if (!$rowNum) {
        throw new Exception('Conversa não encontrada (ID: ' . $idatendimento . ')');
    }
    
    $numero = $rowNum['numero'] ?? '';

    // Buscar próxima sequência
    $querySeq = "SELECT COALESCE(MAX(seq), 0) + 1 as newSeq FROM tbmsgatendimento WHERE id = '$idatendimento' AND canal = 0";
    $resultSeq = mysqli_query($conexao, $querySeq);
    
    if (!$resultSeq) {
        throw new Exception('Erro ao buscar sequência: ' . mysqli_error($conexao));
    }
    
    $rowSeq = mysqli_fetch_assoc($resultSeq);
    $seq = $rowSeq['newSeq'] ?? 1;

    // Inserir mensagem
    $insertMsg = "
        INSERT INTO tbmsgatendimento 
        (id, seq, numero, msg, nome_chat, id_atend, dt_msg, hr_msg, canal, situacao, notificada)
        VALUES 
        ('$idatendimento', '$seq', '$numero', '$mensagem', '$nome_atendente', '$idatendente', CURDATE(), CURTIME(), '0', 'E', true)
    ";

    if (mysqli_query($conexao, $insertMsg)) {
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso',
            'seq' => $seq
        ]);
    } else {
        throw new Exception('Erro ao inserir mensagem: ' . mysqli_error($conexao));
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
