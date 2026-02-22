<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Transfere o Atendimento //
	$s_celular_atendimento = $_POST["numero"]; //Pego o número do atendimento
	$s_id_atendimento = $_POST["id_atendimento"];
	$s_nome = $_POST["nome"];
	$idCanal = isset($_REQUEST["id_canal"]) ? $_REQUEST["id_canal"] : "";

	// Dados do Atendente //
	$id_atend   = $_SESSION["usuariosaw"]["id"];
	$nome_atend = $_SESSION["usuariosaw"]["nome"];
	$id_departamento = $_SESSION["usuariosaw"]["idDepartamento"];
	$nomeDepartamento = $_SESSION["usuariosaw"]["nomeDepartamento"];

	// Recupera a Sequência da próxima Mensagem //
	$newSequence = newSequence($conexao, $s_id_atendimento, $s_celular_atendimento, $idCanal);
	// FIM Recupera a Sequência da próxima Mensagem //
	
	$S_FINALIZADO = 'Transferencia';
   
   	// Parametrizo a mensagem de Final de Atendimento //
   	$existe = mysqli_query($conexao,"select msg_aguardando_atendimento from tbparametros ");

	if (mysqli_num_rows($existe)>0){
		$msg = mysqli_fetch_array($existe);
		$strMensagem = $msg["msg_aguardando_atendimento"];

		if( trim($strMensagem) !== "" ){
			$strMensagem = str_replace("<<setor>>", $nomeDepartamento, $strMensagem);
			$strMensagem = quebraDeLinha($strMensagem);
		}
		else{ $strMensagem  = ''; }
	}
	else{ $strMensagem  = ''; }

	// Antes a mensagem estava fixa agora adicionei a mensagem do Parametro
	if (trim($strMensagem) !== ''){
		$qryaux = mysqli_query(
			$conexao
			, "INSERT INTO tbmsgatendimento(id,seq,numero,msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal)
				VALUES('".$s_id_atendimento."','".$newSequence."','".$s_celular_atendimento."',(CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem.")
						,'".$nome_atend."','E',NOW(),CURTIME(),'".$id_atend."','".$idCanal."')"
		);
	}

	// Seta o Atendimento atual como finalizado e marca Finalizado por Transferencia
	$qryaux = mysqli_query(
		$conexao
		, "UPDATE tbatendimento 
			SET situacao = 'F',
				id_atend = '".$id_atend."' ,
				nome_atend = '".$nome_atend."',
				finalizado_por = '".$S_FINALIZADO."'
				WHERE id = '".$s_id_atendimento."' AND numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'"
	);

	// Busca o Próximo Id //
		$qryaux = mysqli_query(
			$conexao
			, "SELECT coalesce(max(id),0)+1 newId FROM tbatendimento WHERE numero = '".$s_celular_atendimento."'"
		);
		$row = mysqli_fetch_object($qryaux);
		$newId = $row->newId;
	// FIM Busca o Próximo Id //

	// Verificando se Já Transferiu o Atendimento //
		$jatransferiu = mysqli_query($conexao,"select id from tbatendimento where situacao = 'A' and numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'");

		if (mysqli_num_rows($jatransferiu)>0){
			echo "transferido";
			exit();
		}
	// FIM Verificando se Já Transferiu o Atendimento //

	// Insere o novo atendimento como 'Em Andamento' //
		$qryaux = mysqli_query(
			$conexao
			, "INSERT INTO tbatendimento(id,situacao,numero,dt_atend,hr_atend,id_atend,nome_atend,nome,setor,canal)
				VALUES ('".$newId."','A','".$s_celular_atendimento."',CURDATE(),CURTIME(),'".$id_atend."','".$nome_atend."','".$s_nome."','".$id_departamento."','".$idCanal."')"
		) or die(mysqli_error($conexao));
	// FIM Insere o novo atendimento como 'Em Andamento' //

	// Se já existia uma conversa anterior iniciada na Triagem atualizo as conversas para o novo atendimento //
		$gravoaconversaanterior = mysqli_query(
			$conexao,
			"UPDATE tbmsgatendimento SET id = '".$newId."' WHERE id = '".$s_id_atendimento."' AND numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'"
		) or die(mysqli_error($conexao));
	// FIM Se já existia uma conversa anterior iniciada na Triagem atualizo as conversas para o novo atendimento //

	// Se já existem Anexos muda para o Novo Atendimento //
		$mudaAnexos = mysqli_query(
			$conexao,
			"UPDATE tbanexos SET id = '".$newId."' WHERE id = '".$s_id_atendimento."' AND numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'"
		) or die(mysqli_error($conexao));
	// FIM Se já existem Anexos muda para o Novo Atendimento //

	if( $qryaux ){ echo $newId; } else{ echo "-1"; }