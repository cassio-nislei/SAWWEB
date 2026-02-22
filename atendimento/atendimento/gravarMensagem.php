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

				// evitamos erro de sintaxe do MySQL
				$binario = mysqli_real_escape_string($conexao,$binario);


               //Grava o Anexo no Banco de dados
			   $sqlInsertTbAnexo = "INSERT INTO tbanexos(id,seq,numero,arquivo,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
								VALUES ('".$idAtendimento."','".$newSequence."','".$strNumero."','".$binario."','".$nomeArquivo."',
									'".$nomeArquivo."','".$tipo."','".$idCanal."',1)";

				$insereAnexo = mysqli_query($conexao, $sqlInsertTbAnexo)
					or die($sqlInsertTbAnexo."<br/>".mysqli_error($conexao));

				$situacao = 'N'; //Mudo a Situação para N para não enviar duas vezes a mensagem no ANEXO
			  //Gravo uma mensagem vinculada ao Anexo caso o Anexo tenha realmene sido inserido
			  $inseremsg = mysqli_query(
				$conexao, 
				"INSERT INTO tbmsgatendimento(id,seq,numero,msg, resp_msg, nome_chat,situacao, dt_msg,hr_msg,id_atend,canal, chatid_resposta)
					VALUES('".$idAtendimento."','".$newSequence."' ,'".$strNumero."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem."), '".$strResposta."',
							'".$strUserNome."' ,'".$situacao."',NOW(),CURTIME(),'".$intUserId."','".$idCanal."', '".$idResposta."')"
			);


			} //Fim da tentativa Frustada de Gravar multiplos Anexos
		
		}
	// FIM Verifica se existe um Upload //
	else{
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