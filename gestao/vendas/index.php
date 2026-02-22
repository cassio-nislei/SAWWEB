<?php
		   $marcas = mysqli_query($conexao, "SELECT * FROM TAB_SERVIDORES");
           $l = 1;
           while ($ListaMarcas = mysqli_fetch_array($marcas)){	
             //Verifico o Item Padrão da Categoria		
               echo '<a href="'.$ListaMarcas["IP"].'" class="btn btn-primary">'.$ListaMarcas["DESCRICAO"].'</a>'; 		 
           }
        ?>