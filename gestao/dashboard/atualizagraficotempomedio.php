<?php
   include("../../includes/conexao.php");

      $prod = mysqli_query($conexao,"CALL sprDashBoardTempoMedioAtendimentosDiario();");
     
      $listaDescricao = '';
      $listaQTD       = '';
     while ($produtos = mysqli_fetch_assoc($prod) ){
        $listaDescricao .= $produtos["data_atendimento"] . '|';
        $listaQTD       .= $produtos["tempo_medio_atendimento"] . '|';
     }
     $listaDescricao = substr($listaDescricao, 0, -1) . ';';
     $listaQTD       = substr($listaQTD, 0, -1);
    


     echo $listaDescricao . $listaQTD;

?>