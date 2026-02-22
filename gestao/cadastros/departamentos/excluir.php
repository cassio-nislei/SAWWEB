<?php
  require_once("../../../includes/padrao.inc.php");

  $id = $_POST['IdDepartamento'];

    //Apago as mensagens do Chat entre operadores vinculadas a esse departamento
    $sql = "DELETE FROM tbchatoperadores WHERE id_departamento = '$id'";
    $excluir = mysqli_query($conexao,$sql) or die (mysqli_error());

  //Apago os Vinculos de usuarios e departamentos vinculados ao departamento que está sendo excluido
  $sql = "DELETE FROM tbusuariodepartamento WHERE id_departamento = '$id'";
	$excluir = mysqli_query($conexao,$sql) or die (mysqli_error());

  //Apago o Departamento
  $sql = "DELETE FROM tbdepartamentos WHERE id = '$id'";
	$excluir = mysqli_query($conexao,$sql) or die (mysqli_error());

  if( $excluir ){ echo "2"; }
  else{ echo "1"; }