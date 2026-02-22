<?php 
    require_once("../../../includes/padrao.inc.php");
	$codigo    = $_GET["codigo"];
	$tabela    = "tbrespostasautomaticas";

    $dados     = mysqli_query($conexao, "SELECT * FROM $tabela WHERE id_menu = '$codigo'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);
    ?>