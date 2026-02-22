<?php 
    require_once("../../../includes/padrao.inc.php");
	$canal          = (int)$_POST['menu_canal'];
	$tabela    = "tbmsgagendadasawcsv";

    $dados     = mysqli_query($conexao, "SELECT * FROM $tabela WHERE canal = '$canal'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);
    ?>