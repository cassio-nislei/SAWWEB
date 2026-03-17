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
		error_log("Processando audio AUDIO (recebido). Tamanho arquivo_size: " . $objAnexos->arquivo_size . " bytes");
		
		// Definir cache para 30 dias
		$cacheTime = 30 * 24 * 60 * 60; // 30 dias em segundos
		header('Cache-Control: public, max-age=' . $cacheTime);
		header('Pragma: cache');
		$gmdate = gmdate('D, d M Y H:i:s', time() + $cacheTime) . ' GMT';
		header('Expires: ' . $gmdate);
		
		// Se o arquivo é Base64, decodificamos (mesmo tratamento que PTT)
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
	// Arquivo de Vídeo //
	elseif( $objAnexos->tipo_arquivo == 'VIDEO' ){
		header("Content-Type: video/mp4");
	}
	// Áudio em Voz //
	else{
		// Faz o Download do Arquivo //
		header('Content-Description: File Transfer');
		header("Content-Type: application/octet-stream");
		// header("Content-Type: audio/ogg");
		header("Content-Disposition: attachment; filename=".basename($objAnexos->nome_arquivo));
		header("Content-Transfer-Encoding: binary");
	}

	// Essas duas linhas antes do readfile - de imprimir o arquivo //
	ob_end_clean();
	flush();

	// Imprimindo o Arquivo //
	echo $objAnexos->arquivo;