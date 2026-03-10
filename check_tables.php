<?php
require_once('includes/conexao.php');
$result = mysqli_query($conexao, "SHOW TABLES LIKE '%anexo%'");
echo "=== Tabelas contendo 'anexo' ===\n";
if(mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_array($result)) {
        echo $row[0] . "\n";
    }
} else {
    echo "Nenhuma tabela encontrada com 'anexo'\n";
    
    // Listar todas as tabelas
    echo "\n=== Todas as tabelas do banco ===\n";
    $allTables = mysqli_query($conexao, "SHOW TABLES");
    while($row = mysqli_fetch_array($allTables)) {
        echo $row[0] . "\n";
    }
}
mysqli_close($conexao);
