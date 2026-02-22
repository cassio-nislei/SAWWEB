<?php
define('DB_HOST', '104.234.173.105');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    echo "Erro de conexão: " . mysqli_connect_error();
    exit;
}

// Obtém próximo ID
$result = mysqli_query($conn, 'SELECT MAX(id) as max_id FROM tbatendimento');
$row = mysqli_fetch_assoc($result);
$newId = $row['max_id'] + 1;

// Tenta inserir
$numero = "11999999996";
$nome = "Teste Insert";
$numero_esc = mysqli_real_escape_string($conn, $numero);
$nome_esc = mysqli_real_escape_string($conn, $nome);

$sql = "INSERT INTO tbatendimento (id, situacao, nome, id_atend, nome_atend, numero, setor, dt_atend, hr_atend, canal, protocolo) 
        VALUES ($newId, 'P', '$nome_esc', 1, 'Operador', '$numero_esc', '1', '2025-11-19', '20:15:00', '1', 20251119201500)";

echo "SQL: $sql\n";
echo "Executando...\n";

if (mysqli_query($conn, $sql)) {
    echo "✅ Sucesso! ID inserido: $newId\n";
    
    // Verifica se foi inserido
    $check = mysqli_query($conn, "SELECT * FROM tbatendimento WHERE id = $newId");
    $row = mysqli_fetch_assoc($check);
    echo "Dados inseridos:\n";
    echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "❌ Erro: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
