<?php
	require_once("../includes/padrao.inc.php");

	// Logging detalhado
	error_log("=== ANEXO.PHP INICIADO ===");
	error_log("GET params: " . json_encode($_GET));

	// Declaração de Variáveis //
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$numero = isset($_GET['numero']) ? $_GET['numero'] : '';
		$seq = isset($_GET['seq']) ? intval($_GET['seq']) : 0;
	// FIM Declaração de Variáveis //

	// Buscando os dados do Arquivo com prepared statement //
		$stmtAnexos = mysqli_prepare($conexao, "SELECT id, arquivo, nome_arquivo, tipo_arquivo, base64, LENGTH(arquivo) as arquivo_size, LENGTH(base64) as base64_size FROM tbanexos WHERE id = ? AND numero = ? AND seq = ?");
		mysqli_stmt_bind_param($stmtAnexos, "isi", $id, $numero, $seq);
		mysqli_stmt_execute($stmtAnexos);
		$qryAnexos = mysqli_stmt_get_result($stmtAnexos);
		if (!$qryAnexos) {
			die("Erro ao buscar anexo");
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
		// $imagem = explode(".", $objAnexos->nome_arquivo);
		// $fileName = "images/conversas/" . str_replace($imagem[0], $id.'_'.$numero.'_'.$seq, $objAnexos->nome_arquivo);

		// if( !file_exists($fileName) ){
		// 	$img = imagecreatefromstring( $objAnexos->arquivo );	
		// 	imagejpeg( $img, $fileName );
		// }
		
		// header( "Content-type: image/jpeg" );
		// header( sprintf( "Content-length: %d" , strlen( $objAnexos->arquivo ) ) );
	}
	// Áudio em Voz (PTT) - Base64 armazenado //
	elseif( $objAnexos->tipo_arquivo == 'PTT' ){
		error_log("Processando audio PTT. Tamanho arquivo_size: " . $objAnexos->arquivo_size . " bytes");
		
		// Definir cache para 30 dias
		$cacheTime = 30 * 24 * 60 * 60; // 30 dias em segundos
		header('Cache-Control: public, max-age=' . $cacheTime);
		header('Pragma: cache');
		$gmdate = gmdate('D, d M Y H:i:s', time() + $cacheTime) . ' GMT';
		header('Expires: ' . $gmdate);
		
		// Se o arquivo é Base64, decodificamos
		$audioData = $objAnexos->arquivo;
		
		// Verificar se é Base64 válido
		if (preg_match('~^[A-Za-z0-9+/]*={0,2}$~', $audioData)) {
			// Base64 puro, decodificar com strict mode
			$decodedAudio = base64_decode($audioData, true);
			if ($decodedAudio !== false && strlen($decodedAudio) > 0) {
				header('Content-Type: audio/mpeg');
				header('Content-Length: ' . strlen($decodedAudio));
				ob_end_clean();
				flush();
				echo $decodedAudio;
				exit;
			}
		}
		
		// Tentar como data URI
		if (strpos($audioData, 'data:audio') === 0) {
			$base64Part = substr($audioData, strpos($audioData, ',') + 1);
			$decodedAudio = base64_decode($base64Part, true);
			if ($decodedAudio !== false && strlen($decodedAudio) > 0) {
				header('Content-Type: audio/mpeg');
				header('Content-Length: ' . strlen($decodedAudio));
				ob_end_clean();
				flush();
				echo $decodedAudio;
				exit;
			}
		}
		
		// Fallback: usar coluna base64 se disponível
		if (!empty($objAnexos->base64)) {
			$base64Data = $objAnexos->base64;
			if (strpos($base64Data, 'data:audio') === 0) {
				$base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
			}
			$decodedAudio = base64_decode($base64Data, true);
			if ($decodedAudio !== false && strlen($decodedAudio) > 0) {
				header('Content-Type: audio/mpeg');
				header('Content-Length: ' . strlen($decodedAudio));
				ob_end_clean();
				flush();
				echo $decodedAudio;
				exit;
			}
		}
		
		// Se tudo falhou, retornar erro
		header('Content-Type: text/plain');
		echo "Erro: Audio nao disponivel";
		exit;
	}
	// Arquivo de Áudio //
	elseif( $objAnexos->tipo_arquivo == 'AUDIO' ){
		header('Content-type: audio/mpeg');
	}
	// Arquivo de Vídeo //
	elseif( $objAnexos->tipo_arquivo == 'VIDEO' ){
		header("Content-Type: video/mp4");
	}
	// Demais tipos de arquivo (DOCUMENT, APPLI, TEXT/, etc.) //
	else{
		// Decodificar base64 para binário
		$fileData = null;
		$nomeArquivo = $objAnexos->nome_arquivo;
		$ext = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));
		
		// Mapear extensão para MIME type
		$mimeTypes = array(
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
		$contentType = isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : 'application/octet-stream';
		
		// Tentar decodificar de base64 (coluna base64 tem data URI, coluna arquivo tem base64 puro)
		if (!empty($objAnexos->base64)) {
			$b64 = $objAnexos->base64;
			if (strpos($b64, 'data:') === 0) {
				$b64 = substr($b64, strpos($b64, ',') + 1);
			}
			$decoded = base64_decode($b64, true);
			if ($decoded !== false && strlen($decoded) > 0) {
				$fileData = $decoded;
			}
		}
		
		if ($fileData === null && !empty($objAnexos->arquivo)) {
			// Tentar como base64 puro
			if (preg_match('~^[A-Za-z0-9+/\r\n]+=*$~', substr($objAnexos->arquivo, 0, 200))) {
				$decoded = base64_decode($objAnexos->arquivo, true);
				if ($decoded !== false && strlen($decoded) > 0) {
					$fileData = $decoded;
				}
			}
			// Tentar como data URI
			if ($fileData === null && strpos($objAnexos->arquivo, 'data:') === 0) {
				$b64part = substr($objAnexos->arquivo, strpos($objAnexos->arquivo, ',') + 1);
				$decoded = base64_decode($b64part, true);
				if ($decoded !== false && strlen($decoded) > 0) {
					$fileData = $decoded;
				}
			}
			// Fallback: dados binários brutos
			if ($fileData === null) {
				$fileData = $objAnexos->arquivo;
			}
		}
		
		if ($fileData === null) {
			header('Content-Type: text/plain');
			echo 'Erro: Arquivo nao disponivel';
			exit;
		}
		
		// Tipos que o navegador pode exibir inline (abrir em nova aba)
		$inlineTypes = array('pdf', 'html', 'htm', 'txt', 'svg', 'xml', 'json', 'png', 'jpg', 'jpeg', 'gif', 'webp');
		
		header('Content-Type: ' . $contentType);
		header('Content-Length: ' . strlen($fileData));
		
		if (in_array($ext, $inlineTypes)) {
			// Abrir inline no navegador
			header('Content-Disposition: inline; filename="' . basename($nomeArquivo) . '"');
		} else {
			// Forçar download para tipos não visualizáveis
			header('Content-Disposition: attachment; filename="' . basename($nomeArquivo) . '"');
		}
		
		ob_end_clean();
		flush();
		echo $fileData;
		exit;
	}

	// Fallback para tipos não tratados acima (AUDIO, VIDEO sem exit)
	ob_end_clean();
	flush();
	echo $objAnexos->arquivo;