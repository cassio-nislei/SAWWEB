<?php
// Teste direto do arquivo foto-upload.js
// Acesse via navegador: /gestao/cadastros/usuarios/teste-carregamento.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Carregamento de Arquivos</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 6px; }
        .ok { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🧪 Teste de Carregamento de Arquivos</h1>
    
    <div class="box ok">
        <h2>✓ Teste 1: Carregar CSS via caminho relativo</h2>
        <link href="../../css/estiloinputlabel.css" rel="stylesheet">
        <p>Se esta página estiver estilizada, o CSS está carregando corretamente.</p>
    </div>
    
    <div class="box">
        <h2>✓ Teste 2: Verificar jQuery</h2>
        <p id="jquery-status">Testando...</p>
    </div>
    
    <div class="box">
        <h2>✓ Teste 3: Tentar Carregar foto-upload.js</h2>
        <p id="script-status">Testando...</p>
    </div>
    
    <div class="box">
        <h2>✓ Teste 4: Verificar elementos do DOM</h2>
        <button id="test-btn">Clique para testar</button>
        <p id="dom-status">Aguardando...</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log("jQuery disponível:", typeof jQuery);
            
            // Teste jQuery
            if (typeof jQuery !== 'undefined') {
                document.getElementById('jquery-status').innerHTML = '✓ jQuery carregado com sucesso (versão ' + jQuery.fn.jquery + ')';
            } else {
                document.getElementById('jquery-status').innerHTML = '✗ jQuery NÃO carregado';
            }
            
            // Teste button click
            $(document).on('click', '#test-btn', function() {
                console.log('Botão clicado!');
                document.getElementById('dom-status').innerHTML = '✓ Event listeners funcionam corretamente';
            });
        });
    </script>
    
    <script>
        // Teste de carregamento de script
        console.log("Tentando carregar foto-upload.js...");
        var scriptTag = document.createElement('script');
        scriptTag.src = 'foto-upload.js';
        scriptTag.onload = function() {
            console.log("✓ foto-upload.js carregado");
            document.getElementById('script-status').innerHTML = '✓ foto-upload.js carregado com sucesso';
        };
        scriptTag.onerror = function() {
            console.error("✗ Erro ao carregar foto-upload.js");
            document.getElementById('script-status').innerHTML = '✗ Erro ao carregar foto-upload.js - verifique o caminho';
        };
        document.head.appendChild(scriptTag);
    </script>
    
    <div class="box">
        <h2>📊 DevTools Console</h2>
        <p>Abra o console do navegador (F12) para ver os logs de teste.</p>
        <p>Você deve ver uma mensagem como: "✓ foto-upload.js carregado"</p>
    </div>
</body>
</html>
