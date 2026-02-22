<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Verifica se o usuário está logado //
	if(!isset($_SESSION["usuariosaw"]["id"])){
		header("HTTP/1.1 401 Unauthorized");
		exit;
	}

	// Obtém o ID do usuário logado //
	$idUsuario = $_SESSION["usuariosaw"]["id"];

	// Pega a hora atual do PHP (mais confiável) //
	$dataHoraAtual = date("Y-m-d H:i:s");
	
	// Atualiza o timestamp do usuário com a hora do PHP //
	$sqlUpdate = "UPDATE tbusuario SET datetime_online = '".$dataHoraAtual."' WHERE id = '".$idUsuario."'";
	$qryUpdate = mysqli_query($conexao, $sqlUpdate) or die("Erro ao atualizar timestamp: " . mysqli_error($conexao));

	// Verifica se a atualização foi bem-sucedida //
	if(mysqli_affected_rows($conexao) > 0){
		$retorno = array(
			"status" => "success",
			"timestamp" => $dataHoraAtual,
			"idUsuario" => $idUsuario,
			"mensagem" => "Timestamp atualizado com sucesso"
		);
	}
	else{
		$retorno = array(
			"status" => "warning",
			"timestamp" => $dataHoraAtual,
			"idUsuario" => $idUsuario,
			"mensagem" => "Nenhuma linha foi atualizada"
		);
	}
	
	// Retorna o JSON com o timestamp //
	header('Content-Type: application/json');
	echo json_encode($retorno);
?>
