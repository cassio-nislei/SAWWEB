<?php
  require_once("../../../includes/padrao.inc.php");
  $acao         = $_POST['acao'];
  
  $pai          = $_POST['id_menu'];
  $id           = @$_POST['id'];
  $menu         = $_POST['txtmenu'];


if( $acao == 0 ){
	$existe = mysqli_query($conexao,"SELECT * FROM tbmenu WHERE descricao = '$menu'");
	if (mysqli_num_rows($existe)>0){
		echo "3";
		exit();
	}

	$GeraId = mysqli_query($conexao,"select coalesce(max(id),0)+1 as id from tbmenu");
	$idGerado=mysqli_fetch_assoc($GeraId);
	
	$sql = "INSERT INTO tbmenu(id, pai, descricao) VALUES ('".$idGerado["id"]."', '$pai', '$menu')";
	$inserir = mysqli_query($conexao,$sql) 
		or die(mysqli_error($conexao));
   
	if ($inserir){ echo "1"; }
}
else{
	$sql = "UPDATE tbmenu SET pai = '$pai', descricao = '$menu' WHERE id = '$id'";
	$atualizar = mysqli_query($conexao,$sql) or die (mysqli_error());

	if ($atualizar){ echo "2"; }
}