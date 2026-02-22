<?php
//session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
$arquivoexcluido = $_POST["file"];
$id = $_POST["id"];   


if (isset($_SESSION["anexos"])){ 
  foreach($_SESSION["anexos"] as $value){
	 if(file_exists($value) ){
		if ($value == 'arquivos/'.$arquivoexcluido){		
		  unlink($value);
		}
		if (isset($_SESSION["i"])){
	      $_SESSION["i"] = $_SESSION["i"] - 1;
	    }
		unset($_SESSION["anexos"][$value]);
	}
	}
  }
  if (file_exists('arquivos/'.$arquivoexcluido)){
	unlink('arquivos/'.$arquivoexcluido);
  }

  $excluir = mysqli_query(
	$conexao,
	"DELETE FROM base_conhecimento_anexos WHERE id = '".$id."'"
) or die("Erro BCA: " . mysqli_error($conexao));

 

//$_SESSION["anexos"] = $newArr;	  
//print_r($_SESSION["anexos"]);
?>