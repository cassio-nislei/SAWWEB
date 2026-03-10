<?php
require_once('includes/conexao.php');

echo "=== ESTRUTURA DE tbmsgatendimento ===\n\n";

$result = mysqli_query($conexao, 'DESCRIBE tbmsgatendimento');
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Erro: " . mysqli_error($conexao) . "\n";
}

mysqli_close($conexao);
?>
