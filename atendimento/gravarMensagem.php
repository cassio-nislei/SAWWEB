<?php
	require_once("../includes/padrao.inc.php");
	
	// Declaração de Variáveis - com sanitização //
		$strNumero = isset($_POST["numero"]) ? mysqli_real_escape_string($conexao, $_POST["numero"]) : "";
		$idAtendimento = isset($_POST["id_atendimento"]) ? intval($_POST["id_atendimento"]) : 0;
		$idCanal = isset($_POST["id_canal"]) ? intval($_POST["id_canal"]) : 0;
		$strMensagem = isset($_POST["msg"]) ? $_POST["msg"] : "";
		$strResposta = isset($_POST["Resposta"]) ? mysqli_real_escape_string($conexao, $_POST["Resposta"]) : "";
		$idResposta  = isset($_POST["idResposta"]) ? mysqli_real_escape_string($conexao, $_POST["idResposta"]) : "";
		$nomeDepartamento = isset($_SESSION["usuariosaw"]["nomeDepartamento"]) ? $_SESSION["usuariosaw"]["nomeDepartamento"] : "";
		$binario = '';
		$nomeArquivo = '';
		$anexomsgRapida = isset($_POST["anexomsgRapida"]) ? $_POST["anexomsgRapida"] : "";
		$imageBase64 = isset($_POST["imageBase64"]) ? $_POST["imageBase64"] : '';
		$audioBase64 = isset($_POST["audioBase64"]) ? $_POST["audioBase64"] : '';
		$tipo = '';
		$situacao    = 'E'; 
		$intUserId   = intval($_SESSION["usuariosaw"]["id"]);
		$strUserNome = mysqli_real_escape_string($conexao, $_SESSION["usuariosaw"]["nome"]);
		$mensagemJaInserida = false;
		
	// Declaração de Variáveis //

	// exibir o nome do Atendente em cada mensagem enviada
    if ($_SESSION["parametros"]["nome_atendente"] && $_SESSION["parametros"]["departamento_atendente"]){	
		$strMensagem = "*".$strUserNome." [".$nomeDepartamento."]* <br>". $strMensagem;	
	}else if($_SESSION["parametros"]["nome_atendente"]){
		$strMensagem = "*".$strUserNome."* <br>". $strMensagem;
	}else if($_SESSION["parametros"]["departamento_atendente"]){
        $strMensagem = "*".$nomeDepartamento."* <br>". $strMensagem;
	}

	// Se houver imagem em base64 da câmera
	if (!empty($imageBase64)){
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
		
		// Guardar versão original com prefixo para salvar no DB
		if (strpos($imageBase64, 'data:image') === 0) {
			$imageBase64_orig = $imageBase64; // Guardar com prefixo para o DB
			$imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
		} else {
			$imageBase64_orig = 'data:image/jpeg;base64,' . $imageBase64;
		}
		
		$binario = $imageBase64;
		$tipo = 'IMG';
		$nomeArquivo = "imagem_" . $idAtendimento . "_" . $newSequence . ".jpg";
		
		// Grava o Anexo (imagem em base64) no Banco de dados
		$sqlInsertTbAnexo = "INSERT INTO tbanexos(seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES ('".$newSequence."','".$strNumero."','" . mysqli_real_escape_string($conexao, $binario) . "','" . mysqli_real_escape_string($conexao, $imageBase64_orig) . "','".$nomeArquivo."',
								'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
		
		$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo) or die(mysqli_error($conexao));
		
		// Obter o ID do anexo inserido
		$idAnexo = mysqli_insert_id($conexao);
		
		$situacao = 'N';
		
		$inseremsg = mysqli_query(
			$conexao, 
			"INSERT INTO tbmsgatendimento(id,seq,numero,id_anexo,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
				VALUES('".$idAtendimento."','".$newSequence."','".$strNumero."','".$idAnexo."','" . mysqli_real_escape_string($conexao, nl2br($strMensagem)) . "','" . mysqli_real_escape_string($conexao, $strResposta) . "','".$strUserNome."','".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."','".$idResposta."')"
		);
		if ($inseremsg) {
			$mensagemJaInserida = true;  // MARCAR: Mensagem foi inserida com imagem
		}
	}
	
	// Se houver audio em base64 do gravador
	if (!empty($audioBase64)){
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
		
		// Guardar versao original com prefixo para salvar no DB
		if (strpos($audioBase64, 'data:audio') === 0) {
			$audioBase64_orig = $audioBase64; // Guardar com prefixo para o DB
			$audioBase64_clean = substr($audioBase64, strpos($audioBase64, ',') + 1);
		} else {
			$audioBase64_orig = 'data:audio/mpeg;base64,' . $audioBase64;
			$audioBase64_clean = $audioBase64;
		}
		
		// Preparar strings escapadas
		$binario_escaped = mysqli_real_escape_string($conexao, $audioBase64_clean);
		$audioBase64_orig_escaped = mysqli_real_escape_string($conexao, $audioBase64_orig);
		
		$binario = $binario_escaped;
		$tipo = 'PTT';
		$nomeArquivo = "audio_" . $idAtendimento . "_" . $newSequence . ".mp3";
		
		// Grava o Anexo (áudio em base64) no Banco de dados
		$sqlInsertTbAnexo = "INSERT INTO tbanexos(seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES ('".$newSequence."','".$strNumero."','" . $binario . "','" . $audioBase64_orig_escaped . "','".$nomeArquivo."',
								'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
		
		$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo);
		
		if (!$insereAnexo) {
			error_log("Erro ao inserir anexo de audio: " . mysqli_error($conexao));
		} else {
			// Obter o ID do anexo inserido
			$idAnexo = mysqli_insert_id($conexao);
			
			$situacao = 'N';
			
			$inseremsg = mysqli_query(
				$conexao, 
				"INSERT INTO tbmsgatendimento(id,seq,numero,id_anexo,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
					VALUES('".$idAtendimento."','".$newSequence."','".$strNumero."','".$idAnexo."','" . mysqli_real_escape_string($conexao, nl2br($strMensagem)) . "','" . mysqli_real_escape_string($conexao, $strResposta) . "','".$strUserNome."','".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."','".$idResposta."')"
			);
			
			if (!$inseremsg) {
				error_log("Erro ao inserir mensagem de audio: " . mysqli_error($conexao));
			} else {
				$mensagemJaInserida = true;  // MARCAR: Mensagem foi inserida com audio
			}
		}
	}
	// faz o insert apenas do Áudio, sem atendente
	// Insere o Anexo se houver
	     //Se a mensagem rápida possuir anexo
		 if ($anexomsgRapida != 0 && $anexomsgRapida != "undefined" && !empty($anexomsgRapida) && $anexomsgRapida != ""){

			$nomeArquivo = $_POST["nomeanexomsgRapida"];

			$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem

			$fileType = mime_content_type('../'.$anexomsgRapida);
			if( $fileType == "audio/mpeg" ){
				$tipo = 'PTT';
				$nomeArquivo = "audio_" . $idAtendimento . "_" . $newSequence . ".mp3";
			}
			// Demais Arquivos //
			else{ $tipo = strtoupper(substr($fileType,0,5)); }

			// Lemos o  conteudo do arquivo usando afunção do PHP file_get_contents //
			$binario = file_get_contents('../'.$anexomsgRapida);
			// evitamos erro de sintaxe do MySQL
			$binario = mysqli_real_escape_string($conexao,$binario);

		   //GRava o Anexo no Banco de dados
		   $sqlInsertTbAnexo = "INSERT INTO tbanexos(seq,numero,arquivo,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES ('".$newSequence."','".$strNumero."','" . mysqli_real_escape_string($conexao, $binario) . "','".$nomeArquivo."',
								'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";

			$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo) or die(mysqli_error($conexao));
			
			// Obter o ID do anexo inserido
			$idAnexo = mysqli_insert_id($conexao);
			   //Se está enviando anexo eu mudo a Situação da Mensagem para N para não  Enviar duplicada
			   $situacao = 'N';

				$inseremsg = mysqli_query(
					$conexao, 
					"INSERT INTO tbmsgatendimento(id,seq,numero,id_anexo,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
					VALUES('".$idAtendimento."','".$newSequence."','".$strNumero."','".$idAnexo."','" . mysqli_real_escape_string($conexao, nl2br($strMensagem)) . "','" . mysqli_real_escape_string($conexao, $strResposta) . "','".$strUserNome."','".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."','".$idResposta."')"
				);

			if ($inseremsg) {
				$mensagemJaInserida = true;  // MARCAR: Mensagem foi inserida com anexo rapido
			}
	}

	// Verificação de upload de arquivos (independente de mensagem rápida)
	if (isset($_FILES['upload']["name"]) && is_array($_FILES['upload']["name"])) {
		for ($controle = 0; $controle < count($_FILES['upload']["name"]); $controle++){ 
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
				if (is_array($_FILES['upload']["name"])){
					$file_tmp = $_FILES["upload"]["tmp_name"][$controle];
					$nomeArquivo = $_FILES['upload']["name"][$controle];
					$fileType = $_FILES['upload']["type"][$controle];
					$fileSize = $_FILES['upload']["size"][$controle];
				}else{
					$file_tmp = $_FILES["upload"]["tmp_name"];
					$nomeArquivo = $_FILES['upload']["name"];
					$fileType = $_FILES['upload']["type"];
					$fileSize = $_FILES['upload']["size"];
				}
			}
		
			if ($fileSize<=0){
				$inseremsg = 0;
				continue;		
			}
			
			// Mensagem de Voz - Áudio //
			if( ($fileType == "audio/mpeg") || ($fileType == "audio/mp3") || (strpos($nomeArquivo, '.mp3') !== false) ){
				$tipo = 'PTT';
				$nomeArquivo = "audio_" . $idAtendimento . "_" . $newSequence . ".mp3";
			}
			// Demais Arquivos //
			else{ $tipo = strtoupper(substr($fileType,0,5)); }
			
			// Lemos o conteudo do arquivo //
			$binario = file_get_contents($file_tmp);
		
			// Converter para base64
			$binarioBase64 = base64_encode($binario);
			$base64_data_uri = 'data:' . $fileType . ';base64,' . $binarioBase64;
			
			$binarioBase64Escaped = mysqli_real_escape_string($conexao, $binarioBase64);
			$base64DataUriEscaped = mysqli_real_escape_string($conexao, $base64_data_uri);

			$sqlInsertTbAnexo = "INSERT INTO tbanexos(seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado) VALUES ('".$newSequence."','".$strNumero."','" . $binarioBase64Escaped . "','" . $base64DataUriEscaped . "','".$nomeArquivo."','".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
			
			$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo);
			
			if (!$insereAnexo) {
				error_log("Erro ao inserir em tbanexos: " . mysqli_error($conexao));
				continue;
			}
			
			$idAnexo = mysqli_insert_id($conexao);
			$situacao = 'N';
			
			$inseremsg = mysqli_query(
				$conexao, 
				"INSERT INTO tbmsgatendimento(id,seq,numero,id_anexo,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
				VALUES('".$idAtendimento."','".$newSequence."','".$strNumero."','".$idAnexo."','" . mysqli_real_escape_string($conexao, nl2br($strMensagem)) . "','" . mysqli_real_escape_string($conexao, $strResposta) . "','".$strUserNome."','".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."','".$idResposta."')"
			);
			
			if (!$inseremsg) {
				error_log("Erro ao inserir mensagem com anexo");
			} else {
				$mensagemJaInserida = true;
			}
		}
	}
	// FIM Verifica se existe um Upload com ARRAY //
	elseif (isset($_FILES['upload']["name"]) && !is_array($_FILES['upload']["name"])) {
		// ARQUIVO ÚNICO (não é array)
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal);
		
		$file_tmp = $_FILES["upload"]["tmp_name"];
		$nomeArquivo = $_FILES['upload']["name"];
		$fileType = $_FILES['upload']["type"];
		$fileSize = $_FILES['upload']["size"];
		$fileError = $_FILES['upload']["error"];
		
		if ($fileSize > 0 && $fileError == 0 && file_exists($file_tmp)) {
			// Determinar tipo
			if (strpos($nomeArquivo, '.mp3') !== false || $fileType == "audio/mpeg") {
				$tipo = 'PTT';
				$nomeArquivo = "audio_" . $idAtendimento . "_" . $newSequence . ".mp3";
			} else if (strpos($nomeArquivo, '.jpg') !== false || strpos($nomeArquivo, '.jpeg') !== false || $fileType == "image/jpeg") {
				$tipo = 'IMG';
				$nomeArquivo = "imagen_" . $idAtendimento . "_" . $newSequence . ".jpg";
			} else if (strpos($nomeArquivo, '.png') !== false || $fileType == "image/png") {
				$tipo = 'IMG';
				$nomeArquivo = "imagen_" . $idAtendimento . "_" . $newSequence . ".png";
			} else {
				$tipo = strtoupper(substr($fileType, 0, 5));
			}
			
			$binario = file_get_contents($file_tmp);
			$binarioBase64 = base64_encode($binario);
			$base64_data_uri = 'data:' . $fileType . ';base64,' . $binarioBase64;
			
			$binarioBase64Escaped = mysqli_real_escape_string($conexao, $binarioBase64);
			$base64DataUriEscaped = mysqli_real_escape_string($conexao, $base64_data_uri);
			
			$sqlInsertTbAnexo = "INSERT INTO tbanexos(seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado) VALUES ('".$newSequence."','".$strNumero."','" . $binarioBase64Escaped . "','" . $base64DataUriEscaped . "','".$nomeArquivo."','".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
			
			$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo);
			
			if (!$insereAnexo) {
				error_log("Erro ao inserir anexo unico: " . mysqli_error($conexao));
			} else {
				$idAnexo = mysqli_insert_id($conexao);
				
				$situacao = 'N';
				$inseremsg = mysqli_query(
					$conexao, 
					"INSERT INTO tbmsgatendimento(id,seq,numero,id_anexo,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
					VALUES('".$idAtendimento."','".$newSequence."','".$strNumero."','".$idAnexo."','" . mysqli_real_escape_string($conexao, nl2br($strMensagem)) . "','" . mysqli_real_escape_string($conexao, $strResposta) . "','".$strUserNome."','".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."','".$idResposta."')"
				);
				
				if (!$inseremsg) {
					error_log("Erro ao inserir mensagem com anexo unico: " . mysqli_error($conexao));
				} else {
					$mensagemJaInserida = true;
				}
			}
		} else {
			error_log("Arquivo invalido: tamanho=" . $fileSize . ", erro=" . $fileError);
		}
	}
	// FIM Verifica se existe um Upload //

	// Se nenhuma mensagem foi inserida ainda e há texto, gravar como mensagem de texto
	if (!$mensagemJaInserida && !empty(trim($strMensagem))) {
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal);
		$inseremsg = mysqli_query(
			$conexao, 
			"INSERT INTO tbmsgatendimento(id,seq,numero,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
				VALUES('".$idAtendimento."','".$newSequence."','".$strNumero."','" . mysqli_real_escape_string($conexao, nl2br($strMensagem)) . "','" . mysqli_real_escape_string($conexao, $strResposta) . "','".$strUserNome."','".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."','".$idResposta."')"
		);
		if ($inseremsg) {
			$mensagemJaInserida = true;
		}
	}

	if( $inseremsg ){ echo "1"; }
	else{ echo "0"; }