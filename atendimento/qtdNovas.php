<?php
  require_once("../includes/padrao.inc.php");
  
   $id_usuario = $_POST["id"];

		//Lista as conversas
	   $QRYCONVERSA = mysqli_query($conexao,"select coalesce(count(ID),0) as total_notificacoes from tbmsgatendimento 
				where visualizada = false and id_atend = 0 and situacao = 'A'  
		 ")or die(mysqli_error($conexao));
        $qtdNotificacoes = mysqli_fetch_array($QRYCONVERSA);
       if (mysqli_num_rows($QRYCONVERSA)>0){
		echo $qtdNotificacoes["total_notificacoes"];  
	  }else{
		  echo 0;
	  }

?>