<?php
	require_once("../includes/padrao.inc.php");
	
	// ======== FUNÇÕES PARA PROCESSAMENTO DE ÁUDIO ========
	
	/**
	 * Detecta o tipo real de arquivo de áudio analisando magic bytes
	 */
	function detectaTipoAudio($binario) {
		if (empty($binario) || strlen($binario) < 4) {
			return ["tipo" => "desconhecido", "ext" => "bin", "valido" => false];
		}
		
		$primeirosByte = substr($binario, 0, 12);
		$hex = bin2hex($primeirosByte);
		
		// MP3: FF FA ou FF FB (MPEG Audio)
		if (strpos($hex, "fffa") === 0 || strpos($hex, "fffb") === 0) {
			return ["tipo" => "mp3", "ext" => "mp3", "valido" => true];
		}
		// WebM: 1A 45 DF A3
		else if (strpos($hex, "1a45dfa3") === 0) {
			return ["tipo" => "webm", "ext" => "webm", "valido" => true];
		}
		// OGG/Opus: 4F 67 67 53 (OggS)
		else if (strpos($hex, "4f676753") === 0) {
			return ["tipo" => "ogg", "ext" => "ogg", "valido" => true];
		}
		// WAV: 52 49 46 46 (RIFF)
		else if (strpos($hex, "52494646") === 0) {
			return ["tipo" => "wav", "ext" => "wav", "valido" => true];
		}
		// M4A/MP4: ftypisom ou ftypmp4a
		else if (preg_match('/ftypisom|ftypmp4a/i', substr($binario, 4, 8))) {
			return ["tipo" => "m4a", "ext" => "m4a", "valido" => true];
		}
		// Fallback: verificar se tem padrão OGG em outras posições (às vezes não está no início)
		else if (strpos($hex, "4f676753") !== false) {
			return ["tipo" => "ogg", "ext" => "ogg", "valido" => true];
		}
		else {
			return ["tipo" => "desconhecido", "ext" => "bin", "valido" => false];
		}
	}
	
	/**
	 * Valida se o arquivo é grande o suficiente para ser áudio válido
	 */
	function validaTamanhoAudio($tamanho, $tipo) {
		// Tamanho mínimo: 300 bytes (OGG pode ser menor)
		if ($tamanho < 300) {
			return ["valido" => false, "msg" => "Arquivo muito pequeno ($tamanho bytes)"];
		}
		
		// MP3 128kbps tem ~16KB por segundo, mínimo ~2 segundos
		if ($tipo === "mp3" && $tamanho < 8000) {
			return ["valido" => false, "msg" => "MP3 muito pequeno, possível corrupção"];
		}
		
		// OGG Opus tem ~1KB por segundo, mínimo ~300 bytes
		if ($tipo === "ogg" && $tamanho < 300) {
			return ["valido" => false, "msg" => "OGG muito pequeno, possível corrupção"];
		}
		
		return ["valido" => true, "msg" => "OK"];
	}

	// ======== FIM FUNÇÕES DE ÁUDIO ========
	
	// Declaração de Variáveis //
		$strNumero = $_POST["numero"];
		$idAtendimento = $_POST["id_atendimento"];
		$idCanal = isset($_POST["id_canal"]) ? $_POST["id_canal"] : "";
		$strMensagem = $_POST["msg"];
		$strResposta = $_POST["Resposta"];
		$idResposta  = $_POST["idResposta"];
		$nomeDepartamento = $_SESSION["usuariosaw"]["nomeDepartamento"];
		$binario = '';
		$nomeArquivo = '';
		$anexomsgRapida = isset($_POST["anexomsgRapida"]) ? $_POST["anexomsgRapida"] : "0";
		$tipo = '';
		$situacao    = 'E'; 
		$intUserId   = $_SESSION["usuariosaw"]["id"];
		$strUserNome = $_SESSION["usuariosaw"]["nome"];
		
	// Declaração de Variáveis //

    //exibir o nome do Atendente em cada mensagem enviada
    if ($_SESSION["parametros"]["nome_atendente"] && $_SESSION["parametros"]["departamento_atendente"]){	
		$strMensagem = quebraDeLinha("*".$strUserNome." [".$nomeDepartamento."]* <br>". $strMensagem ) ;	
	}else if($_SESSION["parametros"]["nome_atendente"]){
		$strMensagem = quebraDeLinha("*".$strUserNome."* <br>". $strMensagem ) ;
	}else if($_SESSION["parametros"]["departamento_atendente"]){
        $strMensagem = quebraDeLinha("*".$nomeDepartamento."* <br>". $strMensagem ) ;
	}
	else{ $strMensagem = quebraDeLinha($strMensagem); }

	// Verifica se existe Áudio em Base64 //
	if( isset($_POST["uploadBase64"]) && !empty($_POST["uploadBase64"]) ){
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal);
		
		// Decodificar Base64
		$base64Audio = $_POST["uploadBase64"];
		// Remover o prefixo "data:audio/xxx;base64," se existir
		if (strpos($base64Audio, 'data:') === 0) {
			$base64Audio = preg_replace('/^data:audio\/.+;base64,/', '', $base64Audio);
		}
		
		$binario = base64_decode($base64Audio);
		$audioType = isset($_POST["audioType"]) ? $_POST["audioType"] : 'audio/webm';
		
		// DEBUG - Log do tipo recebido
		error_log("\n" . str_repeat("=", 80));
		error_log("🎵 ÁUDIO RECEBIDO - INICIANDO PROCESSAMENTO");
		error_log("   Tipo MIME Recebido: " . $audioType);
		error_log("   ID Atendimento: " . $idAtendimento);
		error_log("   Tamanho Binário Decodificado: " . strlen($binario) . " bytes");
		error_log("   Sequência: " . $newSequence);
		error_log(str_repeat("=", 80));
		
		// PASSO 0: Detectar tipo real do áudio recebido
		$tipoDetectado = detectaTipoAudio($binario);
		error_log("🔍 Tipo Real Detectado: " . $tipoDetectado['tipo'] . " (extensão: ." . $tipoDetectado['ext'] . ")");
		error_log("   Válido: " . ($tipoDetectado['valido'] ? "Sim ✅" : "Não ❌"));
		
		// Validar tamanho
		$validacaoTamanho = validaTamanhoAudio(strlen($binario), $tipoDetectado['tipo']);
		error_log("📊 Validação de Tamanho: " . $validacaoTamanho['msg']);
		
		$ext = 'webm'; // padrão (fallback)
		$convertido = false;
		
		// Se já é MP3 válido, não precisa converter!
		if ($tipoDetectado['tipo'] === 'mp3' && $tipoDetectado['valido'] && $validacaoTamanho['valido']) {
			error_log("✅ Arquivo já é MP3 válido! Salvando direto sem conversão.");
			$ext = 'mp3';
		}
		// Se é OGG válido, salvar direto (Android gera OGG de qualidade!)
		else if ($tipoDetectado['tipo'] === 'ogg' && $tipoDetectado['valido'] && $validacaoTamanho['valido']) {
			error_log("✅ Arquivo é OGG válido! Salvando direto sem conversão.");
			error_log("   OGG gerado pelo Android é compatível com WhatsApp.");
			$ext = 'ogg';
		}
		// Se é WebM, tentar converter para MP3
		else if ($tipoDetectado['tipo'] === 'webm') {
			error_log("⏳ Arquivo é WebM. Tentando converter para MP3 para compatibilidade...");
			
			$tempDir = sys_get_temp_dir();
			$tempWebmFile = $tempDir . '/audio_' . uniqid() . '.webm';
			$tempMp3File = $tempDir . '/audio_' . uniqid() . '.mp3';
			
			$bytesEscritos = @file_put_contents($tempWebmFile, $binario);
			error_log("   📁 Arquivo WebM temp: " . $tempWebmFile . " (" . $bytesEscritos . " bytes)");
			
			if ($bytesEscritos > 0 && file_exists($tempWebmFile)) {
				// Tentar converter com FFmpeg
				$ffmpegPath = trim(@shell_exec('which ffmpeg 2>/dev/null')) ?: 'ffmpeg';
				$ffmpegCheck = @shell_exec($ffmpegPath . ' -version 2>&1 | head -1');
				
				if (!empty($ffmpegCheck) && strpos($ffmpegCheck, 'ffmpeg') !== false) {
					error_log("   ✅ FFmpeg encontrado: " . trim($ffmpegCheck));
					
					// Converter WebM para MP3 com qualidade boa (128kbps)
					$command = $ffmpegPath . ' -loglevel quiet -i ' . escapeshellarg($tempWebmFile) . 
					           ' -q:a 5 -b:a 128k -y ' . escapeshellarg($tempMp3File) . ' 2>&1';
					
					error_log("   🔄 Executando conversão FFmpeg...");
					$output = @shell_exec($command);
					
					if (file_exists($tempMp3File)) {
						$mp3Size = @filesize($tempMp3File);
						error_log("   📊 MP3 criado: " . $mp3Size . " bytes");
						
						if ($mp3Size > 500) {
							$binario = @file_get_contents($tempMp3File);
							if ($binario !== false && strlen($binario) > 500) {
								$ext = 'mp3';
								$convertido = true;
								error_log("   ✅ SUCESSO: Convertido para MP3 (" . strlen($binario) . " bytes)");
								error_log("   🎵 Agora compatível com Android!");
							}
						}
					}
				} else {
					error_log("   ❌ FFmpeg NÃO ENCONTRADO!");
					error_log("   ⚠️  AVISO: Sem FFmpeg, áudio será salvo como WebM");
					error_log("   🔧 SOLUÇÃO: Instale FFmpeg no servidor (apt-get install ffmpeg)");
					$ext = 'webm'; // Fallback
				}
			}
			
			// Limpar arquivos temporários
			@unlink($tempWebmFile);
			@unlink($tempMp3File);
		}
		// Se é WAV, converter para MP3
		else if ($tipoDetectado['tipo'] === 'wav') {
			error_log("⏳ Arquivo é WAV. Tentando converter para MP3...");
			
			$tempDir = sys_get_temp_dir();
			$tempWavFile = $tempDir . '/audio_' . uniqid() . '.wav';
			$tempMp3File = $tempDir . '/audio_' . uniqid() . '.mp3';
			
			$bytesEscritos = @file_put_contents($tempWavFile, $binario);
			
			if ($bytesEscritos > 0 && file_exists($tempWavFile)) {
				$ffmpegPath = trim(@shell_exec('which ffmpeg 2>/dev/null')) ?: 'ffmpeg';
				$ffmpegCheck = @shell_exec($ffmpegPath . ' -version 2>&1 | head -1');
				
				if (!empty($ffmpegCheck) && strpos($ffmpegCheck, 'ffmpeg') !== false) {
					$command = $ffmpegPath . ' -loglevel quiet -i ' . escapeshellarg($tempWavFile) . 
					           ' -q:a 5 -y ' . escapeshellarg($tempMp3File) . ' 2>&1';
					
					@shell_exec($command);
					
					if (file_exists($tempMp3File) && filesize($tempMp3File) > 500) {
						$binario = @file_get_contents($tempMp3File);
						if ($binario !== false) {
							$ext = 'mp3';
							$convertido = true;
							error_log("   ✅ Convertido para MP3");
						}
					}
				}
			}
			
			@unlink($tempWavFile);
			@unlink($tempMp3File);
		}
		else {
			// Arquivo desconhecido ou corrompido
			if (!$tipoDetectado['valido']) {
				error_log("❌ ARQUIVO CORROMPIDO! Não consegue detectar formato válido.");
				error_log("   Magic bytes: " . bin2hex(substr($binario, 0, 12)));
				error_log("   ⚠️  Este arquivo não será reproduzível no WhatsApp.");
			} else {
				error_log("⚠️  Tipo desconhecido, salvando como fallback.");
			}
			$ext = 'webm'; // Fallback
		}
		
		// PASSO 4: Salvar no banco com prepared statement (não corrompe dados binários!)
		$tipo = 'PTT';
		$nomeArquivo = "audio_" . $idAtendimento . "_" . $newSequence . "." . $ext;
		
		error_log("📁 ARQUIVO FINAL: " . $nomeArquivo);
		error_log("   Tipo: " . $tipo);
		error_log("   Tamanho: " . strlen($binario) . " bytes");
		error_log("   Status: " . ($convertido ? "✅ Convertido para MP3" : "⚠️  Salvo como " . strtoupper($ext)));
		error_log(str_repeat("=", 80) . "\n");
		
		// Gravar no banco com prepared statement (evita corrupção de dados binários)
		$sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES (?,?,?,?,?,?,?,?,1)";
		
		$stmt = mysqli_prepare($conexao, $sqlInsertTbAnexo);
		if ($stmt) {
			mysqli_stmt_bind_param($stmt, "iisssssi", 
				$idAtendimento, $newSequence, $strNumero, $binario, 
				$nomeArquivo, $nomeArquivo, $tipo, $idCanal);
			
			$insereAnexo = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		} else {
			$insereAnexo = false;
		}

		// Gravar mensagem vinculada - PREPARED STATEMENT (sem CONCAT_WS quebrado!)
		$sqlMsg = "INSERT INTO tbmsgatendimento(id,seq,numero,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
					VALUES (?,?,?,?,?,?,?,NOW(),CURTIME(),?,?,?)";
		
		$stmtMsg = mysqli_prepare($conexao, $sqlMsg);
		if ($stmtMsg) {
			mysqli_stmt_bind_param($stmtMsg, "issssssis",
				$idAtendimento, $newSequence, $strNumero, $strMensagem, $strResposta,
				$strUserNome, $situacao, $intUserId, $idCanal, $idResposta);
			
			$inseremsg = mysqli_stmt_execute($stmtMsg);
			mysqli_stmt_close($stmtMsg);
		} else {
			$inseremsg = false;
		}
	}
	// Verifica se existe Foto da Câmera em Base64 //
	else if( isset($_POST["uploadBase64Camera"]) && !empty($_POST["uploadBase64Camera"]) ){
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal);
		
		// Decodificar Base64
		$base64Camera = $_POST["uploadBase64Camera"];
		// Remover o prefixo "data:image/png;base64," se existir
		if (strpos($base64Camera, 'data:') === 0) {
			$base64Camera = preg_replace('/^data:image\/.+;base64,/', '', $base64Camera);
		}
		
		$binario = base64_decode($base64Camera);
		$tipo = 'IMAG';
		$nomeArquivo = "foto_" . $idAtendimento . "_" . $newSequence . ".png";
		
		// Gravar no banco com prepared statement
		$sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES (?,?,?,?,?,?,?,?,1)";
		
		$stmt = mysqli_prepare($conexao, $sqlInsertTbAnexo);
		if ($stmt) {
			mysqli_stmt_bind_param($stmt, "iisssssi", 
				$idAtendimento, $newSequence, $strNumero, $binario, 
				$nomeArquivo, $nomeArquivo, $tipo, $idCanal);
			
			$insereAnexo = mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
		} else {
			$insereAnexo = false;
		}

		// Gravar mensagem vinculada - PREPARED STATEMENT
		$sqlMsg = "INSERT INTO tbmsgatendimento(id,seq,numero,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
					VALUES (?,?,?,?,?,?,?,NOW(),CURTIME(),?,?,?)";
		
		$stmtMsg = mysqli_prepare($conexao, $sqlMsg);
		if ($stmtMsg) {
			mysqli_stmt_bind_param($stmtMsg, "issssssis",
				$idAtendimento, $newSequence, $strNumero, $strMensagem, $strResposta,
				$strUserNome, $situacao, $intUserId, $idCanal, $idResposta);
			
			$inseremsg = mysqli_stmt_execute($stmtMsg);
			mysqli_stmt_close($stmtMsg);
		} else {
			$inseremsg = false;
		}
	}
	// Verifica se existe um Upload (arquivo tradicional) //
	else if( isset($_FILES["upload"]) && !empty($_FILES['upload']) ){
		//Tento desesperadamente Pegar Multiplos arquivos para Gravar
		for ($controle = 0; $controle < @count($_FILES['upload']["name"]); $controle++){ 
			//Se possuir anexo, gravo uma mensagem por anexo:
			$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
					
			// Gravo o Binario do Anexo
			if ( @count($_FILES['upload']["name"])>1 ){					
				$file_tmp = $_FILES["upload"]["tmp_name"][$controle];
				$nomeArquivo = $_FILES['upload']["name"][$controle];
				$fileType = $_FILES['upload']["type"][$controle];
				$fileSize = $_FILES['upload']["size"][$controle];
			}else{
				//TRato a agravação quando é imagem da Camera ou Audio
                if (($_FILES['upload']["name"]=='imagem_camera.png') || ($_FILES['upload']["name"]=='audio_gravado.mp3')){
					$file_tmp = $_FILES["upload"]["tmp_name"];
					$nomeArquivo = $_FILES['upload']["name"];
					$fileType = $_FILES['upload']["type"];
					$fileSize = $_FILES['upload']["size"];
				}else{
					$file_tmp = $_FILES["upload"]["tmp_name"][$controle];
					$nomeArquivo = $_FILES['upload']["name"][$controle];
					$fileType = $_FILES['upload']["type"][$controle];	
					$fileSize = $_FILES['upload']["size"][$controle];
				}
			}
		
			if ($fileSize<=0){
				$inseremsg = 0;
				continue;		
			}
			
			// Mensagem de Voz - Áudio //
			if( $fileType == "audio/mpeg" ){
				$tipo = 'PTT';
				$nomeArquivo = "audio_" . $idAtendimento . "_" . $newSequence . ".mp3";
			}
			// Demais Arquivos //
			else{ $tipo = strtoupper(substr($fileType,0,5)); }
			
			// Lemos o  conteudo do arquivo usando afunção do PHP file_get_contents //
			$binario = file_get_contents($file_tmp);

			//GRava o Anexo no Banco de dados com prepared statement
		    $sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES (?,?,?,?,?,?,?,?,1)";
		    
		    $stmt = mysqli_prepare($conexao, $sqlInsertTbAnexo);
		    if ($stmt) {
		        mysqli_stmt_bind_param($stmt, "iisssssi", 
		            $idAtendimento, $newSequence, $strNumero, $binario, 
		            $nomeArquivo, $nomeArquivo, $tipo, $idCanal);
		        
		        $insereAnexo = mysqli_stmt_execute($stmt);
		        mysqli_stmt_close($stmt);
		    } else {
		        $insereAnexo = false;
		    }

		  //GRavo uma mensagem vinculada ao Anexo - PREPARED STATEMENT
		  $sqlMsg = "INSERT INTO tbmsgatendimento(id,seq,numero,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
					VALUES (?,?,?,?,?,?,?,NOW(),CURTIME(),?,?,?)";
		  
		  $stmtMsg = mysqli_prepare($conexao, $sqlMsg);
		  if ($stmtMsg) {
		      mysqli_stmt_bind_param($stmtMsg, "issssssis",
		          $idAtendimento, $newSequence, $strNumero, $strMensagem, $strResposta,
		          $strUserNome, $situacao, $intUserId, $idCanal, $idResposta);
		      
		      $inseremsg = mysqli_stmt_execute($stmtMsg);
		      mysqli_stmt_close($stmtMsg);
		  } else {
		      $inseremsg = false;
		  }
		}
	}
	// FIM Verifica se existe um Upload //
	else{
		//Se for apenas MEnsagem Grava a mensagem
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
		
		$sqlMsg = "INSERT INTO tbmsgatendimento(id,seq,numero,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
					VALUES (?,?,?,?,?,?,?,NOW(),CURTIME(),?,?,?)";
		
		$stmtMsg = mysqli_prepare($conexao, $sqlMsg);
		if ($stmtMsg) {
			mysqli_stmt_bind_param($stmtMsg, "issssssis",
				$idAtendimento, $newSequence, $strNumero, $strMensagem, $strResposta,
				$strUserNome, $situacao, $intUserId, $idCanal, $idResposta);
			
			$inseremsg = mysqli_stmt_execute($stmtMsg);
			mysqli_stmt_close($stmtMsg);
		} else {
			$inseremsg = false;
		}
	}

	if( $inseremsg ){ echo "1"; }
	else{ echo "0"; }
?>
