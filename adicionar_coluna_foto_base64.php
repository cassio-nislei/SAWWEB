<?php
/**
 * Script para adicionar coluna de foto_base64 à tabela tbusuario
 * Executa: php adicionar_coluna_foto_base64.php
 */

// Incluir arquivo de configuração
require_once("includes/padrao.inc.php");

try {
    // SQL para adicionar a coluna
    $sql = "ALTER TABLE tbusuario ADD COLUMN foto_base64 LONGTEXT COLLATE utf8mb4_unicode_ci AFTER msg_almoco";
    
    // Verificar se a coluna já existe
    $checkSql = "SHOW COLUMNS FROM tbusuario WHERE Field = 'foto_base64'";
    $result = $GLOBALS['pdo']->query($checkSql);
    $columnExists = $result->rowCount() > 0;
    
    if ($columnExists) {
        echo "✓ Coluna 'foto_base64' já existe em tbusuario.\n";
    } else {
        // Executar o ALTER TABLE
        $GLOBALS['pdo']->exec($sql);
        echo "✓ Coluna 'foto_base64' adicionada com sucesso à tabela tbusuario!\n";
        echo "  Tipo: LONGTEXT\n";
        echo "  Colação: utf8mb4_unicode_ci\n";
        echo "  Posição: Após coluna 'msg_almoco'\n";
    }
    
    // Mostrar estrutura da tabela
    echo "\n✓ Estrutura atual da tabela:\n";
    $showSql = "DESCRIBE tbusuario";
    $result = $GLOBALS['pdo']->query($showSql);
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo str_pad("Field", 25) . " | " . str_pad("Type", 30) . " | " . str_pad("Null", 6) . " | " . str_pad("Default", 15) . "\n";
    echo str_repeat("-", 85) . "\n";
    
    foreach ($columns as $col) {
        echo str_pad($col['Field'], 25) . " | " 
             . str_pad($col['Type'], 30) . " | " 
             . str_pad($col['Null'], 6) . " | " 
             . str_pad($col['Default'] ?? '-', 15) . "\n";
    }
    
    echo "\n✓ Migração concluída com sucesso!\n";
    
} catch (Exception $e) {
    echo "✗ Erro ao adicionar coluna: " . $e->getMessage() . "\n";
    exit(1);
}
?>
