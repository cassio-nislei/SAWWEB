<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

echo json_encode([
    '__DIR__' => __DIR__,
    '__FILE__' => __FILE__,
    'path_to_padrao' => __DIR__ . '/../includes/padrao.inc.php',
    'file_exists' => file_exists(__DIR__ . '/../includes/padrao.inc.php'),
    'getcwd' => getcwd(),
    'REQUEST_URI' => $_SERVER['REQUEST_URI']
]);
?>
