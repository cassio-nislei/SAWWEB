<?php
    // Requires //
    require_once("padrao.inc.php");

    // Definição de Variáveis //
    $arrRetorno = array( "status" => 9 );

    $conectado = mysqli_query($conexao, "SELECT conectado FROM conexao");

    // Se encontrar registro no BD //
    if( mysqli_num_rows($conectado) > 0 ){
        $rowConnection = mysqli_fetch_object($conectado);
        
        if( $rowConnection->conectado == true ){ $arrRetorno["status"] = 1; }
    }

    echo json_encode($arrRetorno);