<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
	$acao	= $_POST['acaoRespostaRapida'];
	$id		= $_POST['IdRespostaRapida'];
	$idUser	= $_SESSION["usuariosaw"]["id"];
	$titulo	= $_POST['titulo'];
	$resposta = $_POST['resposta'];
	$acaoMenu = (isset($_POST['acao'])) ? intval($_POST['acao']) : 0;

	$arqData="";

	if(isset($_FILES['foto'])){
		$Nome_Arquivo   = $_FILES['foto']['name'];
		$file_tmp            = $_FILES['foto']['tmp_name'];
		$extensao = (isset($Nome_Arquivo)) ? pathinfo($Nome_Arquivo, PATHINFO_EXTENSION) : '';
		$caminhoArquivo = (isset($Nome_Arquivo) && $Nome_Arquivo != '') ? 'anexos/'.md5(uniqid()) . '-' . time() . '.'.$extensao : '';
		
	      if($caminhoArquivo != ''){
	        move_uploaded_file($file_tmp,$caminhoArquivo);	
	      }
	}


	if( $acao == 0 ){
		// Verifico se já existe uma registro com o mesmo 'Título'
		$existe = mysqli_query(
			$conexao
			, "SELECT 1 
				FROM tbrespostasrapidas 
					WHERE titulo = '".$titulo."'"
		);
		
		if( mysqli_num_rows($existe) == 0 ){
			$sql = "INSERT INTO tbrespostasrapidas (id_usuario, titulo, resposta, acao, arquivo, nome_arquivo) VALUES ('".$idUser."', '".$titulo."', '".$resposta."', '".$acaoMenu."', '$caminhoArquivo', '$Nome_Arquivo')";

			$inserir = mysqli_query($conexao, $sql)
				or die(mysqli_error($conexao));
			
			if( $inserir ){ echo "1"; }
			else{ echo "9"; }
		}
		else{ echo "3"; }
	}
	else{
		if ($caminhoArquivo!="") {
			$sql = "UPDATE tbrespostasrapidas 
					SET resposta = '$resposta'
						, titulo = '$titulo'
						, acao = '$acaoMenu'
						, arquivo = '$caminhoArquivo'
						, nome_arquivo = '$Nome_Arquivo'
						 WHERE id = '".$id."'";			
		} else {
			$sql = "UPDATE tbrespostasrapidas 
					SET resposta = '".$resposta."'
						, titulo = '".$titulo."'
						, acao = '$acaoMenu'
						 WHERE id = '".$id."'";
		}
		
		$atualizar = mysqli_query($conexao, $sql)
			or die($sql . "<br/>" . mysqli_error($conexao));

		if( $atualizar ){ echo "2"; }
		else{ echo "9"; }
	}