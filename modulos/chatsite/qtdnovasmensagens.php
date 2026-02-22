<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
$numero = $_SESSION["chat"]["numero"];

/*
$id = mysqli_query(
    $conexao
    , "select id from tbmsgatendimento where canal = '0' and numero = '$numero' limit 1"
  );
  $idatend = mysqli_fetch_assoc($id);

//Atualizo o id do Atendimento
$_SESSION["chat"]["id_atendimento"] = @$idatend["id"];
*/

$qryaux = mysqli_query(
    $conexao
    , "select count(id) as qtd_mensagens from tbmsgatendimento where canal = '0' and notificada = false and numero = '$numero'"
  );

  $qtd = mysqli_fetch_assoc($qryaux);
  echo $qtd["qtd_mensagens"];

?>