<?php
// Ajustar tabela tbanexos para suportar base64 comprimido
// Este script aumenta o campo 'arquivo' para LONGTEXT

require_once("../includes/padrao.inc.php");

echo "<h2>Ajustando Tabela tbanexos</h2>";

// 1. Verificar estrutura atual
echo "<h3>1. Estrutura Atual de tbanexos:</h3>";
$result = mysqli_query($conexao, "
    SELECT 
        COLUMN_NAME, 
        COLUMN_TYPE, 
        IS_NULLABLE, 
        COLUMN_DEFAULT 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'tbanexos'
    ORDER BY ORDINAL_POSITION
");

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Coluna</th><th>Tipo</th><th>Nulo</th><th>Padrão</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['COLUMN_NAME'] . "</td>";
    echo "<td>" . $row['COLUMN_TYPE'] . "</td>";
    echo "<td>" . $row['IS_NULLABLE'] . "</td>";
    echo "<td>" . ($row['COLUMN_DEFAULT'] ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Aumentar campo 'arquivo' para LONGTEXT
echo "<h3>2. Aumentando campo 'arquivo' para LONGTEXT...</h3>";
$sql1 = "ALTER TABLE tbanexos MODIFY COLUMN arquivo LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin";
if (mysqli_query($conexao, $sql1)) {
    echo "✅ Campo 'arquivo' aumentado para LONGTEXT com sucesso!<br>";
} else {
    echo "❌ Erro ao aumentar 'arquivo': " . mysqli_error($conexao) . "<br>";
}

// 3. Adicionar coluna 'base64' se não existir
echo "<h3>3. Adicionando coluna 'base64'...</h3>";
// Primeiro verificar se já existe
$checkResult = mysqli_query($conexao, "
    SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'tbanexos' 
    AND COLUMN_NAME = 'base64'
");

if (mysqli_num_rows($checkResult) == 0) {
    $sql2 = "ALTER TABLE tbanexos ADD COLUMN base64 LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL AFTER arquivo";
    if (mysqli_query($conexao, $sql2)) {
        echo "✅ Coluna 'base64' adicionada com sucesso!<br>";
    } else {
        echo "❌ Erro ao adicionar 'base64': " . mysqli_error($conexao) . "<br>";
    }
} else {
    echo "ℹ️ Coluna 'base64' já existe!<br>";
}

// 4. Status final
echo "<h3>4. Estrutura Final de tbanexos:</h3>";
$result = mysqli_query($conexao, "DESCRIBE tbanexos");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . ($row['Key'] ?? '') . "</td>";
    echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
    echo "<td>" . ($row['Extra'] ?? '') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>✅ Ajustes Concluídos!</h2>";
echo "<p>A tabela tbanexos foi ajustada com sucesso para suportar imagens em base64 comprimidas.</p>";
echo "<p>O campo 'arquivo' agora suporta até 4GB de dados.</p>";
?>
