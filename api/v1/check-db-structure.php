<?php
/**
 * Verificar estrutura do banco de dados
 */

define('DB_HOST', '104.234.173.105');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode([
            'status' => 'error',
            'conexao' => 'FALHA',
            'message' => 'Conexão falhou: ' . $conn->connect_error
        ]));
    }
    
    // Obtém lista de tabelas
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    // Verifica estrutura de tbatendimento se existir
    $atend_struct = null;
    if (in_array('tbatendimento', $tables)) {
        $res = $conn->query("DESCRIBE tbatendimento");
        $atend_struct = [];
        while ($row = $res->fetch_assoc()) {
            $atend_struct[] = $row;
        }
    }
    
    $output = [
        'status' => 'success',
        'conexao' => 'OK',
        'banco' => DB_NAME,
        'tabelas_totais' => count($tables),
        'tabelas' => $tables,
        'tbatendimento_existe' => in_array('tbatendimento', $tables),
        'tbatendimento_estrutura' => $atend_struct
    ];
    
    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
