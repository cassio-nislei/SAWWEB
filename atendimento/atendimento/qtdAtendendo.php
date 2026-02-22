<?php
  	require_once("../includes/padrao.inc.php");

   	// Definições de Variáveis //
		$id_usuario = isset($_POST["id"]) ? $_POST["id"] : "";
		if ($_SESSION["usuariosaw"]["perfil"] == 0 || $_SESSION["parametros"]["mostra_todos_chats"] == 1){
			$permissaoAdmin = '';
		}else{
			$permissaoAdmin = " AND id_atend = '.$id_usuario.' AND setor IN(
				SELECT id_departamento 
					FROM tbusuariodepartamento 
						WHERE id_usuario = '$id_usuario'
			)";
		}

		$qtdeNovasMensagens = 0;
	// FIM Definições de Variáveis //

//	echo "ID: $id_usuario PERFIL: ".$_SESSION["usuariosaw"]["perfil"]. " MOSTRAR CHATS:". $_SESSION["parametros"]["mostra_todos_chats"]."<br>";
//	echo $permissaoAdmin;

//	exit();
	
	// Lista as Conversas //
	$qryConversa = mysqli_query(
		$conexao
		, "SELECT id, numero
			FROM tbatendimento 
				WHERE situacao = 'A' ${permissaoAdmin}					
					ORDER BY dt_atend, hr_atend"
	) or die("Erro ao verificar a quantidade de Conversas em Atendimento: " . mysqli_error($conexao));

	if( mysqli_num_rows($qryConversa) > 0 ){
		// Enviando a Qtde de Atendimentos //
		echo mysqli_num_rows($qryConversa) . "#";

		while( $atendendo = mysqli_fetch_array($qryConversa) ){
			$qtdNovas = mysqli_query(
				$conexao
				, "SELECT count(id) AS qtd_novas 
					FROM tbmsgatendimento 
						WHERE numero = '".$atendendo["numero"]."' AND id = '".$atendendo["id"]."' AND id_atend = 0 AND visualizada = false"
			);
			$not = mysqli_fetch_array($qtdNovas);

			if( $not["qtd_novas"] > 0 ){
				$qryUltMsg = mysqli_query(
					$conexao
					, "SELECT msg, DATE_FORMAT(hr_msg, '%H_%i') AS hora  FROM tbmsgatendimento 
						WHERE numero = '".$atendendo["numero"]."' AND id = '".$atendendo["id"]."'
							ORDER BY seq DESC
								LIMIT 1"
				);
				$arrUltMsg = mysqli_fetch_array($qryUltMsg);

				if( strlen($arrUltMsg['msg']) > 30 ){
					$arrUltMsg['msg'] = substr($arrUltMsg['msg'], 0, 30) . " ... ";
				}

				echo $atendendo["numero"]."&".$not["qtd_novas"]."&".$arrUltMsg['msg']."&".$arrUltMsg['hora']."@";	
				$qtdeNovasMensagens += $not["qtd_novas"];
			}
		}

		echo "#".$qtdeNovasMensagens;
	}
	else{ echo trim(0); }