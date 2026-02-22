<?php 
    require_once("../../includes/padrao.inc.php");

	$tabela    = "tbparametros";
    $dados     = mysqli_query($conexao,"SELECT * FROM $tabela");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);