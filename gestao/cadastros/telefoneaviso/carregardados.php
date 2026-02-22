<?php 
    require_once("../../../includes/padrao.inc.php");
	
	$codigo    = $_GET["id"];
	$tabela    = "tbtelefonesavisos";

    $dados     = mysqli_query($conexao,"select * from $tabela where numero = '$codigo'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);

    ?>