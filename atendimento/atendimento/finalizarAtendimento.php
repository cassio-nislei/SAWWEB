<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Declaração de Variáveis //
		$s_celular_atendimento = isset($_POST["numero"]) ? $_POST["numero"] : "";
		$s_id_atendimento = isset($_POST["id_atendimento"]) ? $_POST["id_atendimento"] : "";
		$s_nome = isset($_POST["nome"]) ? $_POST["nome"] : "";
		$s_id_canal = isset($_POST["id_canal"]) ? $_POST["id_canal"] : "";
		$enviaMensagemFinal = isset($_POST["enviaMensagemFinal"]) ? $_POST["enviaMensagemFinal"] : "";
		$enviaMsgInatividade = isset($_POST["enviaMsgInatividade"]) ? $_POST["enviaMsgInatividade"] : "";
		$msgInatividade = isset($_POST["msgInatividade"]) ? $_POST["msgInatividade"] : "";
		$S_FINALIZADO = 'Atendente';
		$id_atend = isset($_SESSION["usuariosaw"]["id"]) ? $_SESSION["usuariosaw"]["id"] : "";
		$nome_atend = isset($_SESSION["usuariosaw"]["nome"]) ? $_SESSION["usuariosaw"]["nome"] : "";
		$msgObs = isset($_POST["msgObs"]) ? $_POST["msgObs"] : "";
	// FIM Declaração de Variáveis //
     
	// Excluindo as Imagens deste Atendimento //
		deletarArquivos($conexao, $s_celular_atendimento, $s_id_atendimento);

	//Verifico se existiu alguma mensagem na Conversa, caso não tenha 
	//enviado nenhuma mensagem não exibo mensagem de fim de atendimento
    $existemensagens = mysqli_query($conexao, 
	    "SELECT id from tbmsgatendimento where id = '$s_id_atendimento' and numero = '$s_celular_atendimento' and canal = '$s_id_canal' limit 1");
	
   if (mysqli_num_rows($existemensagens)>0){ 
	// Parametrizo e Envio a mensagem de Final de Atendimento //
		if( $enviaMensagemFinal === "true" ){
			// Recupera a Sequência da próxima Mensagem //
			$newSequence = newSequence($conexao, $s_id_atendimento, $s_celular_atendimento, $s_id_canal);
			// FIM Recupera a Sequência da próxima Mensagem //

			// Buscando a Mensagem de Finalização do Atendimento //
				$existe = mysqli_query(
					$conexao
					, "SELECT msg_fim_atendimento FROM tbparametros"
				);
			
				if (mysqli_num_rows($existe)>0){
					$msg = mysqli_fetch_object($existe);
					$strMensagem = quebraDeLinha($msg->msg_fim_atendimento);
				}
				else{ $strMensagem  = ''; }
			// FIM Buscando a Mensagem de Finalização do Atendimento //
		
			// Registrando a Última Mensagem do Atendimento //
				if( trim($strMensagem) <> '' ){
					$qryaux = mysqli_query(
						$conexao
						, "INSERT INTO tbmsgatendimento(id, seq, numero, msg, nome_chat, situacao, dt_msg, hr_msg, id_atend, canal)
								VALUES('".$s_id_atendimento."','".$newSequence."','".$s_celular_atendimento."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem.")
									,'".$s_nome."','E',NOW(),CURTIME(),'".$id_atend."', '".$s_id_canal."')"
					);
				}
			// FIM Registrando a Última Mensagem do Atendimento //
		}
	// FIM Parametrizo e Envio a mensagem de Final de Atendimento //
  }//FIm da verificação se possui alguma mensagem

	// Envio de Mensagem de Desconexão por Inatividade //
		if( $enviaMsgInatividade === "true" && trim($msgInatividade) !== "" ){
			// Recupera a Sequência da próxima Mensagem //
			$newSequence = newSequence($conexao, $s_id_atendimento, $s_celular_atendimento, $s_id_canal);
			// FIM Recupera a Sequência da próxima Mensagem //

			$qryaux = mysqli_query(
				$conexao
				, "INSERT INTO tbmsgatendimento(id, seq, numero, msg, nome_chat, situacao, dt_msg, hr_msg, id_atend, canal)
						VALUES('".$s_id_atendimento."','".$newSequence."','".$s_celular_atendimento."', (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".quebraDeLinha($msgInatividade).")
							,'".$s_nome."','E',NOW(),CURTIME(),'".$id_atend."', '".$s_id_canal."')"
			) or die(mysqli_error($conexao));
		}
	// FIM Envio de Mensagem de Desconexão por Inatividade //

	//Gravo a Observação para ficar vinculada aos próximos atendimentos
	if(  trim($msgObs) !== "" ){
	$qryaux = mysqli_query(
		$conexao
		, "INSERT INTO tbatendimentoobservacoes (id_atendimento, numero, obs)
		         VALUES('".$s_id_atendimento."','".$s_celular_atendimento."',  (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".quebraDeLinha($msgObs).")										
			  )"
	) or die(mysqli_error($conexao));
}

   //GRavo as etiquetas selecionadas vinculadas ao atendimento
	if(isset($_POST['id_etiqueta'])){
		$etiquetas_selecionadas = $_POST['id_etiqueta'];
		foreach ($etiquetas_selecionadas as $etiqueta) {
		   // echo "A etiqueta selecionada foi: " . $etiqueta . "<br>";
			$deletaEtiqueta = mysqli_query( $conexao,"INSERT into tbatendimentoetiquetas (id_atendimento, numero, id_etiqueta) 
			                                             VALUES ('".$s_id_atendimento."','".$s_celular_atendimento."', '$etiqueta')");
		}
	  }


   	// Finalizando o Atendimento //
	   $qryaux = mysqli_query(
			$conexao,
			"UPDATE tbatendimento SET situacao = 'F', 
				id_atend = '".$id_atend."', 
				nome_atend = '".$nome_atend."', 
				finalizado_por = '".$S_FINALIZADO."', 
				classifica_atendimento = 1,
				dt_fim = now()
					WHERE id = '".trim($s_id_atendimento)."' AND numero = '".trim($s_celular_atendimento)."' AND canal = '".trim($s_id_canal)."'"
		) or die(mysqli_error($conexao));

	

		if( $qryaux ){ echo "1"; }
		else{ echo "2"; }
	// FIM Finalizando o Atendimento //