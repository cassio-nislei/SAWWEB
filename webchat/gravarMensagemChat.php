<?php
	// Flag para avisar padrao.inc.php que é uma chamada AJAX
	define('AJAX_CALL', true);
	
	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	}
	
	require_once(__DIR__ . "/../includes/padrao.inc.php");

	header('Content-Type: application/json');

	try {
		// Inicializar usuário se não autenticado
		if (!isset($_SESSION["usuariosaw"]["id"])) {
			$_SESSION["usuariosaw"]["id"] = 0;
		}

		// Validação de parâmetros
		if (!isset($_POST["idDepto"]) || !isset($_POST["strMensagem"])) {
			throw new Exception('Parâmetros incompletos');
		}

		$idUsuario = intval($_SESSION["usuariosaw"]["id"]);
		$idDepto = intval($_POST["idDepto"] ?? 0);
		$strMensagem = trim($_POST["strMensagem"] ?? "");
		$ehPrivada = intval($_POST["ehPrivada"] ?? 0);
		$idDestinatario = intval($_POST["idDestinatario"] ?? 0);

		// Validações
		if (empty($strMensagem)) {
			throw new Exception('Mensagem não pode estar vazia');
		}

		if (strlen($strMensagem) > 5000) {
			throw new Exception('Mensagem muito longa (máximo 5000 caracteres)');
		}

		// Se é privada, validar se tem destinatário
		if ($ehPrivada && $idDestinatario <= 0) {
			throw new Exception('Mensagem privada requer um destinatário');
		}

		// Sanitizar mensagem
		$strMensagem = mysqli_real_escape_string($conexao, $strMensagem);

		// Validar departamento se informado
		if ($idDepto > 0) {
			$sqlValidaDepto = "SELECT id FROM tbdepartamentos WHERE id = '$idDepto' LIMIT 1";
			$resultDepto = mysqli_query($conexao, $sqlValidaDepto);
			
			if (!$resultDepto || mysqli_num_rows($resultDepto) === 0) {
				throw new Exception('Departamento inválido');
			}
		}

		// Validar destinatário se privada
		if ($ehPrivada && $idDestinatario > 0) {
			$sqlValidaUser = "SELECT id FROM tbusuario WHERE id = '$idDestinatario' LIMIT 1";
			$resultUser = mysqli_query($conexao, $sqlValidaUser);
			
			if (!$resultUser || mysqli_num_rows($resultUser) === 0) {
				throw new Exception('Operador destinatário inválido');
			}
		}

		// Montar e executar INSERT
		if ($ehPrivada) {
			// Mensagem privada
			$sqlInsert = "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, id_destinatario, eh_privada)
						  VALUES('" . $idUsuario . "', " . ($idDepto > 0 ? "'" . $idDepto . "'" : "NULL") . ", '" . $strMensagem . "', NOW(), '" . $idDestinatario . "', 1)";
		} else {
			// Mensagem pública por departamento
			if ($idDepto > 0) {
				$sqlInsert = "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, eh_privada)
							  VALUES('" . $idUsuario . "', '" . $idDepto . "', '" . $strMensagem . "', NOW(), 0)";
			} else {
				$sqlInsert = "INSERT INTO tbchatoperadores(id_usuario, mensagem, data_hora, eh_privada)
							  VALUES('" . $idUsuario . "', '" . $strMensagem . "', NOW(), 0)";
			}
		}

		$insert = mysqli_query($conexao, $sqlInsert);

		if (!$insert) {
			throw new Exception('Erro ao salvar mensagem: ' . mysqli_error($conexao));
		}

		// Responder com sucesso
		echo json_encode([
			'success' => true,
			'message' => 'Mensagem enviada com sucesso'
		]);

	} catch (Exception $e) {
		http_response_code(400);
		echo json_encode([
			'success' => false,
			'error' => $e->getMessage()
		]);
	}
?>