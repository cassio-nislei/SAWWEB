<?php 
    require_once("../../../includes/padrao.inc.php");
	$codigo    = $_GET["codigo"];
	$tabela    = "tbenviomgsmassa";

    $dados     = mysqli_query($conexao, "SELECT id, nome, DATE_FORMAT(dt_envio, '%d/%m/%Y') AS dt_envio, CASE 
        WHEN ativo = 1 THEN 'checked' 
        ELSE '' 
    END AS ativo, msg FROM tbenviomgsmassa WHERE id = '$codigo'");
	$resultado = mysqli_fetch_object($dados);

    echo json_encode($resultado);
    ?>