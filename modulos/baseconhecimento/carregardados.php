<?php 
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
	
	$codigo = $_GET["codigo"];
	$tabela = "base_conhecimento";
    $tags   = "#";
    $files  = array();

    // Base de Conhecimento //
    $dados     = mysqli_query($conexao,"SELECT * FROM $tabela WHERE id = '".$codigo."'");
	$resultado = mysqli_fetch_object($dados);

    // Tags //
    $dadosTags = mysqli_query($conexao,"SELECT * FROM base_conhecimento_categorias WHERE id_base_conhecimento = '".$codigo."'");


        while( $resultadoTags = mysqli_fetch_object($dadosTags) ){
            $tags .= "," . $resultadoTags->descricao;
        }

        $resultado->tags = str_replace("#,", "", $tags);

  

    
    // FIM Tags //

    // Files //
        $dadosFiles = mysqli_query($conexao,"SELECT * FROM base_conhecimento_anexos WHERE id_base_conhecimento = '".$codigo."'");
        
        while( $resultadoFiles = mysqli_fetch_object($dadosFiles) ){
            $files[] = array("id" =>$resultadoFiles->id, "name" => $resultadoFiles->nome_arquivo);
        }
        
        $resultado->files = json_encode($files);
        $resultado->qtdeFiles = count($files);
    // FIM Tags //

    // Limpo os arquivos da Sessão //
    emptySessionFilesBC();

    echo json_encode($resultado);