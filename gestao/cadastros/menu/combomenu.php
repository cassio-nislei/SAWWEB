<?php
    require_once("../../../includes/padrao.inc.php");


    $menu = mysqli_query($conexao , "SELECT * FROM tbmenu ORDER BY id");
	echo '<option value="0" selected>Nenhum</option>';
     while ($ListaMenus = mysqli_fetch_array($menu)){
         echo '<option value="'.$ListaMenus["id"].'">'.$ListaMenus["descricao"].'</option>';
     }
 ?>