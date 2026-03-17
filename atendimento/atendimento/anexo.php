<?php
	require_once("../includes/padrao.inc.php");

	// Logging detalhado
	error_log("=== ANEXO.PHP INICIADO ===");
	error_log("GET params: " . json_encode($_GET));

	// Declaração de Variáveis //
		$id = isset($_GET['id']) ? $_GET['id'] : null;
		$numero = isset($_GET['numero']) ? $_GET['numero'] : null;
		$seq = isset($_GET['seq']) ? $_GET['seq'] : null;
	// FIM Declaração de Variáveis //

	// Função para decodificar base64 de múltiplas fontes
	function decodeFileData($arquivo, $base64) {
		// Tentar coluna base64 (tem data URI)
		if (!empty($base64)) {
			$b64 = $base64;
			if (strpos($b64, 'data:') === 0) {
				$b64 = substr($b64, strpos($b64, ',') + 1);
			}
			$decoded = base64_decode($b64, true);
			if ($decoded !== false && strlen($decoded) > 0) {
				return $decoded;
			}
		}
		
		// Tentar coluna arquivo como base64 puro
		if (!empty($arquivo)) {
			// Verificar bases64 válido
			if (preg_match('~^[A-Za-z0-9+/\r\n]+=*$~', substr($arquivo, 0, 200))) {
				$decoded = base64_decode($arquivo, true);
				if ($decoded !== false && strlen($decoded) > 0) {
					return $decoded;
				}
			}
			// Tentar como data URI
			if (strpos($arquivo, 'data:') === 0) {
				$b64part = substr($arquivo, strpos($arquivo, ',') + 1);
				$decoded = base64_decode($b64part, true);
				if ($decoded !== false && strlen($decoded) > 0) {
					return $decoded;
				}
			}
		}
		
		return null;
	}

	error_log("id=$id, numero=$numero, seq=$seq");

	// Buscando os dados do Arquivo //
		$strAnexos = "SELECT id, arquivo, nome_arquivo, tipo_arquivo, base64, LENGTH(arquivo) as arquivo_size, LENGTH(base64) as base64_size FROM tbanexos WHERE id = '".$id."' AND numero = '".$numero."' AND seq = '".$seq."'";
		error_log("Query: " . $strAnexos);
		
		$qryAnexos = mysqli_query($conexao, $strAnexos);
		if (!$qryAnexos) {
			error_log("✗ Erro na query: " . mysqli_error($conexao));
			die("Erro ao buscar anexo: " . mysqli_error($conexao));
		}
		
		$objAnexos = mysqli_fetch_object($qryAnexos);
		if (!$objAnexos) {
			error_log("✗ Nenhum registro encontrado para id=$id, numero=$numero, seq=$seq");
			die("Arquivo não encontrado");
		}
		
		error_log("✓ Anexo encontrado: tipo=" . $objAnexos->tipo_arquivo . ", arquivo_size=" . $objAnexos->arquivo_size . ", base64_size=" . $objAnexos->base64_size);
	// FIM Buscando os dados do Arquivo //

	// Imagem //
	if( $objAnexos->tipo_arquivo == 'IMAGE' ){
		error_log("Processando IMAGE. Decodificando base64...");
		
		$cacheTime = 30 * 24 * 60 * 60; // 30 dias em segundos
		header('Cache-Control: public, max-age=' . $cacheTime);
		header('Pragma: cache');
		$gmdate = gmdate('D, d M Y H:i:s', time() + $cacheTime) . ' GMT';
		header('Expires: ' . $gmdate);
		
		$fileData = decodeFileData($objAnexos->arquivo, $objAnexos->base64);
		if ($fileData === null) {
			header('Content-Type: text/plain');
			echo "Erro: Imagem não disponível";
			exit;
		}
		
		header('Content-Type: image/jpeg');
		header('Content-Length: ' . strlen($fileData));
		ob_end_clean();
		flush();
		echo $fileData;
		exit;
	}
	// Áudio em Voz (PTT) ou Áudio Recebido (AUDIO) ou Vídeo ou demais arquivos
	else{
		error_log("Processando tipo: " . $objAnexos->tipo_arquivo . ". Decodificando base64...");
		
		$cacheTime = 30 * 24 * 60 * 60; // 30 dias em segundos
		header('Cache-Control: public, max-age=' . $cacheTime);
		header('Pragma: cache');
		$gmdate = gmdate('D, d M Y H:i:s', time() + $cacheTime) . ' GMT';
		header('Expires: ' . $gmdate);
		
		$nomeArquivo = $objAnexos->nome_arquivo;
		$ext = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
		
		// Mapear tipo/extensão para MIME type
		$tipoMime = array(
			'PTT'   => 'audio/mpeg',
			'AUDIO' => 'audio/mpeg',
			'VIDEO' => 'video/mp4',
		);
		
		$extensionMimeTypes = array(
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xls'  => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'csv'  => 'text/csv',
			'ppt'  => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'txt'  => 'text/plain',
			'html' => 'text/html',
			'htm'  => 'text/html',
			'xml'  => 'application/xml',
			'json' => 'application/json',
			'svg'  => 'image/svg+xml',
			'png'  => 'image/png',
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'gif'  => 'image/gif',
			'webp' => 'image/webp',
			'zip'  => 'application/zip',
			'rar'  => 'application/x-rar-compressed',
			'mp3'  => 'audio/mpeg',
			'mp4'  => 'video/mp4',
		);
		
		// Determinar MIME type
		$contentType = isset($tipoMime[$objAnexos->tipo_arquivo]) 
			? $tipoMime[$objAnexos->tipo_arquivo]
			: (isset($extensionMimeTypes[$ext]) ? $extensionMimeTypes[$ext] : 'application/octet-stream');
		
		$fileData = decodeFileData($objAnexos->arquivo, $objAnexos->base64);
		if ($fileData === null) {
			header('Content-Type: text/plain');
			echo 'Erro: Arquivo não disponível';
			exit;
		}
		
		// Tipos que o navegador pode exibir inline
		$inlineTypes = array('pdf', 'html', 'htm', 'txt', 'svg', 'xml', 'json', 'png', 'jpg', 'jpeg', 'gif', 'webp');
		
		header('Content-Type: ' . $contentType);
		header('Content-Length: ' . strlen($fileData));
		
		if (in_array($ext, $inlineTypes) || in_array($objAnexos->tipo_arquivo, array('PTT', 'AUDIO', 'VIDEO'))) {
			// Abrir inline no navegador (áudio e vídeo com controles)
			header('Content-Disposition: inline; filename="' . basename($nomeArquivo) . '"');
		} else {
			// Download
			header('Content-Disposition: attachment; filename="' . basename($nomeArquivo) . '"');
		}
		
		ob_end_clean();
		flush();
		echo $fileData;
		exit;
	}