<?php
	require_once("../includes/padrao.inc.php");
  	
	//Inicia o Atendimento

	$s_celular_atendimento = $_POST["numero"]; //Pego o número do atendimento
	$s_nome = $_POST["nome"];
	//Gero o número de atendimento

	// Verifica antes se já não tem um Atendimento em Curso //
		$qryAtendimentoAtivo = mysqli_query($conexao, "SELECT id, nome_atend FROM tbatendimento WHERE numero = '".$s_celular_atendimento."' AND situacao NOT IN('F') LIMIT 1;");
		$objAtendimentoAtivo = mysqli_fetch_object($qryAtendimentoAtivo);

		if( mysqli_num_rows($qryAtendimentoAtivo) == 0 ){
			$qryaux = mysqli_query(
				$conexao
				, "SELECT coalesce(max(id),0)+1 SEQ 
					FROM tbatendimento
						WHERE numero = '$s_celular_atendimento'"
			);

			$listaqryaux = mysqli_fetch_array($qryaux);
			$I_SEQ = $listaqryaux['SEQ'];
			$s_id_atendimento = $I_SEQ;
			// Fim da Geração do Id de Atendimento //

			//Dados do Atendente
			$id_atend = $_SESSION["usuariosaw"]["id"];
			$nome_atend = $_SESSION["usuariosaw"]["nome"];

			//Agora ao inciar exibe o setor do usuário pertence no início
			$qryDeptoUsuario = mysqli_query($conexao, "select * from tbusuariodepartamento where id_usuario = '$id_atend' ");
			$arrDeptoUsuario = mysqli_fetch_array($qryDeptoUsuario);
			$idDepartamento = $arrDeptoUsuario["id_departamento"];

			//Verifico se já possui um atendimento aberto para o usuário selecionado, se já Houver eu não inicio um novo atendimento
			$qryaux = mysqli_query(
				$conexao
				, "SELECT id 
					FROM tbatendimento 
						WHERE situacao = 'A' AND numero = '$s_celular_atendimento'"
			);

			if( mysqli_num_rows($qryaux) > 0 ){
				if( $qryaux ){
					$idAtendimento = mysqli_fetch_array($qryaux);
					echo $idAtendimento["id"]; 
				}
				else{ echo "erro"; }
				exit();
			}
			// FIM da verificação de existencia de atendimento em aberto

		// Seleciona o último ID Canal utilizado pelo cliente ou define o ID Canal Padrão //
			$qryIDCanal = mysqli_query($conexao, "SELECT canal FROM `tbatendimento` WHERE `numero` = '".$s_celular_atendimento."' ORDER BY id DESC LIMIT 1;");
			$objIDCanal = mysqli_fetch_object($qryIDCanal);

			if( intval(mysqli_num_rows($qryIDCanal)) > 0 ){
				$idCanal = $objIDCanal->canal;
			}
			// Defini o ID Canal Padrão //
			else{ $idCanal = "1"; }
		// FIM Seleciona o último ID Canal utilizado pelo cliente ou define o ID Canal Padrão //

		// if( intval(mysqli_num_rows($qryAtendimentoAtivo)) === 0 ){

			//Gero um número de Protocolo para o atendimento que foi Iniciado Ex: 202304221544591 202304221550411
		//	$qryprotocolo = mysqli_query($conexao, "select CONCAT(DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y%m%d%H%i%s'), MAX(COALESCE(SUBSTRING(protocolo, 14),0))+1) as protocolo from tbatendimento;");
		//	$objprotocolo = mysqli_fetch_object($qryprotocolo);
	//		$protocolo = $objprotocolo->protocolo;
			//Verifico se já existe o protocolo gerado
			$protocolo = date('YmdHis');

			$qryaux = mysqli_query(
				$conexao
				, "INSERT INTO tbatendimento (id,situacao,nome,id_atend,nome_atend,numero,setor,dt_atend,hr_atend, canal, protocolo)
					VALUES('$s_id_atendimento','A','$s_nome','$id_atend','$s_nome','$s_celular_atendimento','$idDepartamento',CURDATE(),CURTIME(), '$idCanal','$protocolo'
					 )"
			) or die(mysqli_error($conexao));

			//Após Gerar o Atendimento, verifico se está controlando protocolos para Enviar uma mensagem com o Número do Protocolo
			//Exibir mensagem de Número de Protocolo
			if ($_SESSION["parametros"]["usar_protocolo"]==1){
				//BUsco o Número de protocolo
				$qryprotocolo = mysqli_query(
					$conexao
					, "SELECT protocolo from tbatendimento							
							WHERE id = '".$s_id_atendimento."' AND numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'" );

				$protocoolo = mysqli_fetch_assoc($qryprotocolo);
				//Adiciono no Banco a mensagem com o Número de protocolo
				$strMensagem = "Seu Protocolo de Atendimento é #".$protocoolo["protocolo"].".";

				// Recupera a Sequência da próxima Mensagem //
	            $newSequence = newSequence($conexao, $s_id_atendimento, $s_celular_atendimento, $idCanal);

				//Faz o Insert da Mensagem
				$qryaux = mysqli_query(
					$conexao
					, "INSERT INTO tbmsgatendimento(id, seq, numero, msg, nome_chat, situacao, dt_msg, hr_msg, id_atend, canal)
						VALUES('".$s_id_atendimento."', '".$newSequence."', '".$s_celular_atendimento."', '".$strMensagem."', '".$nome_atend."', 'E', NOW(), CURTIME(), '".$id_atend."', '".$idCanal."')"
				);
			}

		   if( $qryaux ){ 
			  echo $s_id_atendimento; }
		  	  else{ echo "erro"; 
			}
		}else{ 	
			echo $objAtendimentoAtivo->id;		
			//echo "Já existe um atendimento deste cliente com o(a) atendente " . $objAtendimentoAtivo->nome_atend; 
		}
	// FIM Verifica antes se já não tem um Atendimento em Curso //