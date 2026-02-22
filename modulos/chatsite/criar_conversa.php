<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

header('Content-Type: application/json');

try {
    if (!isset($_POST['nome']) || empty($_POST['nome'])) {
        throw new Exception('Nome do cliente é obrigatório');
    }

    $nome = mysqli_real_escape_string($conexao, trim($_POST['nome']));
    
    if (strlen($nome) < 2) {
        throw new Exception('Nome deve ter pelo menos 2 caracteres');
    }

    // Gerar número único (13 dígitos)
    $ascii = implode('', range(0, 9));
    $ascii = str_repeat($ascii, 5);
    $numero = substr(str_shuffle($ascii), 0, 13);

    // Inserir atendimento
    $query = "
        INSERT INTO tbatendimento 
        (numero, nome, situacao, dt_atend, canal)
        VALUES 
        ('$numero', '$nome', 'A', NOW(), '0')
    ";

    if (!mysqli_query($conexao, $query)) {
        throw new Exception('Erro ao criar conversa: ' . mysqli_error($conexao));
    }
    
    // Inserir mensagem de sistema
    $idAtendimento = mysqli_insert_id($conexao);
    
    if ($idAtendimento <= 0) {
        throw new Exception('Erro ao obter ID da conversa criada');
    }
    
    $queryMsgSistema = "
        INSERT INTO tbmsgatendimento 
        (id, seq, numero, msg, nome_chat, id_atend, dt_msg, hr_msg, canal, situacao, notificada)
        VALUES 
        ('$idAtendimento', 1, '$numero', 'Conversa iniciada pelo painel de atendimento.', 'Sistema', 0, CURDATE(), CURTIME(), '0', 'E', true)
    ";
    
    if (!mysqli_query($conexao, $queryMsgSistema)) {
        throw new Exception('Erro ao criar mensagem de sistema: ' . mysqli_error($conexao));
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Conversa criada com sucesso',
        'id' => $idAtendimento,
        'numero' => $numero,
        'nome' => $nome
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
