<?php 
    require_once("../../../includes/padrao.inc.php");
	
	$nome = isset($_GET["nome"]) ? trim($_GET["nome"]) : '';

	if (empty($nome)) {
		echo json_encode(['erro' => 'Nome não informado']);
		exit;
	}

	$tabela = "tbusuario";
	$dados = mysqli_query($conexao, "SELECT * FROM $tabela WHERE nome = '".$nome."' LIMIT 1");
	
	if (mysqli_num_rows($dados) > 0) {
		$resultado = mysqli_fetch_object($dados);
		echo json_encode($resultado);
	} else {
		echo json_encode(['erro' => 'Usuário não encontrado']);
	}
?>
