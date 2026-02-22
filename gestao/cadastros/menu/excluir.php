<?php
  require_once("../../../includes/padrao.inc.php");

  $id = $_POST['IdMenu'];

//Apago as Respostas Rápidas vinculadas ao item do Menu
  $sql = "DELETE FROM tbrespostasautomaticas WHERE id_menu = '$id'";
	$excluir = mysqli_query($conexao,$sql)or die (mysqli_error());

  $sql = "DELETE FROM tbmenu WHERE id = '$id'";
	$excluir = mysqli_query($conexao,$sql)or die (mysqli_error());

  if ($excluir){ 
    echo "2"; 
  } else{ 
    echo "1";
   }