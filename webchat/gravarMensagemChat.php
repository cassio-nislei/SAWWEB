<?php
	// Flag para avisar padrao.inc.php que é uma chamada AJAX
	define('AJAX_CALL', true);
	
	if (session_status() === PHP_SESSION_NONE) {
	    session_start();
	}
	
	require_once(__DIR__ . "/../includes/padrao.inc.php");

	header('Content-Type: application/json');

	try {
		// Rejeitar se não autenticado
		if (!isset($_SESSION["usuariosaw"]["id"]) || intval($_SESSION["usuariosaw"]["id"]) <= 0) {
			http_response_code(401);
			echo json_encode(['success' => false, 'error' => 'Não autenticado']);
			exit;
		}

		// Validação de parâmetros
		if (!isset($_POST["idDepto"]) || (!isset($_POST["strMensagem"]) && !isset($_POST["anexoBase64"]))) {
			throw new Exception('Parâmetros incompletos');
		}

		$idUsuario = intval($_SESSION["usuariosaw"]["id"]);
		$idDepto = intval($_POST["idDepto"] ?? 0);
		$strMensagem = trim($_POST["strMensagem"] ?? "");
		$ehPrivada = intval($_POST["ehPrivada"] ?? 0);
		$idDestinatario = intval($_POST["idDestinatario"] ?? 0);
		$anexoBase64 = $_POST["anexoBase64"] ?? null;
		$anexoNome = $_POST["anexoNome"] ?? null;
		$anexoTipo = $_POST["anexoTipo"] ?? null;

		// Validações
		if (empty($strMensagem) && empty($anexoBase64)) {
			throw new Exception('Mensagem e anexo não podem estar vazios');
		}

		if (!empty($strMensagem) && strlen($strMensagem) > 5000) {
			throw new Exception('Mensagem muito longa (máximo 5000 caracteres)');
		}

		// Se é privada, validar se tem destinatário
		if ($ehPrivada && $idDestinatario <= 0) {
			throw new Exception('Mensagem privada requer um destinatário');
		}

		// Sanitizar mensagem
		$strMensagem = mysqli_real_escape_string($conexao, $strMensagem);

		// Processar anexo se enviado
		$idAnexo = null;
		if (!empty($anexoBase64)) {
			// Validar que é um data URI válido
			if (strpos($anexoBase64, 'data:') !== 0) {
				throw new Exception('Anexo em formato inválido');
			}

			// Validar tamanho (10MB em base64)
			if (strlen($anexoBase64) > 10 * 1024 * 1024) {
				throw new Exception('Arquivo muito grande (máximo 10MB)');
			}

			// Extrair tipo MIME e remover prefixo
			$tipoMIME = $anexoTipo;
			$binarioBase64 = $anexoBase64;
			
			if (preg_match('/^data:([^;]+);base64,(.+)$/', $anexoBase64, $matches)) {
				$tipoMIME = $matches[1];
				$binarioBase64 = $matches[2];
			}

			// Sanitizar nome do arquivo
			$nomeFileSanitizado = preg_replace('/[^a-zA-Z0-9._-]/', '_', $anexoNome ?? 'arquivo');

			// Determinar tipo de arquivo baseado no MIME
			$tipoArquivo = 'ARQUIVO';
			if (strpos($tipoMIME, 'image/') === 0) {
				$tipoArquivo = 'IMAGE';
			} else if (strpos($tipoMIME, 'audio/') === 0) {
				$tipoArquivo = 'AUDIO';
			} else if ($tipoMIME === 'application/pdf') {
				$tipoArquivo = 'PDF';
			}

			// Inserir em tbanexos
			$binarioEscapado = mysqli_real_escape_string($conexao, $binarioBase64);
			$base64Escapado = mysqli_real_escape_string($conexao, $anexoBase64);
			
			$sqlInsertAnexo = "INSERT INTO tbanexos (seq, numero, arquivo, base64, nome_arquivo, nome_original, tipo_arquivo, canal, enviado)
							   VALUES (0, '0', '" . $binarioEscapado . "', '" . $base64Escapado . "', '" . $nomeFileSanitizado . "', '" . $nomeFileSanitizado . "', '" . $tipoArquivo . "', '0', 1)";
			
			if (!mysqli_query($conexao, $sqlInsertAnexo)) {
				throw new Exception('Erro ao salvar anexo: ' . mysqli_error($conexao));
			}
			
			// Obter o ID do anexo inserido
			$idAnexo = mysqli_insert_id($conexao);
		}

		// Validar departamento se informado
		if ($idDepto > 0) {
			$stmtDepto = mysqli_prepare($conexao, "SELECT id FROM tbdepartamentos WHERE id = ? LIMIT 1");
			mysqli_stmt_bind_param($stmtDepto, "i", $idDepto);
			mysqli_stmt_execute($stmtDepto);
			$resultDepto = mysqli_stmt_get_result($stmtDepto);
			
			if (!$resultDepto || mysqli_num_rows($resultDepto) === 0) {
				mysqli_stmt_close($stmtDepto);
				throw new Exception('Departamento inválido');
			}
			mysqli_stmt_close($stmtDepto);
		}

		// Validar destinatário se privada
		if ($ehPrivada && $idDestinatario > 0) {
			$stmtUser = mysqli_prepare($conexao, "SELECT id FROM tbusuario WHERE id = ? LIMIT 1");
			mysqli_stmt_bind_param($stmtUser, "i", $idDestinatario);
			mysqli_stmt_execute($stmtUser);
			$resultUser = mysqli_stmt_get_result($stmtUser);
			
			if (!$resultUser || mysqli_num_rows($resultUser) === 0) {
				mysqli_stmt_close($stmtUser);
				throw new Exception('Operador destinatário inválido');
			}
			mysqli_stmt_close($stmtUser);
		}

		// Inserir mensagem usando prepared statements
		if ($ehPrivada) {
			if ($idAnexo) {
				$stmtInsert = mysqli_prepare($conexao, "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, id_destinatario, eh_privada, id_anexo) VALUES(?, ?, ?, NOW(), ?, 1, ?)");
				$deptoVal = $idDepto > 0 ? $idDepto : null;
				mysqli_stmt_bind_param($stmtInsert, "iisii", $idUsuario, $deptoVal, $strMensagem, $idDestinatario, $idAnexo);
			} else {
				$stmtInsert = mysqli_prepare($conexao, "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, id_destinatario, eh_privada) VALUES(?, ?, ?, NOW(), ?, 1)");
				$deptoVal = $idDepto > 0 ? $idDepto : null;
				mysqli_stmt_bind_param($stmtInsert, "iisi", $idUsuario, $deptoVal, $strMensagem, $idDestinatario);
			}
		} else {
			if ($idAnexo) {
				$stmtInsert = mysqli_prepare($conexao, "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, eh_privada, id_anexo) VALUES(?, ?, ?, NOW(), 0, ?)");
				$deptoVal = $idDepto > 0 ? $idDepto : null;
				mysqli_stmt_bind_param($stmtInsert, "iisi", $idUsuario, $deptoVal, $strMensagem, $idAnexo);
			} else {
				$stmtInsert = mysqli_prepare($conexao, "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, eh_privada) VALUES(?, ?, ?, NOW(), 0)");
				$deptoVal = $idDepto > 0 ? $idDepto : null;
				mysqli_stmt_bind_param($stmtInsert, "iis", $idUsuario, $deptoVal, $strMensagem);
			}
		}

		$insert = mysqli_stmt_execute($stmtInsert);
		mysqli_stmt_close($stmtInsert);

		if (!$insert) {
			error_log('Erro ao salvar mensagem chat: ' . mysqli_error($conexao));
			throw new Exception('Erro ao salvar mensagem');
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