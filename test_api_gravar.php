<?php
/**
 * Teste de API - gravarMensagemChat.php
 * Simula um POST com anexo em base64
 */

require_once(__DIR__ . '/includes/conexao.php');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION["usuariosaw"]["id"] = 1; // Fingir que está logado

echo "=== TESTE DA API gravarMensagemChat.php ===\n\n";

// Teste 1: Apenas mensagem
echo "1. Testando POST com apenas mensagem...\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/webchat/gravarMensagemChat.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'idDepto' => 1,
        'strMensagem' => 'Teste de mensagem pura',
        'ehPrivada' => 0,
        'idDestinatario' => 0
    ]),
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

// Teste 2: Mensagem com anexo
echo "2. Testando POST com anexo (imagem small)...\n";

$base64_test = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/webchat/gravarMensagemChat.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'idDepto' => 1,
        'strMensagem' => 'Teste com anexo',
        'ehPrivada' => 0,
        'idDestinatario' => 0,
        'anexoBase64' => $base64_test,
        'anexoNome' => 'teste.png',
        'anexoTipo' => 'image/png'
    ]),
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

// Teste 3: Apenas anexo (sem mensagem)
echo "3. Testando POST com apenas anexo (sem mensagem)...\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/webchat/gravarMensagemChat.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'idDepto' => 1,
        'strMensagem' => '',
        'ehPrivada' => 0,
        'idDestinatario' => 0,
        'anexoBase64' => $base64_test,
        'anexoNome' => 'só_anexo.png',
        'anexoTipo' => 'image/png'
    ]),
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n\n";

echo "✅ Testes de API concluídos\n";
?>
