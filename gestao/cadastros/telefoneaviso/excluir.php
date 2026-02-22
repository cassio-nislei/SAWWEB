<?php
  require_once("../../../includes/padrao.inc.php");

  $id = $_POST['IdTelefone'];

  $sql = "DELETE FROM tbtelefonesavisos WHERE numero = '$id'";
	$excluir = mysqli_query($conexao,$sql);

  if ($excluir){ echo "2"; }
  else{ echo "1"; }

  ?>