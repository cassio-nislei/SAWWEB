<?php
define('DB_HOST', '104.234.173.105');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$result = mysqli_query($conn, 'DESCRIBE tbatendimento');
while ($row = mysqli_fetch_assoc($result)) {
    if ($row['Field'] == 'id') {
        echo "ID Column Info:\n";
        echo "Type: " . $row['Type'] . "\n";
        echo "Null: " . $row['Null'] . "\n";
        echo "Key: " . $row['Key'] . "\n";
        echo "\nInteiros que caberiam:\n";
        echo "  -2147483648 to 2147483647\n";
    }
}

// Tenta uma inserção direta COM parâmetros bindeados corretamente
echo "\nTentando inserção com bind_param corrigido...\n";
$stmt = $conn->prepare('INSERT INTO tbatendimento (id, situacao, nome, id_atend, nome_atend, numero, setor, dt_atend, hr_atend, canal, protocolo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

$id = 3;
$situacao = 'P';
$nome = 'Teste 3';
$id_atend = 1;
$nome_atend = 'Op1';
$numero = '11987654325';
$setor = '1';
$dt_atend = '2025-11-19';
$hr_atend = '22:00:00';
$canal = '1';
$protocolo = 20251119220000;

echo "Tipos: i s s i s s s s s s i\n";
echo "Valores: $id, $situacao, $nome, $id_atend, $nome_atend, $numero, $setor, $dt_atend, $hr_atend, $canal, $protocolo\n";

$stmt->bind_param('issiisssssb', $id, $situacao, $nome, $id_atend, $nome_atend, $numero, $setor, $dt_atend, $hr_atend, $canal, $protocolo);

if ($stmt->execute()) {
    echo "✅ Sucesso!\n";
} else {
    echo "❌ Erro: " . $stmt->error . "\n";
}

mysqli_close($conn);
?>
