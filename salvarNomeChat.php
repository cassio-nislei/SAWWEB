<?php
session_start();

try {
    // Verificar se usuário está logado
    if (!isset($_SESSION["usuariosaw"]["id"])) {
        echo 'erro_sessao';
        exit;
    }

    // Incluir a conexão com o banco
    require_once("includes/conexao.php");

    // Verificar se conexão foi estabelecida
    if (!isset($conexao) || !$conexao) {
        echo 'erro_conexao: Conexão não disponível';
        exit;
    }

    // Capturar o nome do chat
    $nomeChat = isset($_POST['nome_chat']) ? trim($_POST['nome_chat']) : '';

    // Validar entrada
    if (empty($nomeChat)) {
        echo 'erro_vazio';
        exit;
    }

    // ID do usuário logado
    $usuarioID = intval($_SESSION["usuariosaw"]["id"]);

    // Atualizar o nome no chat no banco de dados
    $sql = "UPDATE tbusuario SET nome_chat = ? WHERE id = ?";
    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        echo 'erro_prepare: ' . $conexao->error;
        exit;
    }

    $stmt->bind_param("si", $nomeChat, $usuarioID);

    if ($stmt->execute()) {
        // Atualizar a sessão também
        $_SESSION["usuariosaw"]["nome_chat"] = $nomeChat;
        echo 'sucesso';
    } else {
        echo 'erro_execute: ' . $stmt->error;
    }

    $stmt->close();

} catch (Exception $e) {
    echo 'erro_excecao: ' . $e->getMessage();
}
?>
