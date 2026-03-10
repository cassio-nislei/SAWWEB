<?php
require_once('includes/conexao.php');

// Adicionar coluna id_anexo em tbchatoperadores
$checkCol = mysqli_query($conexao, "SHOW COLUMNS FROM tbchatoperadores LIKE 'id_anexo'");
if(mysqli_num_rows($checkCol) === 0) {
    $sql = "ALTER TABLE tbchatoperadores ADD COLUMN id_anexo INT NULL DEFAULT NULL AFTER mensagem";
    if(mysqli_query($conexao, $sql)) {
        echo "✅ Coluna 'id_anexo' adicionada em tbchatoperadores\n";
    } else {
        echo "❌ Erro: " . mysqli_error($conexao) . "\n";
    }
} else {
    echo "ℹ️  Coluna 'id_anexo' já existe\n";
}

// Remover coluna anexo_base64 se existir (não precisa mais)
$checkCol2 = mysqli_query($conexao, "SHOW COLUMNS FROM tbchatoperadores LIKE 'anexo_base64'");
if(mysqli_num_rows($checkCol2) > 0) {
    $sql = "ALTER TABLE tbchatoperadores DROP COLUMN anexo_base64";
    if(mysqli_query($conexao, $sql)) {
        echo "✅ Coluna 'anexo_base64' removida\n";
    }
}

mysqli_close($conexao);
