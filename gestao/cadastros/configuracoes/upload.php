<?php
	if( isset($_POST["image"]) ){
		$image = $_POST["image"];
		
		// Se já é uma string data URI (base64), extrai apenas a parte base64
		if (strpos($image, 'data:image/') === 0) {
			// Remove o prefixo "data:image/...;base64," para pegar apenas o base64
			$image = preg_replace('#^data:image/\w+;base64,#i', '', $image);
		}
		
		// Retorna a string base64 completa com o prefixo data URI
		echo 'data:image/jpeg;base64,' . $image;
	}
