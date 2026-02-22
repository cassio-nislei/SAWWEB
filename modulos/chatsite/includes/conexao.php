<?php
$usuarioBD = 'root';
$senhaBD   = '';
$servidorBD= 'localhost';
$idAtual=1;
$strVerSeq = '';
$strVerId = '';
//Faz a conexão com o Banco de dados MYSQL
$conexao = mysqli_connect($servidorBD,$usuarioBD,$senhaBD,'saw_quality') or die("Não foi possivel conectar, aguarde um momento");
mysqli_set_charset($conexao,"utf8mb4");

// Retorna a Foto Perfil do Cliente //
function newId($conexao, $numero){
      $strNewID = "SELECT id FROM tbatendimento WHERE situacao IN ('A','T','P') AND numero = '".$numero."'";

      $qryNewID = mysqli_query($conexao, $strNewID);
      $objNewID = mysqli_fetch_object($qryNewID);

      return $objNewID->id;
 // }
} 

  // Retorna a Foto Perfil do Cliente //
 function newSequence($conexao, $idAtendimento, $numero, $idCanal){
      $strNewSequence = "SELECT coalesce(max(seq),0)+1 newSequence FROM tbmsgatendimento WHERE id_atendimento = '".$idAtendimento."' AND canal = '".$idCanal."' AND numero = '".$numero."'";
   
  $qryNewSequence = mysqli_query($conexao, $strNewSequence);
  $objNewSequence = mysqli_fetch_object($qryNewSequence);

  return $objNewSequence->newSequence;
}
?>