<?php 
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
	$codigo    = $_GET["codigo"];
	$tabela    = "tbetiquetas";

    $dados     = mysqli_query($conexao,"select * from $tabela where id = '$codigo'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);