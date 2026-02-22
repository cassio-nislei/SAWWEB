<?php
/**
 * Teste de Carregamento de Arquivos
 * Verifica se os arquivos CSS e JS existem e estão acessíveis
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificação de Arquivos - Upload de Foto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status {
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .ok {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        h1 {
            color: #333;
        }
        .path {
            background: #f0f0f0;
            padding: 10px;
            border-left: 3px solid #007bff;
            margin: 10px 0;
            font-family: monospace;
            font-size: 12px;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✓ Verificação de Arquivos</h1>
        <p>Testando se os arquivos CSS e JS podem ser carregados:</p>

        <?php
        $diretorio_atual = __DIR__;
        $base_path = dirname(dirname(dirname($diretorio_atual))); // Volta para SAWWeb
        
        echo '<h2>Diretório Atual:</h2>';
        echo '<div class="path">' . $diretorio_atual . '</div>';

        $arquivos = [
            'foto-upload.js' => $diretorio_atual . '/foto-upload.js',
            'foto-usuario.css' => $diretorio_atual . '/foto-usuario.css',
            'salvar.php' => $diretorio_atual . '/salvar.php',
            'index.php' => $diretorio_atual . '/index.php',
        ];

        echo '<h2>Arquivos Necessários:</h2>';

        foreach ($arquivos as $nome => $caminho) {
            if (file_exists($caminho)) {
                $tamanho = filesize($caminho);
                echo '<div class="status ok">';
                echo "✓ <strong>$nome</strong> - Encontrado ($tamanho bytes)";
                echo '</div>';
            } else {
                echo '<div class="status error">';
                echo "✗ <strong>$nome</strong> - NÃO ENCONTRADO";
                echo '<div class="path">' . $caminho . '</div>';
                echo '</div>';
            }
        }

        // Testar jQuery
        echo '<h2>Verificação Adicional:</h2>';
        
        $jquery_path = $base_path . '/js/jquery-3.6.0.min.js';
        if (file_exists($jquery_path)) {
            echo '<div class="status ok">';
            echo "✓ <strong>jQuery</strong> - Encontrado em " . basename(dirname($jquery_path));
            echo '</div>';
        } else {
            echo '<div class="status error">';
            echo "✗ <strong>jQuery</strong> - NÃO ENCONTRADO";
            echo '<div class="path">' . $jquery_path . '</div>';
            echo '</div>';
        }
        ?>

        <h2>Caminho para Referências HTML:</h2>
        <p>Quando estiver em <code>gestao/cadastros/usuarios/index.php</code>, use:</p>
        <div class="path">
&lt;!-- Para subir 3 níveis até SAWWeb --&gt;
&lt;link href="../../../css/estiloinputlabel.css" rel="stylesheet"&gt;
&lt;!-- Para arquivo no mesmo diretório --&gt;
&lt;link href="foto-usuario.css" rel="stylesheet"&gt;
&lt;script src="foto-upload.js"&gt;&lt;/script&gt;
        </div>

        <h2>Próximas Etapas:</h2>
        <ol>
            <li>Confirme que todos os arquivos foram encontrados acima</li>
            <li>Acesse o formulário de cadastro de usuários</li>
            <li>Abra o DevTools (F12)</li>
            <li>Procure na aba Network pelos arquivos CSS e JS</li>
            <li>Verifique se foram carregados com status 200</li>
        </ol>
    </div>
</body>
</html>
