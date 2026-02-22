<?php
	require_once("../includes/padrao.inc.php");
	
	// Declaração de Variáveis //
	$id =  $_POST["id"];
	$strNumero = $_POST["numero"];
	$idAtendimento = $_POST["id_atendimento"];
	$idCanal = isset($_POST["id_canal"]) ? $_POST["id_canal"] : "";
    $seq =$_POST["seq"];

	$removemsg = mysqli_query(
		$conexao, 
		"update tbmsgatendimento set msg='🚫Mensagem Apagada', apagada=1, situacao='N' where id = '$idAtendimento' and seq = '$seq' and numero = '$strNumero'"
	);	
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