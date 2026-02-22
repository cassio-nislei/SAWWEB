<?php
  require_once("../../../includes/padrao.inc.php");

  $id = $_POST['IdStorie'];
  $sql = "DELETE FROM tbstorie WHERE id = '".$id."'";
	$excluir = mysqli_query($conexao,$sql);
   
  if( $excluir ){ echo "2"; }
  else{ echo "1"; }

  ?>