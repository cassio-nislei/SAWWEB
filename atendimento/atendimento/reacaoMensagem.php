
<?php
	require_once("../includes/padrao.inc.php");
	
	// Declaração de Variáveis //
	$id = $_POST["id"];
	$reacao = $_POST["reacao"];
	
	$reagemsg = mysqli_query(
		$conexao, 
		"update tbmsgatendimento set reacao='$reacao', reagir=1, situacao='N' where chatid = '$id' "
	);	
	//0=Não Reagiu 1=Solicita reação 2=reagiu
	
//Não solicito para apagar pelo ID Unico, porque ele pode ainda não existir
/*
	*/
		
	if( $reagemsg ){ echo "1"; }
	else{ echo "0"; }