<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

  $id = $_POST['IdEtiqueta'];

  $sql = "delete from tbetiquetas where id = '$id'";
  $excluir = mysqli_query($conexao,$sql);
   
  if( $excluir ){
    // Apago os Vinculos desse usuário com os departamentos //
    $excluir = mysqli_query($conexao,"delete from tbetiquetas where id_usuario = '$id'");
    echo "2"; 
  }
  else{ echo "1"; }