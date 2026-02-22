<?php
session_start();
require_once("includes/padrao.inc.php");

// Forçar carregaWebChat para "1"
$_SESSION['webchat_load'] = 1;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Operadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            width: 100%;
            height: 100%;
            background: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Input necessário para carregaChat funcionar -->
    <input type="hidden" id="carregaWebChat" value="1" />
    
    <?php require_once("webchat/index.php"); ?>
    
    <script>
        // Garantir que carregaWebChat está ativo
        $(document).ready(function() {
            $("#carregaWebChat").val("1");
            console.log("✅ carregaWebChat ativado no iframe");
        });
    </script>
</body>
</html>
