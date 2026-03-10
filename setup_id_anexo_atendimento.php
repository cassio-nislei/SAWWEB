<?php
/**
 * Script para adicionar coluna id_anexo em tbmsgatendimento
 */

require_once('includes/conexao.php');

echo "=== ADICIONANDO COLUNA id_anexo EM tbmsgatendimento ===\n\n";

// 1. Verificar se coluna já existe
$result = mysqli_query($conexao, "DESCRIBE tbmsgatendimento");
$temColuna = false;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] === 'id_anexo') {
            $temColuna = true;
            break;
        }
    }
}

if ($temColuna) {
    echo "✅ Coluna 'id_anexo' já existe em tbmsgatendimento\n";
} else {
    echo "1. Adicionando coluna 'id_anexo'...\n";
    
    $sql = "ALTER TABLE tbmsgatendimento ADD COLUMN id_anexo INT DEFAULT 0 AFTER numero";
    
    if (mysqli_query($conexao, $sql)) {
        echo "✅ Coluna 'id_anexo' adicionada com sucesso\n";
    } else {
        echo "❌ Erro: " . mysqli_error($conexao) . "\n";
    }
}

echo "\n2. Verificando nova estrutura...\n";

$result = mysqli_query($conexao, "DESCRIBE tbmsgatendimento");
if ($result) {
    $encontrou = false;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] === 'id_anexo') {
            $encontrou = true;
            echo "✅ Campo encontrado: " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    }
    if (!$encontrou) {
        echo "⚠️  Campo id_anexo não foi encontrado\n";
    }
}

mysqli_close($conexao);
?>
