<?php
	require_once("../includes/padrao.inc.php");
	
	header('Content-Type: application/json; charset=utf-8');

	try {
		// Verificar autenticação
		if (empty($_SESSION["usuariosaw"]["id"])) {
			throw new Exception('Usuário não autenticado');
		}

		$usuarioId = $_SESSION["usuariosaw"]["id"];
		$msgId = intval($_POST['id'] ?? 0);

		// Validar parâmetros
		if ($msgId <= 0) {
			throw new Exception('ID inválido');
		}

		// Verificar se a mensagem existe e é do usuário
		$queryCheck = "SELECT id, id_usuario FROM tbchatoperadores WHERE id = '$msgId'";
		$resultCheck = mysqli_query($conexao, $queryCheck);
		
		if (!$resultCheck) {
			throw new Exception('Erro ao validar mensagem: ' . mysqli_error($conexao));
		}

		$mensagem = mysqli_fetch_assoc($resultCheck);
		
		if (!$mensagem) {
			throw new Exception('Mensagem não encontrada');
		}

		if ($mensagem['id_usuario'] != $usuarioId) {
			throw new Exception('Você não tem permissão para deletar esta mensagem');
		}

		// Deletar mensagem
		$deleteQuery = "DELETE FROM tbchatoperadores WHERE id = '$msgId'";
		
		if (!mysqli_query($conexao, $deleteQuery)) {
			throw new Exception('Erro ao deletar: ' . mysqli_error($conexao));
		}

		http_response_code(200);
		echo json_encode(['success' => true, 'message' => 'Mensagem deletada com sucesso']);

	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode(['success' => false, 'error' => $e->getMessage()]);
	}
?>
