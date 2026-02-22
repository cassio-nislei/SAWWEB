<?php
header('Content-Type: application/json');

$response = [
    'HTTP_AUTHORIZATION' => $_SERVER['HTTP_AUTHORIZATION'] ?? 'NOT SET',
    'HTTP_AUTH_USER' => $_SERVER['HTTP_AUTHORIZATION_USER'] ?? 'NOT SET',
    'REDIRECT_HTTP_AUTHORIZATION' => $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 'NOT SET',
    'Authorization' => getallheaders()['Authorization'] ?? 'NOT SET',
    'all_headers' => getallheaders()
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
