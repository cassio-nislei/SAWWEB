<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
	$idBC = isset($_POST["idBC"]) ? $_POST["idBC"] : "";
  	$problema = isset($_POST["problema"]) ? $_POST["problema"] : "";
	$solucao = isset($_POST["solucao"]) ? $_POST["solucao"] : "";
	$tags = isset($_POST["tags"]) ? $_POST["tags"] : "";
	$acao = isset($_POST['acaoBC']) ? $_POST['acaoBC'] : "0";

	try {
		// Atualização //
		if( intval($acao) > 0 ){
			//Gravo os dados da Base de Conhecimento
			$gravarBC = mysqli_query(
				$conexao, 
				"UPDATE base_conhecimento SET problema = '".$problema."', solucao = '".$solucao."' WHERE id = '".$idBC."';"
			) or die("Erro U_BC: " . mysqli_error($conexao));

			// Gravo as categorias
			if( strlen(trim($tags)) > 0 ){
				saveTags($idBC, $tags, $conexao);
			}

			// Gravo os Arquivos //
			if( isset($_SESSION["anexos"]) ){
				saveFiles($idBC, $tags, $conexao);
			}

			echo "2";
		}
		// Inserção //
		else{
			//Gravo os dados da Base de Conhecimento
			$gravarBC = mysqli_query(
				$conexao, 
				"INSERT INTO base_conhecimento(problema, solucao) VALUES ('$problema', '$solucao')"
			) or die("Erro I_BC: " . mysqli_error($conexao));

			$idBC = mysqli_insert_id($conexao);

			// Gravo os Arquivos //
			if( isset($_SESSION["anexos"]) ){
				saveFiles($idBC, $tags, $conexao);
			}

			// Gravo as Categorias //
			if( strlen(trim($tags)) > 0 ){
				saveTags($idBC, $tags, $conexao);
			}

			echo "1";
		}
	}
	catch (Exception $e) { echo 'Aconteceu o Seguinte Erro ao gerar ticket: ' . $e->getMessage(); }

	function saveTags($idBC, $tags, $conexao){
		// Removendo as Categorias existentes //
		mysqli_query(
			$conexao, 
			"DELETE FROM base_conhecimento_categorias WHERE id_base_conhecimento = '".$idBC."';"
		) or die("Erro BCC: " . mysqli_error($conexao));

		// Inserindo as novas Categorias //
		$categorias = explode(",", $tags);

		foreach ($categorias as $valor) {
			mysqli_query(
				$conexao, 
				"INSERT INTO base_conhecimento_categorias VALUES('".$idBC."', '".$valor."')"
			) or die("Erro BCC: " . mysqli_error($conexao));
		}
	}

	function saveFiles($idBC, $tags, $conexao){
		foreach ($_SESSION["anexos"] as $value) {
			if( file_exists($value) ){
				$nome_arquivo = @end(explode("/", $value));
				// Lemos o  conteudo do arquivo usando afunção do PHP file_get_contents //
				$binario = file_get_contents($value);
				// evitamos erro de sintaxe do MySQL
				$binario = mysqli_real_escape_string($conexao, $binario);
				
				//Gravo no Banco de Dados
				$gravarBC = mysqli_query(
					$conexao, 
					"INSERT INTO base_conhecimento_anexos(id_base_conhecimento, arquivo, nome_arquivo) VALUES('".$idBC."','".$binario."','".$nome_arquivo."')"
				) or die("Erro BCA: " . mysqli_error($conexao));

				unlink($value); //Apago o Arquivo da Pasta		
			}
		}

		// Limpo os arquivos da Sessão //
		emptySessionFilesBC();
	}