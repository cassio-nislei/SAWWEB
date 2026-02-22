<?php
define('DB_HOST', '104.234.173.105');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$result = mysqli_query($conn, 'SELECT MAX(id) as max_id FROM tbatendimento');
$row = mysqli_fetch_assoc($result);

echo "MAX ID atual: " . $row['max_id'] . "\n";
echo "Próximo ID: " . ($row['max_id'] + 1) . "\n\n";

echo "Estrutura do campo ID:\n";
$result2 = mysqli_query($conn, 'DESCRIBE tbatendimento');
while ($field = mysqli_fetch_assoc($result2)) {
    if ($field['Field'] == 'id') {
        echo "Type: " . $field['Type'] . "\n";
        echo "Null: " . $field['Null'] . "\n";
        echo "Key: " . $field['Key'] . "\n";
        echo "Extra: " . $field['Extra'] . "\n";
    }
}

// Tenta inserir com ID válido
echo "\nTentando inserir novo atendimento...\n";
$newId = $row['max_id'] + 1;
$numero = "11987654323";
$nome = "Teste Cliente 3";
$idAtende = 1;
$nomeAtende = "Operador 1";
$situacao = "P";
$canal = "1";
$setor = "1";
$dtAtend = date('Y-m-d');
$hrAtend = date('H:i:s');
$protocolo = date('YmdHis');

$sql = "INSERT INTO tbatendimento (id, situacao, nome, id_atend, nome_atend, numero, setor, dt_atend, hr_atend, canal, protocolo) 
        VALUES ($newId, '$situacao', '$nome', $idAtende, '$nomeAtende', '$numero', '$setor', '$dtAtend', '$hrAtend', '$canal', $protocolo)";

echo "SQL: $sql\n";

if (mysqli_query($conn, $sql)) {
    echo "✅ Sucesso!\n";
} else {
    echo "❌ Erro: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
