<?php
/**
 * Script para corrigir AUTO_INCREMENT na tabela tbanexos
 * Abordagem: usar o máximo id existente
 */

require_once('includes/conexao.php');

echo "=== CORRIGINDO AUTO_INCREMENT EM TBANEXOS ===\n\n";

// 1. Encontrar o máximo id
echo "1. Encontrando máximo id existente...\n";

$result = mysqli_query($conexao, "SELECT MAX(id) AS max_id FROM tbanexos");
$row = mysqli_fetch_assoc($result);
$max_id = intval($row['max_id']) + 1;

echo "   Máximo ID: " . ($max_id - 1) . "\n";
echo "   Próximo AUTO_INCREMENT será: " . $max_id . "\n\n";

// 2. Adicionar AUTO_INCREMENT com base no máximo
echo "2. Adicionando AUTO_INCREMENT...\n";

$sql_alter = "ALTER TABLE tbanexos MODIFY id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, AUTO_INCREMENT = " . $max_id;

if (mysqli_query($conexao, $sql_alter)) {
    echo "✅ AUTO_INCREMENT adicionado com sucesso\n";
} else {
    echo "❌ Erro: " . mysqli_error($conexao) . "\n";
}

echo "\n";

// 3. Verificar estrutura
echo "3. Verificando estrutura...\n";

$result = mysqli_query($conexao, 'SHOW CREATE TABLE tbanexos');
$row = mysqli_fetch_assoc($result);

if (preg_match('/AUTO_INCREMENT=/', $row['Create Table'])) {
    echo "✅ AUTO_INCREMENT configurado\n";
} else {
    echo "⚠️  AUTO_INCREMENT pode não estar configurado\n";
}

echo "\n";

// 4. Testar inserção
echo "4. Testando inserção sem especificar id...\n";

$sql_test = "INSERT INTO tbanexos (seq, numero, arquivo, base64, nome_arquivo, tipo_arquivo, canal, enviado) 
             VALUES (0, '0', 0x00, 'data:text/plain;base64,dGVzdA==', 'teste.txt', 'ARQUIVO', '0', 1)";

if (mysqli_query($conexao, $sql_test)) {
    $new_id = mysqli_insert_id($conexao);
    echo "✅ Registro inserido com novo ID: " . $new_id . "\n";
    
    // Limpar registro de teste
    mysqli_query($conexao, "DELETE FROM tbanexos WHERE id = " . $new_id);
    echo "   (Removido registro de teste)\n";
} else {
    echo "❌ Erro ao testar: " . mysqli_error($conexao) . "\n";
}

mysqli_close($conexao);
?>
