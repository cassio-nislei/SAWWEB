<?php
require_once("../../../includes/padrao.inc.php");

if (!validarCSRF()) { echo "Token de segurança inválido."; exit; }

$id        = intval($_SESSION["usuariosaw"]["id"]);
$nome      = isset($_POST['nome_usuario']) ? trim($_POST['nome_usuario']) : '';
$email     = isset($_POST['email']) ? trim($_POST['email']) : '';
$login     = isset($_POST['login']) ? trim($_POST['login']) : '';

$stmt = mysqli_prepare($conexao, "UPDATE tbusuario SET nome=?, login=?, email=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "sssi", $nome, $login, $email, $id);
$inserir = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


$_SESSION["usuariosaw"]["nome"]  = $nome;
$_SESSION["usuariosaw"]["login"] = $login;
$_SESSION["usuariosaw"]["email"] = $email;

if ($inserir){
	echo "1";
}else{
	echo "0";
}

?>