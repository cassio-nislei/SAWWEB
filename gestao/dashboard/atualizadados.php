<?php
   include("../../includes/conexao.php");

     $periodo = mysqli_query($conexao,"CALL sprDashBoardAtendimentosMensais();");
     
     $anos = mysqli_fetch_assoc($periodo);
     $meses = $anos["JANEIRO"].','.$anos["FEVEREIRO"].','.$anos["MARCO"].','.$anos["ABRIL"].','.$anos["MAIO"].','.$anos["JUNHO"].','.
     $anos["JULHO"].','.$anos["AGOSTO"].','.$anos["SETEMBRO"].','.$anos["OUTUBRO"].','.$anos["NOVEMBRO"].','.$anos["DEZEMBRO"];


     echo $meses;

?>