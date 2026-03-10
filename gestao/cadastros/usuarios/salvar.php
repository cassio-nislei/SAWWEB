<?php
require_once("../../../includes/padrao.inc.php");

if (!validarCSRF()) { echo "Token de segurança inválido."; exit; }

// Garantir que a coluna 'foto' existe
$verificaColuna = mysqli_query($conexao, "SHOW COLUMNS FROM tbusuario LIKE 'foto'");
if (mysqli_num_rows($verificaColuna) == 0) {
    mysqli_query($conexao, "ALTER TABLE tbusuario ADD COLUMN foto LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER nome_chat");
}

$acao           = isset($_POST['acaoUsuario']) ? $_POST['acaoUsuario'] : '0';
$id             = isset($_POST['id_usuarios']) ? intval($_POST['id_usuarios']) : 0;
$nome           = isset($_POST['nome_usuario']) ? trim($_POST['nome_usuario']) : '';
$login          = isset($_POST['login']) ? trim($_POST['login']) : '';
$email          = isset($_POST['email']) ? trim($_POST['email']) : '';
$senha          = isset($_POST["senha"]) ? $_POST["senha"] : '';
$perfil         = isset($_POST["perfil"]) ? intval($_POST["perfil"]) : 0;
$fotoBase64     = isset($_POST['foto_base64']) ? $_POST['foto_base64'] : '';

if (trim($nome)==''){
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
	$stmtBusca = mysqli_prepare($conexao, "SELECT id FROM tbusuario WHERE nome = ? LIMIT 1");
	mysqli_stmt_bind_param($stmtBusca, "s", $nome);
	mysqli_stmt_execute($stmtBusca);
	$buscaNome = mysqli_stmt_get_result($stmtBusca);
	if (mysqli_num_rows($buscaNome) > 0) {
		$resultadoBusca = mysqli_fetch_assoc($buscaNome);
		$id = intval($resultadoBusca['id']);
	}
	mysqli_stmt_close($stmtBusca);
}
    

if( $acao == 0 ){
	$stmtExiste = mysqli_prepare($conexao, "SELECT id FROM tbusuario WHERE login = ?");
	mysqli_stmt_bind_param($stmtExiste, "s", $login);
	mysqli_stmt_execute($stmtExiste);
	$existe = mysqli_stmt_get_result($stmtExiste);

	if( mysqli_num_rows($existe) > 0 ){
		echo "3";
		mysqli_stmt_close($stmtExiste);
		exit();
	}
	mysqli_stmt_close($stmtExiste);

	// Hash da senha com bcrypt
	$senhaHash = hashSenha($senha);
	$fotoParaDB = !empty($fotoBase64) ? $fotoBase64 : '';
	
	$stmtInsert = mysqli_prepare($conexao, "INSERT INTO tbusuario (nome, login, email, senha, situacao, nome_chat, perfil, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
	mysqli_stmt_bind_param($stmtInsert, "ssssssis", $nome, $login, $email, $senhaHash, $ativo, $nome, $perfil, $fotoParaDB);
	$inserir = mysqli_stmt_execute($stmtInsert);
	mysqli_stmt_close($stmtInsert);

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
		 $stmtUsuario = mysqli_prepare($conexao, "SELECT login FROM tbusuario WHERE id = ?");
		 mysqli_stmt_bind_param($stmtUsuario, "i", $id);
		 mysqli_stmt_execute($stmtUsuario);
		 $usuario = mysqli_stmt_get_result($stmtUsuario);
		 $usuarioSelecionado = mysqli_fetch_assoc($usuario);
		 mysqli_stmt_close($stmtUsuario);
		if ($usuarioSelecionado && $usuarioSelecionado["login"]=='admin'){
		  echo '5';
		   exit();
		}
	  }

	// Se senha foi alterada, fazer hash
	$senhaFinal = $senha;
	if (!empty($senha)) {
		$senhaFinal = hashSenha($senha);
	}

	$fotoParaDB = !empty($fotoBase64) ? $fotoBase64 : '';
	
	if (!empty($fotoParaDB)) {
		$stmtUpdate = mysqli_prepare($conexao, "UPDATE tbusuario SET nome = ?, senha = ?, login = ?, email = ?, nome_chat = ?, perfil = ?, situacao = ?, foto = ? WHERE id = ?");
		mysqli_stmt_bind_param($stmtUpdate, "sssssissi", $nome, $senhaFinal, $login, $email, $nome, $perfil, $ativo, $fotoParaDB, $id);
	} else {
		$stmtUpdate = mysqli_prepare($conexao, "UPDATE tbusuario SET nome = ?, senha = ?, login = ?, email = ?, nome_chat = ?, perfil = ?, situacao = ? WHERE id = ?");
		mysqli_stmt_bind_param($stmtUpdate, "sssssisi", $nome, $senhaFinal, $login, $email, $nome, $perfil, $ativo, $id);
	}
	$atualizar = mysqli_stmt_execute($stmtUpdate);
	mysqli_stmt_close($stmtUpdate);
   
	if( $atualizar ){
		echo "2";
   	}
}