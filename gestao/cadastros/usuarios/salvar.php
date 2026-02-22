<?php
require_once("../../../includes/padrao.inc.php");

// Garantir que a coluna 'foto' existe
$verificaColuna = mysqli_query($conexao, "SHOW COLUMNS FROM tbusuario LIKE 'foto'");
if (mysqli_num_rows($verificaColuna) == 0) {
    mysqli_query($conexao, "ALTER TABLE tbusuario ADD COLUMN foto LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER nome_chat");
}

$acao           = $_POST['acaoUsuario'];
$id             = isset($_POST['id_usuarios']) ? $_POST['id_usuarios'] : '0';
$nome           = $_POST['nome_usuario'];
$login          = $_POST['login'];
$email          = $_POST['email'];
$senha          = $_POST["senha"];
$perfil         = $_POST["perfil"];
$fotoBase64     = isset($_POST['foto_base64']) ? $_POST['foto_base64'] : '';

if (trim($_POST['nome_usuario'])==''){
	exit();
}
//A: ATIVO; I: INATIVO
if (isset($_POST["usuario_ativo"])){
	$ativo         = 'A';
}else{
	$ativo         = 'I';
}

// Se ação é UPDATE (acao != 0) e id está vazio, buscar por nome
if ($acao != 0 && empty($id)) {
	$buscaNome = mysqli_query($conexao, "SELECT id FROM tbusuario WHERE nome = '".$nome."' LIMIT 1");
	if (mysqli_num_rows($buscaNome) > 0) {
		$resultadoBusca = mysqli_fetch_assoc($buscaNome);
		$id = $resultadoBusca['id'];
	}
}
    

if( $acao == 0 ){
	$existe = mysqli_query($conexao, "SELECT * FROM tbusuario WHERE login = '".$login."'");

	if( mysqli_num_rows($existe) > 0 ){
		echo "3";
		exit();
	}

	// Preparar foto (escapar para MySQL)
	$fotoParaDB = !empty($fotoBase64) ? mysqli_real_escape_string($conexao, $fotoBase64) : '';
	
	$sql = "INSERT INTO tbusuario (nome, login, email, senha, situacao, nome_chat, perfil, foto) VALUES ('$nome', '$login','$email', '$senha', '$ativo', '$nome', '$perfil', '$fotoParaDB')";
	$inserir = mysqli_query($conexao, $sql)
		or die($sql . "<br />" . mysqli_error($conexao));

	if( $inserir ){
		echo "1";
	}
}
else{   
	
	if ($ativo=='I'){
		if ($_SESSION["usuariosaw"]["id"] == $id){
			echo '4'; //Retorno 4 e aviso que não pode Desativar a si próprio
			exit();
		  }
	}
	
	  
	
	  if ($_SESSION["usuariosaw"]["perfil"] == 0){
		 //Busco o nome do usuario para saber se é o admin
		 $usuario = mysqli_query($conexao,"select login from tbusuario where id = '$id'") or die(mysqli_error($conexao));
		 $usuarioSelecionado = mysqli_fetch_assoc($usuario);
		if ($usuarioSelecionado["login"]=='admin'){
		  echo '5'; //Retorno 4 e aviso que não pode Desativar o Administrador Principal
		   exit();
		}
		
	  }

	// Preparar foto (escapar para MySQL)
	$fotoParaDB = !empty($fotoBase64) ? mysqli_real_escape_string($conexao, $fotoBase64) : '';
	
	// Se foto foi enviada, incluir no UPDATE
	if (!empty($fotoParaDB)) {
		$sql = "UPDATE tbusuario SET nome = '$nome', senha = '$senha', login = '$login', email = '$email', nome_chat = '$nome', perfil = '$perfil', situacao='$ativo', foto='$fotoParaDB' where id = '$id'";
	} else {
		$sql = "UPDATE tbusuario SET nome = '$nome', senha = '$senha', login = '$login', email = '$email', nome_chat = '$nome', perfil = '$perfil', situacao='$ativo' where id = '$id'";
	}
	
	$atualizar = mysqli_query($conexao, $sql)
		or die($sql . "<br />" . mysqli_error($conexao));
   
	if( $atualizar ){
		echo "2";
   	}
}