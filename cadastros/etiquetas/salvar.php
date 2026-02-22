<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
@$id            = $_POST['id_Etiqueta'];
$acao           = $_POST['acaoEtiqueta'];
$cor            = $_POST['cor'];
$descricao      = $_POST['descricao'];


if( $acao == 0 ){
	$existe = mysqli_query($conexao, "SELECT * FROM tbetiquetas WHERE cor = '".$cor."'");

	if( mysqli_num_rows($existe) > 0 ){
		echo "3";
		exit();
	}

	//A: ATIVO; I: INATIVO
	$sql = "INSERT INTO tbetiquetas (cor, descricao) VALUES ('".$cor."', '".$descricao."')";
	$inserir = mysqli_query($conexao, $sql)
		or die($sql . "<br />" . mysqli_error($conexao));

	if( $inserir ){
		echo "1";
	}
}
else{
	$sql = "UPDATE tbetiquetas SET cor = '".$cor."', descricao = '".$descricao."' where id = '".$id."'";
	$atualizar = mysqli_query($conexao, $sql)
		or die($sql . "<br />" . mysqli_error($conexao));
   
	if( $atualizar ){
		echo "2";
   	}
}