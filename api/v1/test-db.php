<?php
/**
 * Teste direto ao banco
 */

// Configurações
define('DB_HOST', '172.20.0.6');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Conexão falhou: ' . $conn->connect_error]));
    }
    
    // Obtém informações
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    // Tenta obter dados da tabela tbatendimento
    $atendimentos = [];
    if (in_array('tbatendimento', $tables)) {
        $res = $conn->query("SELECT COUNT(*) as count FROM tbatendimento");
        $count_row = $res->fetch_assoc();
        $atendimentos = $count_row;
    }
    
    $output = [
        'conexao' => 'OK',
        'banco' => DB_NAME,
        'tabelas' => $tables,
        'tbatendimento_count' => $atendimentos['count'] ?? 'tabela não existe'
    ];
    
    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
