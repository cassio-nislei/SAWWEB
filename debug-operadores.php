<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DEBUG getOperadores.php ===\n";

// Flag para avisar padrao.inc.php que é uma chamada AJAX
define('AJAX_CALL', true);
echo "✅ AJAX_CALL definido\n";

echo "📁 __DIR__: " . __DIR__ . "\n";
echo "📁 Caminho padrao.inc.php: " . __DIR__ . "/../includes/padrao.inc.php\n";

if (file_exists(__DIR__ . "/../includes/padrao.inc.php")) {
    echo "✅ Arquivo padrao.inc.php existe\n";
    
    try {
        require_once(__DIR__ . "/../includes/padrao.inc.php");
        echo "✅ padrao.inc.php incluído com sucesso\n";
    } catch (Exception $e) {
        echo "❌ Erro ao incluir padrao.inc.php: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Arquivo NOT FOUND: " . __DIR__ . "/../includes/padrao.inc.php\n";
}

echo "\n=== Testando conexão ===\n";
if (isset($conexao)) {
    echo "✅ \$conexao existe\n";
} else {
    echo "❌ \$conexao NÃO existe\n";
}

echo "\n=== Testando sessão ===\n";
echo "Session status: " . session_status() . "\n";
if (isset($_SESSION)) {
    echo "✅ \$_SESSION existe\n";
    if (isset($_SESSION["usuariosaw"])) {
        echo "✅ \$_SESSION['usuariosaw'] existe\n";
    } else {
        echo "⚠️ \$_SESSION['usuariosaw'] NÃO existe\n";
    }
} else {
    echo "❌ \$_SESSION NÃO existe\n";
}
?>
