<?php
  require_once("../includes/padrao.inc.php");

  if (isset($_POST["id"])){
    $id = $_POST["id"];


    //Lista as conversas
    $QRYCONVERSA = mysqli_query(
      $conexao
      , "UPDATE tbmsgatendimento tma, tbatendimento ta
          SET tma.notificada = true
            WHERE tma.notificada = false AND tma.id_atend = 0 AND ta.id_atend = '$id'"
    ) or die(mysqli_error($conexao));
    echo "0";
  }
  else{ echo "1"; }
  ?>