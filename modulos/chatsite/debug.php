<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

echo "<h1>Debug ChatSite</h1>";

// Verificar sessão
echo "<h2>Sessão</h2>";
echo "usuariosaw: " . (isset($_SESSION["usuariosaw"]) ? "SETADO" : "NÃO SETADO") . "<br>";
echo "chat: " . (isset($_SESSION["chat"]) ? "SETADO" : "NÃO SETADO") . "<br>";

// Testar API de conversas
echo "<h2>API Conversas</h2>";
echo "<a href='api_conversas.php' target='_blank'>Clique aqui para testar o api_conversas.php</a><br>";

// Testar cada API endpoint
echo "<h2>Testes de Requisições</h2>";
echo "<pre>";

// Testar criar conversa
echo "\n--- Teste de Criar Conversa ---\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . "/modulos/chatsite/criar_conversa.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "nome=Teste Debug");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=" . session_id());
$result = curl_exec($ch);
curl_close($ch);
echo "Resposta: $result\n";

// Testar listar conversas
echo "\n--- Teste de Listar Conversas ---\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST'] . "/modulos/chatsite/api_conversas.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=" . session_id());
$result = curl_exec($ch);
curl_close($ch);
echo "Resposta: " . substr($result, 0, 500) . "...\n";

echo "</pre>";

// Verificar se tabelas existem
echo "<h2>Verificação de Tabelas</h2>";
$conn = new mysqli($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pass'], $GLOBALS['db']);
if ($conn->connect_error) {
    echo "Erro de conexão: " . $conn->connect_error;
} else {
    $tables = ['tbatendimento', 'tbmsgatendimento'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        echo "$table: " . ($result->num_rows > 0 ? "EXISTE" : "NÃO EXISTE") . "<br>";
    }
    
    // Contar atendimentos
    $result = $conn->query("SELECT COUNT(*) as total FROM tbatendimento");
    $row = $result->fetch_assoc();
    echo "Total de atendimentos: " . $row['total'] . "<br>";
}
?>
