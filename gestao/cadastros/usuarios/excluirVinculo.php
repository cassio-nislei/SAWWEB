<?php
  require_once("../../../includes/padrao.inc.php");
  

  $idUsuario          = $_POST['IdUsuario'];
  $idDepartamento     = $_POST["idDepartamento"];

  $sql = "delete from tbusuariodepartamento where id_usuario = '$idUsuario' and id_departamento = '$idDepartamento'";
  $excluir = mysqli_query($conexao,$sql);

  if( $excluir ){ echo "2"; }
  else{ echo "1"; }