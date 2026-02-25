<?php
// Script de diagnóstico para debugar carregamento de conversa
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

echo "=== DEBUG CONVERSA ===\n\n";

// 1. Verificar se o arquivo padrao.inc.php existe
$padrao_path = __DIR__ . "/../includes/padrao.inc.php";
echo "📁 Caminho padrao.inc.php: " . $padrao_path . "\n";
echo "   Arquivo existe: " . (file_exists($padrao_path) ? "✅ SIM" : "❌ NÃO") . "\n";

// 2. Verificar se o arquivo htmlConversa.php existe
$html_path = __DIR__ . "/htmlConversa.php";
echo "\n📁 Caminho htmlConversa.php: " . $html_path . "\n";
echo "   Arquivo existe: " . (file_exists($html_path) ? "✅ SIM" : "❌ NÃO") . "\n";

// 3. Listar arquivos do diretório atendimento
echo "\n📁 Arquivos em " . __DIR__ . ":\n";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        echo "   - $file\n";
    }
}

// 4. Verificar diretórios includes
echo "\n📁 Arquivos em " . __DIR__ . "/../includes:\n";
$includes_dir = __DIR__ . "/../includes";
if (is_dir($includes_dir)) {
    $files = scandir($includes_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "   - $file\n";
        }
    }
} else {
    echo "   ❌ Diretório não encontrado!\n";
}

// 5. Tentar incluir o padrao.inc.php
echo "\n🔄 Tentando incluir padrao.inc.php...\n";
try {
    require_once($padrao_path);
    echo "✅ padrao.inc.php incluído com sucesso\n";
    
    // Verificar conexão
    if (isset($conexao)) {
        echo "✅ Variável \$conexao disponível\n";
        echo "   Conexão ativa: " . (mysqli_ping($conexao) ? "✅ SIM" : "❌ NÃO") . "\n";
    } else {
        echo "⚠️ Variável \$conexao não definida\n";
    }
} catch (Exception $e) {
    echo "❌ Erro ao incluir: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DEBUG ===\n";
?>
