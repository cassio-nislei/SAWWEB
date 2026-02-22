<?php 
    require_once("../../../includes/padrao.inc.php");
	
	$id    = $_GET["id"];
	$tabela    = "tbusuario";

    $dados     = mysqli_query($conexao,"select * from $tabela where id = '$id'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);