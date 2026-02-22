<?php
  require_once("../includes/padrao.inc.php");

  if( isset( $_POST["id"] ) ){
    $idAtendimento = $_POST["id"];
    $numero = $_POST["numero"];

    // Lista as conversas //
    $QRYCONVERSA = mysqli_query(
        $conexao
        , "UPDATE tbmsgatendimento SET visualizada = true 
            WHERE id = '$idAtendimento' AND  numero = '$numero'"
    ) or die(mysqli_error($conexao));
  }
  else{ sleep(2); }
  ?>