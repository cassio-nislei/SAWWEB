<?php
	require_once("../includes/padrao.inc.php");

	// Declaração de Variáveis //
		$id = $_GET['id'];
		$numero = $_GET['numero'];
		$seq = $_GET['seq'];
	// FIM Declaração de Variáveis //

	// Buscando os dados do Arquivo //
		$strAnexos = "SELECT arquivo, nome_arquivo, tipo_arquivo FROM tbanexos WHERE id = '".$id."' AND numero = '".$numero."' AND seq = '".$seq."'";
		$qryAnexos = mysqli_query($conexao, $strAnexos) or die( $strAnexos . "<br />" . mysqli_error($conexao) );
		$objAnexos = mysqli_fetch_object($qryAnexos);
	// FIM Buscando os dados do Arquivo //

	// Imagem //
	if( $objAnexos->tipo_arquivo == 'IMAGE' ){
		// $imagem = explode(".", $objAnexos->nome_arquivo);
		// $fileName = "images/conversas/" . str_replace($imagem[0], $id.'_'.$numero.'_'.$seq, $objAnexos->nome_arquivo);

		// if( !file_exists($fileName) ){
		// 	$img = imagecreatefromstring( $objAnexos->arquivo );	
		// 	imagejpeg( $img, $fileName );
		// }
		
		// header( "Content-type: image/jpeg" );
		// header( sprintf( "Content-length: %d" , strlen( $objAnexos->arquivo ) ) );
	}
	// Arquivo de Áudio //
	elseif( $objAnexos->tipo_arquivo == 'AUDIO' ){
		header('Content-type: audio/mpeg');
	}
	// Arquivo de Vídeo //
	elseif( $objAnexos->tipo_arquivo == 'VIDEO' ){
		header("Content-Type: video/mp4");
	}
	// Áudio em Voz //
	else{
		// Faz o Download do Arquivo //
		header('Content-Description: File Transfer');
		header("Content-Type: application/octet-stream");
		// header("Content-Type: audio/ogg");
		header("Content-Disposition: attachment; filename=".basename($objAnexos->nome_arquivo));
		header("Content-Transfer-Encoding: binary");
	}

	// Essas duas linhas antes do readfile - de imprimir o arquivo //
	ob_end_clean();
	flush();

	// Imprimindo o Arquivo //
	echo $objAnexos->arquivo;