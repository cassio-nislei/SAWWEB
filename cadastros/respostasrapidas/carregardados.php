<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
	$codigo    = $_GET["codigo"];
	$tabela    = "tbrespostasrapidas";

    $dados     = mysqli_query($conexao, "SELECT * FROM $tabela WHERE id = '".$codigo."'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);