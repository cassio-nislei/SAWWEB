<?php
	require_once("../../../includes/padrao.inc.php");
	$acao                = $_POST['acao'];
	$menu                = $_POST['menu_resposta'];
	$menu_acao           = $_POST['menu_acao'];
	$respostaautomatica  = $_POST['respostaautomatica'];
	

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
		// Verifico se já existe um departamento com o mesmo nome Cadastrado
		$existe = mysqli_query($conexao, "SELECT 1 FROM tbrespostasautomaticas WHERE descricao = '$respostaautomatica'")
			or die(mysqli_error($conexao));
		
		if( mysqli_num_rows($existe) > 0 ){
			echo "3";
			exit();
		}
		
		// Verifico se Já existe um departamento vinculado ao Menu Selecionado
		$existe = mysqli_query($conexao, "SELECT 1 FROM tbdepartamentos WHERE id_menu = '$menu'")
			or die(mysqli_error($conexao));

		if( mysqli_num_rows($existe) > 0 ){
			echo "4";
			exit();
		}
		
		//Verifico se Já existe uma resposta automatica cadastrada para o Menu Selecionado
		$existe = mysqli_query($conexao, "SELECT 1 FROM tbrespostasautomaticas WHERE id_menu = '$menu'")
			or die(mysqli_error($conexao));

		if( mysqli_num_rows($existe) > 0 ){
			echo "5";
			exit();
		}
		
		
		$sql = "INSERT INTO tbrespostasautomaticas (id_menu, descricao, acao, arquivo, nome_arquivo) VALUES ('$menu', '$respostaautomatica', '$menu_acao', '$arqData', '$arquivoautomatico')";
		$inserir = mysqli_query($conexao,$sql)
			or die($sql . "<br/>" . mysqli_error($conexao));
		
		if( $inserir ){ echo "1"; }
	}
	else{
		if ($arqData!="") {
			$sql = "UPDATE tbrespostasautomaticas SET id_menu = '$menu', descricao = '$respostaautomatica', acao = '$menu_acao', arquivo='$arqData', nome_arquivo = '$arquivoautomatico' WHERE id_menu = '$menu'";
		} else {
			$sql = "UPDATE tbrespostasautomaticas SET id_menu = '$menu', descricao = '$respostaautomatica', acao = '$menu_acao' WHERE id_menu = '$menu'";
		}
		
		$atualizar = mysqli_query($conexao,$sql);

		if( $atualizar ){ echo "2"; }
	}

	?>