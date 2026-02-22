<?php
  require_once("../../../includes/padrao.inc.php");

  $idUsuario          = $_POST['IdUsuario'];
  $idDepartamento     = $_POST["idDepartamento"];

	//echo "Acao: $acao Nome: $nome Login: $login Senha: $senha";
	$existe = mysqli_query($conexao,"select * from tbusuariodepartamento where id_usuario = '$idUsuario' and id_departamento = '$idDepartamento'");
	
	if( mysqli_num_rows($existe) > 0 ){
		echo "2";
		exit();
	}	

	$sql = "insert into tbusuariodepartamento (id_usuario, id_departamento) VALUES ('$idUsuario', '$idDepartamento')";
	$inserir = mysqli_query($conexao,$sql);
   
	if( $inserir ){ echo "1"; }