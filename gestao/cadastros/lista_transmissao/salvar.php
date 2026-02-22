<?php
	require_once("../../../includes/padrao.inc.php");
	$acao                = $_POST['acao'];
	$dt          = explode('/', $_POST['dt_envio']) ;
    $data_envio = $dt[2].'-'.$dt[1].'-'.$dt[0];
	$hora_envio          = $_POST['hr_envio'];
	$celular             = mysqli_real_escape_string($conexao, $_POST['celular']);
	$mensagem            = mysqli_real_escape_string($conexao, $_POST['mensagem']);
	
		
		$sql = "INSERT INTO tbmsgsenviadaspelosaw (numero, dt_inclusao, dt_programada, hora_programada, msg) VALUES ('$celular', current_date(), '$data_envio', '$hora_envio', '$mensagem')";
		$inserir = mysqli_query($conexao,$sql)
			or die($sql . "<br/>" . mysqli_error($conexao));
		
		if( $inserir ){ echo "1"; }


	?>
