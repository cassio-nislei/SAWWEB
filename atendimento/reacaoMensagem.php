
<?php
	define('AJAX_CALL', true);
	require_once("../includes/padrao.inc.php");
	
	// Declaração de Variáveis //
	$id = isset($_POST["id"]) ? $_POST["id"] : "";
	$reacao = isset($_POST["reacao"]) ? $_POST["reacao"] : "";
	
	$stmtReacao = mysqli_prepare($conexao, "UPDATE tbmsgatendimento SET reacao=?, reagir=1, situacao='N' WHERE chatid = ?");
	mysqli_stmt_bind_param($stmtReacao, "ss", $reacao, $id);
	$reagemsg = mysqli_stmt_execute($stmtReacao);
	mysqli_stmt_close($stmtReacao);	
	//0=Não Reagiu 1=Solicita reação 2=reagiu
	
//Não solicito para apagar pelo ID Unico, porque ele pode ainda não existir
/*
	*/
		
	if( $reagemsg ){ echo "1"; }
	else{ echo "0"; }