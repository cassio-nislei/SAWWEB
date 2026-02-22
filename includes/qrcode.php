<?php
    // Requires //
    require_once("padrao.inc.php");

    // Definição de Variáveis //
    $_countExecution = intval($_POST["count"])+1;
    $arrRetorno = array(
        "status" => 9,
        "qrcode" => 9,
        "count" => ($_countExecution > 60) ? 0 : $_countExecution,
    );

    $conectado = mysqli_query($conexao, "SELECT conectado, qrcode FROM conexao");

    // Se encontrar registro no BD //
    if( mysqli_num_rows($conectado) > 0 ){
        $rowConnection = mysqli_fetch_object($conectado);
        $arrRetorno["qrcode"] = strlen($rowConnection->qrcode) < 15 ? 9 : $rowConnection->qrcode;
        
        if( $rowConnection->conectado == true ){ $arrRetorno["status"] = 1; }
    }

    echo json_encode($arrRetorno);