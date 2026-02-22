<?php
/**
 * Executar Migrations SQL via PHP
 * Solução para quando MySQL CLI não está disponível
 * 
 * Uso: php execute-migrations.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "═════════════════════════════════════════════════════════════\n";
echo "  Executor de Migrations - SAW API\n";
echo "═════════════════════════════════════════════════════════════\n\n";

// Configurações
$db_host = '104.234.173.105';
$db_user = 'root';
$db_password = 'Ncm@647534';
$db_name = 'saw15';
$sql_file = __DIR__ . '/api/v1/migrations-audit.sql';

// Verificar se arquivo SQL existe
if (!file_exists($sql_file)) {
    echo "❌ ERRO: Arquivo não encontrado: $sql_file\n";
    exit(1);
}

echo "📁 Arquivo SQL: $sql_file\n";
echo "📊 Tamanho: " . number_format(filesize($sql_file) / 1024, 2) . " KB\n\n";

// Conectar ao banco
echo "🔗 Conectando ao banco de dados...\n";
try {
    $dsn = "mysql:host=$db_host;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conectado com sucesso!\n\n";
} catch (PDOException $e) {
    echo "❌ ERRO de Conexão: " . $e->getMessage() . "\n";
    echo "\n📝 Verificar:\n";
    echo "   • Host: $db_host\n";
    echo "   • User: $db_user\n";
    echo "   • Senha: ***\n";
    exit(1);
}

// Selecionar banco de dados
try {
    $pdo->query("USE $db_name");
    echo "✅ Banco de dados selecionado: $db_name\n\n";
} catch (PDOException $e) {
    echo "❌ ERRO ao selecionar banco: " . $e->getMessage() . "\n";
    exit(1);
}

// Ler arquivo SQL
$sql_content = file_get_contents($sql_file);

// Remover comentários e linhas vazias
$lines = preg_split('/\n/', $sql_content);
$cleaned_lines = [];

foreach ($lines as $line) {
    $line = trim($line);
    // Ignorar linhas vazias e comentários de bloco
    if (!empty($line) && !preg_match('/^--/', $line) && !preg_match('/^\/\*/', $line)) {
        $cleaned_lines[] = $line;
    }
}

$sql_content = implode("\n", $cleaned_lines);

// Dividir em comandos (por ;)
$commands = array_filter(
    array_map('trim', explode(';', $sql_content)),
    function($cmd) {
        return !empty($cmd);
    }
);

echo "📝 Comandos encontrados: " . count($commands) . "\n\n";
echo "═════════════════════════════════════════════════════════════\n";
echo "  Executando Migrations\n";
echo "═════════════════════════════════════════════════════════════\n\n";

// Executar cada comando
$success_count = 0;
$error_count = 0;
$errors = [];

foreach ($commands as $index => $command) {
    $command = trim($command);
    
    if (empty($command)) {
        continue;
    }
    
    // Extrair tipo de comando
    preg_match('/^(CREATE|ALTER|INSERT|DROP|DELETE|UPDATE)/i', $command, $matches);
    $cmd_type = $matches[1] ?? 'QUERY';
    
    // Resumir comando para exibição
    $display_cmd = substr($command, 0, 80);
    if (strlen($command) > 80) {
        $display_cmd .= '...';
    }
    
    echo "[" . str_pad(($index + 1), 2, '0', STR_PAD_LEFT) . "] $cmd_type: $display_cmd\n";
    
    try {
        $pdo->query($command);
        echo "     ✅ Sucesso\n";
        $success_count++;
    } catch (PDOException $e) {
        $error_msg = $e->getMessage();
        
        // Ignorar erros esperados (tabela já existe, coluna já existe, etc)
        if (strpos($error_msg, 'already exists') !== false || 
            strpos($error_msg, 'Duplicate column') !== false ||
            strpos($error_msg, 'Duplicate key') !== false) {
            echo "     ⚠️  Já existe (ignorado)\n";
            $success_count++;
        } else {
            echo "     ❌ ERRO: $error_msg\n";
            $errors[] = [
                'command' => $command,
                'error' => $error_msg
            ];
            $error_count++;
        }
    }
}

echo "\n";
echo "═════════════════════════════════════════════════════════════\n";
echo "  Resultado Final\n";
echo "═════════════════════════════════════════════════════════════\n\n";

echo "✅ Executados com sucesso: $success_count\n";

if ($error_count > 0) {
    echo "❌ Erros críticos: $error_count\n\n";
    
    foreach ($errors as $err) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Comando:\n" . substr($err['command'], 0, 200) . "\n";
        echo "Erro: " . $err['error'] . "\n";
    }
    echo "\n";
}

// Verificar se tabelas foram criadas
echo "\n📊 Verificando Tabelas Criadas:\n";
echo "───────────────────────────────────────────────────────────\n";

$tables_to_check = [
    'tb_audit_login',
    'tb_audit_download',
    'tb_api_log',
    'tb_usuario'
];

foreach ($tables_to_check as $table) {
    try {
        $result = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($result->rowCount() > 0) {
            echo "✅ $table\n";
        } else {
            echo "❌ $table (não encontrada)\n";
        }
    } catch (Exception $e) {
        echo "⚠️  $table (erro ao verificar)\n";
    }
}

echo "\n";
echo "═════════════════════════════════════════════════════════════\n";

if ($error_count === 0) {
    echo "🎉 Migrations executadas com SUCESSO!\n";
    echo "\n✅ Próximos passos:\n";
    echo "   1. Configurar JWT_SECRET\n";
    echo "   2. Testar endpoints com CURL\n";
    echo "   3. Integrar com cliente Delphi\n";
    echo "\n";
    exit(0);
} else {
    echo "⚠️  Migrations executadas com alguns erros\n";
    echo "   (Alguns erros podem ser esperados, como tabelas já existentes)\n\n";
    exit(0);
}
?>
