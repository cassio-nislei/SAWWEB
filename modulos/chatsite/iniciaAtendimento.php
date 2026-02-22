<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

if (isset($_POST["nome"])){
    $_SESSION["chat"]["nome"] = $_POST["nome"];

 
    $ascii = implode('',  range(0, 9));
    $ascii = str_repeat($ascii, 5);
    $numero = substr(str_shuffle($ascii), 0, 13);
    $_SESSION["chat"]["numero"] = $numero;
    
    // Get the attendance ID
    $id_atendimento = newId($conexao, $numero);
    $_SESSION["chat"]["id_atendimento"] = $id_atendimento;

    $_SESSION["chat"]["menu"] = true;    

    echo 1;   
} 
?>