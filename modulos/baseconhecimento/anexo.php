<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

	$id = $_GET['id'];


	$sqlAnexos = "SELECT * FROM base_conhecimento_anexos WHERE id = '".$id."' LIMIT 1";
	$qryAnexos = mysqli_query($conexao, $sqlAnexos)
		or die( $sqlAnexos . "<br />" . mysqli_error($conexao) );
	$anexo = mysqli_fetch_array($qryAnexos);

		// Faz o Download do Arquivo //
		header('Content-Description: File Transfer');
		header("Content-Type: application/octet-stream");
		// header("Content-Type: audio/ogg");
		header("Content-Disposition: attachment; filename=".basename($anexo["nome_arquivo"]));
		header("Content-Transfer-Encoding: binary"); // base64

	// Essas duas linhas antes do readfile - de imprimir o arquivo //
	ob_end_clean();
	flush();

	// Imprimindo o Arquivo //
	echo $anexo["arquivo"];