<?php
  require_once("../../../includes/padrao.inc.php");

  $acao          = $_POST['acao'];
  $id            = $_POST['id'];
  $menu          = @$_POST['menu'];
  $departamento  = $_POST['departamento'];

if( $acao == 0 ){
	// Verifico se já existe um departamento com o mesmo nome Cadastrado
	$existe = mysqli_query($conexao,"select * from tbdepartamentos where departamento = '$departamento'");
	
	if( mysqli_num_rows($existe) > 0 ){
		echo "3";
		exit();
	}
	
	// Verifico se Já existe um departamento vinculado a o Menu Selecionado
	$existe = mysqli_query($conexao,"select * from tbdepartamentos where id_menu = '$menu'");

	if( mysqli_num_rows($existe) > 0 ){
		echo "4";
		exit();
	}
	
	// Verifico se Já existe uma resposta automatica cadastrada para o Menu Selecionado
	$existe = mysqli_query($conexao,"select * from tbrespostasautomaticas where id_menu = '$menu'");

	if( mysqli_num_rows($existe) > 0 ){
		echo "5";
		exit();
	}

	if (empty($menu)){
		$menu = null;
	}
	
	// Insere o Registro //
	$sql = "insert into tbdepartamentos VALUES (0, '$menu', '$departamento')";
	$inserir = mysqli_query($conexao,$sql);

	if( $inserir ){ echo "1"; }
}
else{
	$sql = "update tbdepartamentos set id_menu = '$menu', departamento = '$departamento' where id = '$id'";
	$atualizar = mysqli_query($conexao,$sql);

	if( $atualizar ){ echo "2"; }
}