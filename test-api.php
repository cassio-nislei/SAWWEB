<?php
/**
 * Script de teste dos 10 endpoints da API
 * Compatível com Windows PowerShell
 */

$api_base = 'http://104.234.173.105:7080/api/v1';

echo "═════════════════════════════════════════════════════════════\n";
echo "  Teste dos 10 Endpoints da SAW API\n";
echo "═════════════════════════════════════════════════════════════\n\n";

// Teste 1: Login
echo "[1/10] POST /auth/login\n";
echo "───────────────────────────────────────────────────────────\n";

$login_data = json_encode([
    'usuario' => 'admin',
    'senha' => '123456'
]);

$ch = curl_init($api_base . '/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $http_code\n";
echo "Response:\n";

$decoded = json_decode($response, true);
if ($decoded) {
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    
    if (isset($decoded['data']['token'])) {
        $token = $decoded['data']['token'];
        echo "\n✅ Token obtido com sucesso!\n";
    } else {
        echo "\n❌ Falha ao obter token\n";
        echo "Response bruto: $response\n";
    }
} else {
    echo "❌ Erro ao decodificar JSON\n";
    echo "Response bruto: $response\n";
}

echo "\n";

// Teste 2: Validate Token
if (isset($token)) {
    echo "[2/10] GET /auth/validate\n";
    echo "───────────────────────────────────────────────────────────\n";
    
    $ch = curl_init($api_base . '/auth/validate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $http_code\n";
    echo "Response:\n";
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo "Response bruto: $response\n";
    }
}

echo "\n";

// Teste 3: GET /usuarios/me
if (isset($token)) {
    echo "[3/10] GET /usuarios/me (Usuário Autenticado)\n";
    echo "───────────────────────────────────────────────────────────\n";
    
    $ch = curl_init($api_base . '/usuarios/me');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Status: $http_code\n";
    echo "Response:\n";
    $decoded = json_decode($response, true);
    if ($decoded) {
        echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo "Response bruto: $response\n";
    }
}

echo "\n═════════════════════════════════════════════════════════════\n";
echo "✅ Testes completados!\n";
echo "═════════════════════════════════════════════════════════════\n";
?>
