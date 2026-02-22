<?php
  require_once("../../../includes/padrao.inc.php");

  $id = $_POST['IdRespostaAutomatica'];
  $sql = "DELETE FROM tbrespostasautomaticas WHERE id_menu = '".$id."'";
	$excluir = mysqli_query($conexao,$sql);
   
  if( $excluir ){ echo "2"; }
  else{ echo "1"; }

  ?>