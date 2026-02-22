<?php
	require_once("../includes/padrao.inc.php");
   	
	$id_usuario = $_POST["id"];

		//Lista as conversas
	   $QRYCONVERSA = mysqli_query($conexao,"select coalesce(count(tma.ID),0) as total_notificacoes from tbmsgatendimento tma 
inner join tbatendimento ta
				where tma.notificada = false and tma.id_atend = 0 and ta.id_atend = '$id_usuario' 
		 ")or die(mysqli_error($conexao));
        $qtdNotificacoes = mysqli_fetch_array($QRYCONVERSA);
       if (mysqli_num_rows($QRYCONVERSA)>0){
		echo $qtdNotificacoes["total_notificacoes"];  
	  }else{
		  echo 0;
	  }

?>