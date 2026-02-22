<?php
	require_once("../../../includes/padrao.inc.php");
	$acao                = $_POST['acao'];
	$id                  = $_POST['id'];	
	$renovar = isset($_POST['republicar']) ? 1 : 0;

	$arqData="";
    $arquivoautomatico   = '';
	if(isset($_FILES['foto'])){
		$arquivoautomatico   = $_FILES['foto']['name'];
		$file_tmp            = $_FILES['foto']['tmp_name'];

		 $ext = pathinfo($arquivoautomatico, PATHINFO_EXTENSION);
		 $mimetype = mime_content_type($file_tmp);
		if ($ext=='mp3') {
			$arqData ='data:audio/ogg;base64,';
		} else if ($ext=='ogg') {
			$arqData ='data:audio/ogg;base64,';
		} else if ($ext=='pdf') {
			$arqData ='data:application/pdf;base64,';
		} else if ($ext=='mp4') {
			$arqData ='data:video/mp4;base64,';
		} else if ($ext=='avi') {
			$arqData ='data:video/avi;base64,';
		} else if ($ext=='mpeg') {
			$arqData ='data:video/mpeg;base64,';
		} else {
			$arqData = "data:". $_FILES['foto']['type'].";base64,";
		}
		$arqData = $arqData .base64_encode(file_get_contents($file_tmp));
	}


	if( $acao == 0 ){
		
		$sql = "INSERT INTO tbstorie (data, canal, renovar, arquivo, nome_arquivo) VALUES (now(), 1, '$renovar', '$arqData', '$arquivoautomatico')";
		$inserir = mysqli_query($conexao,$sql)
			or die($sql . "<br/>" . mysqli_error($conexao));
		
		if( $inserir ){ echo "1"; }
	}
	else{
    	$sql = "UPDATE tbstorie SET arquivo='$arqData', nome_arquivo = '$arquivoautomatico', renovar = '$renovar' WHERE id = '$id'";
		$atualizar = mysqli_query($conexao,$sql)or die($sql . "<br/>" . mysqli_error($conexao));

		if( $atualizar ){ echo "2"; }
	}

	?>