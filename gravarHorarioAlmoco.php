<?php
require_once("includes/padrao.inc.php");

$id_usuario     = $_SESSION["usuariosaw"]["id"];

	if (!isset($_POST['em_almoco'])){
	  $sql = "select em_almoco, msg_almoco from tbusuario  where id = '".$id_usuario."'";
	  $listar= mysqli_query($conexao, $sql)
	  or die($sql . "<br />" . mysqli_error($conexao));

	  $dados = mysqli_fetch_assoc($listar);
	  echo json_encode($dados);

	  exit();
	}

$almoco         = $_POST['em_almoco'];
$mensagem       = $_POST['msgAlmoco'];



	$sql = "UPDATE tbusuario SET em_almoco=$almoco, msg_almoco='$mensagem' where id = '".$id_usuario."'";
	$atualizar = mysqli_query($conexao, $sql)
		or die($sql . "<br />" . mysqli_error($conexao));
   
	if( $atualizar ){
		$_SESSION["usuariosaw"]["em_almoco"] = $almoco;
		$_SESSION["usuariosaw"]["msg_almoco"] = $mensagem ;
		echo "1";
   	}
