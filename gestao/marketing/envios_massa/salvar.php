<?php
	require_once("../../../includes/padrao.inc.php");
	$acao                = $_POST['acao'];
	$id                = $_POST['id'];
	$dt          = explode('/', $_POST['dt_envio']) ;
    $data_envio = $dt[2].'-'.$dt[1].'-'.$dt[0];
	$mensagem            = mysqli_real_escape_string($conexao, $_POST['mensagem']);
	$ativo = isset($_POST['ativo']) ? 1 : 0; // 1 para marcado, 0 para desmarcado
	$canal               = 1;
	$nome                = mysqli_real_escape_string($conexao, $_POST['nome']);
	
	if( $acao == 0 ){	
		$sql = "INSERT INTO tbenviomgsmassa (canal, nome, dt_inclusao, dt_envio, msg, tipo_mensagem, ativo) VALUES ('$canal', '$nome', current_date(), '$data_envio',  '$mensagem', 2, $ativo)";
		$inserir = mysqli_query($conexao,$sql)
			or die($sql . "<br/>" . mysqli_error($conexao));

	}else{
		$sql = "UPDATE tbenviomgsmassa SET  nome = '$nome', dt_envio = '$data_envio', msg = '$mensagem', ativo = '$ativo' where id = '$id'";
		$inserir = mysqli_query($conexao,$sql)
			or die($sql . "<br/>" . mysqli_error($conexao));
	 	
	}
		
		if( $inserir ){ echo "1"; }


	?>