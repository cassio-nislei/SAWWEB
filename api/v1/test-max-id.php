<?php
/**
 * Debug do MAX(id)
 */

define('DB_HOST', '104.234.173.105');
define('DB_USER', 'root');
define('DB_PASS', 'Ncm@647534');
define('DB_NAME', 'saw15');
define('DB_CHARSET', 'utf8mb4');

require_once 'Database.php';

header('Content-Type: application/json; charset=utf-8');

$result = Database::query("SELECT COALESCE(MAX(id), 0) + 1 as newId FROM tbatendimento", []);

echo json_encode([
    'raw_result' => $result,
    'result_count' => count($result),
    'newId' => isset($result[0]['newId']) ? $result[0]['newId'] : 'NOT SET',
    'newId_type' => isset($result[0]['newId']) ? gettype($result[0]['newId']) : 'NOT SET',
    'newId_int' => isset($result[0]['newId']) ? (int)$result[0]['newId'] : null,
    'lastError' => Database::getLastError()
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

Database::close();
?>
