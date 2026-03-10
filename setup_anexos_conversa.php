<?php
require_once('includes/conexao.php');

echo "=== Configurando tbanexos para conversas ===\n\n";

// 1. Verificar se tbanexos existe
$checkTable = mysqli_query($conexao, "SHOW TABLES LIKE 'tbanexos'");
if(mysqli_num_rows($checkTable) === 0) {
    echo "❌ Tabela tbanexos não existe!\n";
    exit;
}

// 2. Adicionar coluna id_conversa se não existir
$checkCol = mysqli_query($conexao, "SHOW COLUMNS FROM tbanexos LIKE 'id_conversa'");
if(mysqli_num_rows($checkCol) === 0) {
    $sql = "ALTER TABLE tbanexos ADD COLUMN id_conversa INT NULL AFTER id";
    if(mysqli_query($conexao, $sql)) {
        echo "✅ Coluna 'id_conversa' adicionada\n";
    } else {
        echo "❌ Erro ao adicionar 'id_conversa': " . mysqli_error($conexao) . "\n";
    }
} else {
    echo "ℹ️  Coluna 'id_conversa' já existe\n";
}

// 3. Verifcar estrutura final
echo "\n=== Estrutura de tbanexos ===\n";
$result = mysqli_query($conexao, "DESCRIBE tbanexos");
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n✅ Configuração concluída!\n";
mysqli_close($conexao);
