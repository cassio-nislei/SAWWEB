<?php
/**
 * Teste de Conexão MySQL - Descobrir Credenciais Corretas
 * 
 * Uso: php test-db-connection.php
 */

echo "═════════════════════════════════════════════════════════════\n";
echo "  Teste de Conexão MySQL\n";
echo "═════════════════════════════════════════════════════════════\n\n";

// Array de credenciais para testar
$credentials = [
    // IP externo
    [
        'host' => '104.234.173.105',
        'user' => 'root',
        'password' => 'saw15',
        'database' => 'saw15',
        'label' => '1. IP Externo (saw15)'
    ],
    // Alternativas
    [
        'host' => '104.234.173.105',
        'user' => 'root',
        'password' => 'root',
        'database' => 'saw15',
        'label' => '2. IP Externo (senha: root)'
    ],
    [
        'host' => '104.234.173.105',
        'user' => 'saw',
        'password' => 'saw15',
        'database' => 'saw15',
        'label' => '3. Usuário saw'
    ],
    [
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'saw15',
        'database' => 'saw15',
        'label' => '4. Localhost'
    ],
    [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => 'saw15',
        'database' => 'saw15',
        'label' => '5. 127.0.0.1'
    ],
];

$success = false;
$successful_creds = null;

foreach ($credentials as $cred) {
    echo "[TESTE] {$cred['label']}\n";
    echo "        Host: {$cred['host']} | User: {$cred['user']}\n";
    
    try {
        $dsn = "mysql:host={$cred['host']};charset=utf8mb4";
        $pdo = new PDO($dsn, $cred['user'], $cred['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Testar conexão
        $result = $pdo->query("SELECT 1");
        
        echo "        ✅ CONEXÃO COM SUCESSO!\n";
        
        // Tentar selecionar banco
        try {
            $pdo->query("USE {$cred['database']}");
            echo "        ✅ Banco '{$cred['database']}' acessível\n";
        } catch (Exception $e) {
            echo "        ⚠️  Banco '{$cred['database']}' não acessível\n";
        }
        
        echo "\n";
        $success = true;
        $successful_creds = $cred;
        
    } catch (PDOException $e) {
        $error = $e->getMessage();
        
        // Extrair erro específico
        if (strpos($error, 'Access denied') !== false) {
            echo "        ❌ Acesso negado (user/password incorretos)\n";
        } elseif (strpos($error, 'Connection refused') !== false) {
            echo "        ❌ Conexão recusada (host incorreto ou MySQL offline)\n";
        } else {
            echo "        ❌ Erro: " . substr($error, 0, 50) . "...\n";
        }
        echo "\n";
    }
}

echo "═════════════════════════════════════════════════════════════\n\n";

if ($success) {
    echo "✅ CREDENCIAIS ENCONTRADAS!\n\n";
    echo "Use estas credenciais:\n";
    echo "   Host: {$successful_creds['host']}\n";
    echo "   User: {$successful_creds['user']}\n";
    echo "   Password: {$successful_creds['password']}\n";
    echo "   Database: {$successful_creds['database']}\n\n";
    
    echo "Atualize o arquivo execute-migrations.php com:\n";
    echo "   \$db_host = '{$successful_creds['host']}';\n";
    echo "   \$db_user = '{$successful_creds['user']}';\n";
    echo "   \$db_password = '{$successful_creds['password']}';\n";
    echo "   \$db_name = '{$successful_creds['database']}';\n";
} else {
    echo "❌ NÃO FOI POSSÍVEL CONECTAR COM NENHUMA CREDENCIAL\n\n";
    
    echo "Possíveis causas:\n";
    echo "   1. MySQL não está rodando no servidor\n";
    echo "   2. Firewall bloqueando acesso\n";
    echo "   3. Credenciais estão erradas\n";
    echo "   4. IP do servidor está incorreto\n\n";
    
    echo "Verifique em seu Docker/VPS:\n";
    echo "   • Se MySQL está rodando: docker ps | grep mysql\n";
    echo "   • Se porta 3306 está aberta: telnet 104.234.173.105 3306\n";
    echo "   • Logs do MySQL: docker logs <container_name>\n";
}

?>
