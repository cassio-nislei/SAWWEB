<?php
require_once("includes/padrao.inc.php");

echo "<h1>🔍 Diagnóstico tbanexos</h1>";

// 1. Verificar estrutura da tabela
echo "<h2>1. Estrutura de tbanexos:</h2>";
$result = mysqli_query($conexao, "DESCRIBE tbanexos");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Extra</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 2. Verificar tamanho da tabela
echo "<h2>2. Informações da tabela:</h2>";
$result = mysqli_query($conexao, "
    SELECT 
        TABLE_NAME,
        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
        TABLE_ROWS
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbanexos'
");
$info = mysqli_fetch_assoc($result);
echo "Tamanho: " . $info['size_mb'] . " MB<br>";
echo "Registros: " . $info['TABLE_ROWS'] . "<br>";

// 3. Verificar max_allowed_packet
echo "<h2>3. Configurações MySQL:</h2>";
$result = mysqli_query($conexao, "SHOW VARIABLES LIKE 'max_allowed_packet'");
$row = mysqli_fetch_assoc($result);
$max_packet = $row['Value'];
echo "max_allowed_packet: " . ($max_packet / 1024 / 1024) . " MB<br>";

// 4. Verificar últimos registros
echo "<h2>4. Últimos 5 registros em tbanexos:</h2>";
$result = mysqli_query($conexao, "
    SELECT id, seq, numero, tipo_arquivo, 
           LENGTH(arquivo) as arquivo_size, 
           LENGTH(base64) as base64_size,
           nome_arquivo
    FROM tbanexos 
    ORDER BY id DESC 
    LIMIT 5
");

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Seq</th><th>Numero</th><th>Tipo</th><th>Arquivo (bytes)</th><th>Base64 (bytes)</th><th>Nome</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['seq'] . "</td>";
    echo "<td>" . $row['numero'] . "</td>";
    echo "<td>" . $row['tipo_arquivo'] . "</td>";
    echo "<td>" . $row['arquivo_size'] . "</td>";
    echo "<td>" . $row['base64_size'] . "</td>";
    echo "<td>" . $row['nome_arquivo'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 5. Teste de INSERT
echo "<h2>5. Teste de INSERT:</h2>";
$testSQL = "INSERT INTO tbanexos(seq, numero, arquivo, base64, nome_arquivo, nome_original, tipo_arquivo, canal, enviado)
            VALUES (999, '999999', 'TEST_BINARY_DATA', 'data:text/plain;base64,VEVTVApCQVNFNjQ=', 'test.txt', 'test.txt', 'TEXT', 1, 1)";

$result = mysqli_query($conexao, $testSQL);
if ($result) {
    $id = mysqli_insert_id($conexao);
    echo "✅ INSERT teste bem-sucedido. ID: " . $id . "<br>";
    
    // Deletar registro de teste
    mysqli_query($conexao, "DELETE FROM tbanexos WHERE id = " . $id);
} else {
    echo "❌ INSERT teste falhou: " . mysqli_error($conexao) . "<br>";
}

?>
