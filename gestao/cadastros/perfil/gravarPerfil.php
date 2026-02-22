<?php
require_once("../../../includes/padrao.inc.php");
$id        = $_SESSION["usuariosaw"]["id"];
$nome      = addslashes($_POST['nome_usuario']);
$email     = addslashes($_POST['email']);
$login     = addslashes($_POST['login']);



$inserir = mysqli_query($conexao,"update tbusuario set nome='$nome', login = '$login', email = '$email' where id =  '$id' ")or die(mysqli_error($conexao));


$_SESSION["usuariosaw"]["nome"]  = $nome;
$_SESSION["usuariosaw"]["login"] = $login;
$_SESSION["usuariosaw"]["email"] = $email;

if ($inserir){
//	$_SESSION["usuariosaw"]["FOTO"] = $fotoatual;
	echo "1";
}else{
	echo "0";
}

?>