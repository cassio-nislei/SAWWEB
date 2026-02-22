<?php
// Arquivo de teste para verificar se o webchat está renderizando
session_start();
$_SESSION["usuariosaw"]["id"] = 1;  // Simular usuário logado

echo "<!-- Teste do webchat -->";
echo "\n\n";

// Incluir apenas o conteúdo do webchat
$_SERVER["REQUEST_URI"] = "/test-webchat.php";  // Para evitar redireções
require_once("webchat/content.php");
?>
