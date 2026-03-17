<?php
	require_once("../includes/padrao.inc.php");
	
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
		$anexomsgRapida = $_POST["anexomsgRapida"];		
		$imageBase64 = isset($_POST["imageBase64"]) ? $_POST["imageBase64"] : '';
		$audioBase64 = isset($_POST["audioBase64"]) ? $_POST["audioBase64"] : '';
		$pdfBase64 = isset($_POST["pdfBase64"]) ? $_POST["pdfBase64"] : '';
		$pdfFileName = isset($_POST["pdfFileName"]) ? $_POST["pdfFileName"] : '';
		$tipo = '';
		//$situacao    = ( strpos($strMensagem, 'BEGIN:VCARD') !== false ) ? ((intval($idCanal) > 1 ? "E" : "N" ) ) : "E"; // O Marcelino precisa disso! Pois no Delphi ainda não funciona o envio de Contato!
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

	// Se houver imagem em base64 da câmera
	if (!empty($imageBase64)){
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
		
		// Remove o cabeçalho data:image/...;base64, se existir
		if (strpos($imageBase64, 'data:image') === 0) {
			$imageBase64 = substr($imageBase64, strpos($imageBase64, ',') + 1);
		}
		
		$binario = $imageBase64;
		$tipo = 'IMG';
		$nomeArquivo = "imagem_" . $idAtendimento . "_" . $newSequence . ".jpg";
		
		// Grava o Anexo (imagem em base64) no Banco de dados
		$sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES ('".$idAtendimento."','".$newSequence."','".$strNumero."','".$binario."','".$nomeArquivo."',
								'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
		
		$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo) or die(mysqli_error($conexao));
		
		$situacao = 'N';
		
		$inseremsg = mysqli_query(
			$conexao, 
			"INSERT INTO tbmsgatendimento(id,seq,numero,msg, resp_msg, nome_chat,situacao, dt_msg,hr_msg,id_atend,canal, chatid_resposta)
				VALUES('".$idAtendimento."','".$newSequence."' ,'".$strNumero."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem."), '".$strResposta."',
						'".$strUserNome."' ,'".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."', '".$idResposta."')"
		);
	}

	// Se houver áudio em base64 (gravado com o microfone)
	else if (!empty($audioBase64)){
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
		
		// Guardar versão original com prefixo para salvar no DB
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
		$base64DataUri = $audioBase64_orig_escaped;
		$tipo = 'PTT';
		$nomeArquivo = "audio_" . $idAtendimento . "_" . $newSequence . ".mp3";
		
		// Grava o Anexo (áudio em base64) no Banco de dados com campo base64
		$sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES ('".$idAtendimento."','".$newSequence."','".$strNumero."','" . $binario . "','" . $base64DataUri . "','".$nomeArquivo."',
								'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
		
		$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo) or die(mysqli_error($conexao));
		
		$situacao = 'N';
		
		$inseremsg = mysqli_query(
			$conexao, 
			"INSERT INTO tbmsgatendimento(id,seq,numero,msg, resp_msg, nome_chat,situacao, dt_msg,hr_msg,id_atend,canal, chatid_resposta)
				VALUES('".$idAtendimento."','".$newSequence."' ,'".$strNumero."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem."), '".$strResposta."',
						'".$strUserNome."' ,'".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."', '".$idResposta."')"
		);
	}

	// Se houver PDF em base64 (capturado do drag & drop)
	else if (!empty($pdfBase64)){
		$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
		
		// Guardar versão original com prefixo para salvar no DB
		if (strpos($pdfBase64, 'data:application/pdf') === 0 || strpos($pdfBase64, 'data:') === 0) {
			$pdfBase64_orig = $pdfBase64; // Guardar com prefixo para o DB
			$pdfBase64_clean = substr($pdfBase64, strpos($pdfBase64, ',') + 1);
		} else {
			$pdfBase64_orig = 'data:application/pdf;base64,' . $pdfBase64;
			$pdfBase64_clean = $pdfBase64;
		}
		
		// Preparar strings escapadas
		$binario_escaped = mysqli_real_escape_string($conexao, $pdfBase64_clean);
		$pdfBase64_orig_escaped = mysqli_real_escape_string($conexao, $pdfBase64_orig);
		
		$binario = $binario_escaped;
		$base64DataUri = $pdfBase64_orig_escaped;
		$tipo = 'PDF';
		$nomeArquivo = (!empty($pdfFileName)) ? $pdfFileName : "pdf_" . $idAtendimento . "_" . $newSequence . ".pdf";
		
		// Grava o Anexo (PDF em base64) no Banco de dados com campo base64
		$sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES ('".$idAtendimento."','".$newSequence."','".$strNumero."','" . $binario . "','" . $base64DataUri . "','".$nomeArquivo."',
								'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
		
		$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo) or die(mysqli_error($conexao));
		
		$situacao = 'N';
		
		$inseremsg = mysqli_query(
			$conexao, 
			"INSERT INTO tbmsgatendimento(id,seq,numero,msg, resp_msg, nome_chat,situacao, dt_msg,hr_msg,id_atend,canal, chatid_resposta)
				VALUES('".$idAtendimento."','".$newSequence."' ,'".$strNumero."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem."), '".$strResposta."',
						'".$strUserNome."' ,'".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."', '".$idResposta."')"
		);
	}

	// faz o insert apenas da Imagem, sem atendente
	// Insere o Anexo se houver
	     //Se a mensagem rápida possuir anexo
		 if ($anexomsgRapida != 0){

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
		   $sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
							VALUES ('".$idAtendimento."','".$newSequence."','".$strNumero."','".$binario."','".$nomeArquivo."',
								'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";

			$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo) or die(mysqli_error($conexao));

			   //Se está enviando anexo eu mudo a Situação da Mensagem para N para não  Enviar duplicada
			   $situacao = 'N';

				$inseremsg = mysqli_query(
					$conexao, 
					"INSERT INTO tbmsgatendimento(id,seq,numero,msg, resp_msg, nome_chat,situacao, dt_msg,hr_msg,id_atend,canal, chatid_resposta)
						VALUES('".$idAtendimento."','".$newSequence."' ,'".$strNumero."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem."), '".$strResposta."',
								'".$strUserNome."' ,'".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."', '".$idResposta."')"
				);


		 }else if (isset($_FILES["upload"]) && !empty($_FILES['upload'])   ){ // Verifica se existe um Upload //
            //Verifico se foi selecionado 1 único arquivo
					
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
			
			//	echo "Tamanho Arquivo $fileSize";
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
			
			// Converter para base64
			$binarioBase64 = base64_encode($binario);
			
			// Criar data URI com base64
			$base64_data_uri = 'data:' . $fileType . ';base64,' . $binarioBase64;
			
			// Escapar strings para MySQL
			$binarioBase64Escaped = mysqli_real_escape_string($conexao, $binarioBase64);
			$base64DataUriEscaped = mysqli_real_escape_string($conexao, $base64_data_uri);

               //Grava o Anexo no Banco de dados com base64
			   $sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado) VALUES ('".$idAtendimento."','".$newSequence."','".$strNumero."','" . $binarioBase64Escaped . "','" . $base64DataUriEscaped . "','".$nomeArquivo."','".$nomeArquivo."','".$tipo."','".$idCanal."',1)";
			
			error_log("===== DEBUG TBANEXOS (atendimento) =====");
			error_log("newSequence: " . $newSequence);
			error_log("strNumero: " . $strNumero);
			error_log("nomeArquivo: " . $nomeArquivo);
			error_log("tipo: " . $tipo);
			error_log("idCanal: " . $idCanal);
			error_log("Tamanho binarioBase64Escaped: " . strlen($binarioBase64Escaped) . " bytes");
			error_log("Tamanho base64DataUriEscaped: " . strlen($base64DataUriEscaped) . " bytes");
		
		$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo);
		
		if (!$insereAnexo) {
			error_log("ERRO ao inserir em tbanexos: " . mysqli_error($conexao));
			error_log("Errno: " . mysqli_errno($conexao));
			die("ERRO: " . mysqli_error($conexao));
		}
		
		// Verificar linhas afetadas
		$affectedRows = mysqli_affected_rows($conexao);
		error_log("OK: Linhas inseridas: " . $affectedRows);
		
		// Obter o ID do anexo inserido
		$idAnexo = mysqli_insert_id($conexao);
		error_log("OK: Anexo inserido com ID: " . $idAnexo);
		
		if ($idAnexo <= 0) {
			error_log("PROBLEMA: ID do anexo eh zero ou negativo!");
		}

		$situacao = 'N'; //Mudo a Situacao para N para nao enviar duas vezes a mensagem no ANEXO
		
		error_log("===== DEBUG TBMSGATENDIMENTO (atendimento) =====");
		error_log("ID Atendimento: " . $idAtendimento);
		error_log("Seq: " . $newSequence);
		error_log("Numero: " . $strNumero);
		error_log("ID Anexo: " . $idAnexo);
		
		  //Gravo uma mensagem vinculada ao Anexo caso o Anexo tenha realmene sido inserido
		  $inseremsg = mysqli_query(
			$conexao, 
			"INSERT INTO tbmsgatendimento(id,seq,numero,id_anexo,msg,resp_msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal,chatid_resposta)
			VALUES('".$idAtendimento."','".$newSequence."','".$strNumero."','".$idAnexo."','" . mysqli_real_escape_string($conexao, nl2br($strMensagem)) . "','" . mysqli_real_escape_string($conexao, $strResposta) . "','".$strUserNome."','".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."','".$idResposta."')"
		  );
		  
		  if (!$inseremsg) {
		  	error_log("ERRO ao inserir em tbmsgatendimento: " . mysqli_error($conexao));
		  } else {
		  	error_log("OK: Mensagem com anexo inserida com sucesso");
		  	$msgAffected = mysqli_affected_rows($conexao);
		  	error_log("OK: Linhas inseridas em tbmsgatendimento: " . $msgAffected);
		} //Fim da tentativa Frustada de Gravar multiplos Anexos
		
		} //fecha o for
	} else {
	//Se for apenas Mensagem Grava a mensagem
	$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal); // Gera a sequencia da mensagem
	$inseremsg = mysqli_query(
		$conexao, 
		"INSERT INTO tbmsgatendimento(id,seq,numero,msg, resp_msg, nome_chat,situacao, dt_msg,hr_msg,id_atend,canal, chatid_resposta)
			VALUES('".$idAtendimento."','".$newSequence."' ,'".$strNumero."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem."), '".$strResposta."',
					'".$strUserNome."' ,'".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."', '".$idResposta."')"
	);

	}

	
	if( $inseremsg ){ echo "1"; }
	else{ echo "0"; }