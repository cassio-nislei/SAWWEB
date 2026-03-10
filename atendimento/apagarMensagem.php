<?php
	require_once("../includes/padrao.inc.php");
	
	// Declaração de Variáveis //
	$id = isset($_POST["id"]) ? $_POST["id"] : "";
	$strNumero = isset($_POST["numero"]) ? $_POST["numero"] : "";
	$idAtendimento = isset($_POST["id_atendimento"]) ? intval($_POST["id_atendimento"]) : 0;
	$idCanal = isset($_POST["id_canal"]) ? $_POST["id_canal"] : "";
    $seq = isset($_POST["seq"]) ? intval($_POST["seq"]) : 0;

	$stmtRemove = mysqli_prepare($conexao, "UPDATE tbmsgatendimento SET msg='🚫Mensagem Apagada', apagada=1, situacao='N' WHERE id = ? AND seq = ? AND numero = ?");
	mysqli_stmt_bind_param($stmtRemove, "iis", $idAtendimento, $seq, $strNumero);
	$removemsg = mysqli_stmt_execute($stmtRemove);
	mysqli_stmt_close($stmtRemove);	
	//0=Não Apagada 1=Solicita exclusao 2=Apagada
	
//Não solicito para apagar pelo ID Unico, porque ele pode ainda não existir
/*
	// faz o insert apenas da Imagem, sem atendente
	$removemsg = mysqli_query(
		$conexao, 
		"update tbmsgatendimento set msg='🚫Mensagem Apagada', apagada=1 where chatid = '$id'"
	);
    //0=Não Apagada 1=Solicita exclusao 2=Apagada
	*/
		
	if( $removemsg ){ echo "1"; }
	else{ echo "0"; }