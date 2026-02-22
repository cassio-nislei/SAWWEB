<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

  $id = $_POST['id'];
  //Apago o anexo vinculado
  $anexo = mysqli_query($conexao, "select arquivo from tbrespostasrapidas where id = '$id'");
  $anexoSelecionado = mysqli_fetch_assoc($anexo);
  unlink($anexoSelecionado["arquivo"]);
  $sql = "DELETE FROM tbrespostasrapidas WHERE id = '".$id."'";
	$excluir = mysqli_query($conexao,$sql);
   
  if( $excluir ){ echo "1"; }
  else{ echo "9"; }