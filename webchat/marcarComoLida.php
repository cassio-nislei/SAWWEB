<?php
	// Endpoint que marca uma mensagem privada como lida
	header('Content-Type: application/json; charset=utf-8');

	// Flag para avisar padrao.inc.php que é uma chamada AJAX
	define('AJAX_CALL', true);

	// Incluir conexão com banco de dados
	require_once(__DIR__ . "/../includes/padrao.inc.php");

	try {
		// Inicializar usuário se não autenticado
		if (!isset($_SESSION["usuariosaw"]["id"])) {
			$_SESSION["usuariosaw"]["id"] = 0;
		}

		// Validar parâmetros
		if (!isset($_POST["id_mensagem"])) {
			throw new Exception('ID da mensagem não informado');
		}

		$idMensagem = intval($_POST["id_mensagem"]);
		$usuarioAtualId = intval($_SESSION["usuariosaw"]["id"]);

		// Validar se a mensagem existe e o usuário é o destinatário
		$sqlVerifica = "SELECT id, eh_privada, id_destinatario FROM tbchatoperadores 
						WHERE id = '" . $idMensagem . "' AND id_destinatario = '" . $usuarioAtualId . "' LIMIT 1";
		
		$resultVerifica = mysqli_query($conexao, $sqlVerifica);

		if (!$resultVerifica || mysqli_num_rows($resultVerifica) === 0) {
			throw new Exception('Mensagem não encontrada ou você não é o destinatário');
		}

		// Marcar como lida
		$sqlUpdate = "UPDATE tbchatoperadores 
					  SET visualizado = 1, data_leitura = NOW() 
					  WHERE id = '" . $idMensagem . "' AND id_destinatario = '" . $usuarioAtualId . "'";

		$resultUpdate = mysqli_query($conexao, $sqlUpdate);

		if (!$resultUpdate) {
			throw new Exception('Erro ao marcar mensagem como lida: ' . mysqli_error($conexao));
		}

		echo json_encode([
			'success' => true,
			'message' => 'Mensagem marcada como lida'
		]);

	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'error' => $e->getMessage()
		]);
	}
?>
