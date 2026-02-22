<?php
try {
    $pdo = new PDO('mysql:host=104.234.173.105;dbname=saw15;charset=utf8mb4', 'root', 'Ncm@647534');
    $result = $pdo->query('DESCRIBE tbusuario');
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Colunas da tabela tbusuario:\n";
    echo "═════════════════════════════════════════════\n";
    
    foreach ($rows as $row) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo 'ERRO: ' . $e->getMessage() . PHP_EOL;
}
?>
