<?php
  require_once("../../../includes/padrao.inc.php");

  $id = $_POST['IdRespostaAutomatica'];
  $sql = "DELETE FROM tbenviomassanumero WHERE id_msg = $id";


  $sql = "DELETE FROM tbenviomgsmassa WHERE id = $id";
	$excluir = mysqli_query($conexao,$sql)or die(mysqli_error($conexao));
   
  if( $excluir ){ echo "2"; }
  else{ echo "1"; }

  ?>