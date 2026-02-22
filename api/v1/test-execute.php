<?php
define('DB_HOST', '104.234.173.105');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');
define('DB_CHARSET', 'utf8mb4');

require_once 'Database.php';

$sql = 'INSERT INTO tbatendimento (id, situacao, nome, id_atend, nome_atend, numero, setor, dt_atend, hr_atend, canal, protocolo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

$params = [2, 'P', 'Teste 2', 1, 'Operador', '11987654324', '1', '2025-11-19', '21:52:51', '1', 20251119215251];

echo "Parâmetros:\n";
var_dump($params);
echo "\n";

$result = Database::execute($sql, $params);

echo "Resultado: $result\n";
echo "Erro: " . Database::getLastError() . "\n";

Database::close();
?>
