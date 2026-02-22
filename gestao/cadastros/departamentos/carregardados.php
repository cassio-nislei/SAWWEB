<?php 
    require_once("../../../includes/padrao.inc.php");
	
	$codigo    = $_GET["id"];
	$tabela    = "tbdepartamentos";

    $dados     = mysqli_query($conexao,"select * from $tabela where ID = '$codigo'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);