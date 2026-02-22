<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

$strNumero     = $_SESSION["chat"]["numero"];
//$idAtendimento = newId($conexao, $numero);
$idCanal     = 0;
$strMensagem = $_POST["mensagem"];
$idResposta  = '';
$strResposta = '';

$intUserId = 0;
$strUserNome = $_SESSION["chat"]["nome"];
$situacao    = 'E';

$idchat = newId($conexao, $strNumero);

if ($idchat < 2) {
    $idAtendimento = 1;
} else {
    $idAtendimento = $idchat;

}

 $newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal);

//echo "ID ATENDIMENTO:".$idAtendimento."Sequencia:". $newSequence. "NUmero:".$strNumero;
$inseremsg = mysqli_query(
    $conexao,   "INSERT INTO tbmsgatendimento(id,seq,numero,msg, resp_msg, nome_chat,situacao, dt_msg,hr_msg,id_atend, canal, chatid_resposta)
                     VALUES ('".$idAtendimento."', '$newSequence', '$strNumero','$strMensagem', '$strResposta',
                '$strUserNome', '$situacao',CURDATE(),CURTIME(),'$intUserId','$idCanal', '$idResposta')")or die(mysqli_error($conexao));

  echo "1";

  
?>