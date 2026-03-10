<?php
/**
 * Script para limpar duplicatas em tbanexos
 */

require_once('includes/conexao.php');

echo "=== VERIFICANDO DUPLICATAS EM TBANEXOS ===\n\n";

// 1. Verificar duplicatas
echo "1. Procurando IDs duplicados...\n";

$sql_dup = "SELECT id, COUNT(*) as count FROM tbanexos GROUP BY id HAVING count > 1";
$result = mysqli_query($conexao, $sql_dup);

if (mysqli_num_rows($result) > 0) {
    echo "   ⚠️  IDs duplicados encontrados:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "      - ID " . $row['id'] . " aparece " . $row['count'] . " vezes\n";
    }
    
    echo "\n2. Limpando duplicatas (mantendo primeiro registro)...\n";
    
    // Para cada ID duplicado
    $result = mysqli_query($conexao, $sql_dup);
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['id'];
        
        // Deletar registros duplicados (mantendo o primeiro)
        $delete_sql = "DELETE FROM tbanexos WHERE id = " . $id . " LIMIT " . ($row['count'] - 1);
        
        if (mysqli_query($conexao, $delete_sql)) {
            echo "   ✅ Limpas " . ($row['count'] - 1) . " cópia(s) de ID " . $id . "\n";
        } else {
            echo "   ❌ Erro ao limpar ID " . $id . ": " . mysqli_error($conexao) . "\n";
        }
    }
} else {
    echo "   ✅ Nenhuma duplicata encontrada\n";
}

echo "\n3. Agora tentando adicionar AUTO_INCREMENT...\n";

$result = mysqli_query($conexao, "SELECT MAX(id) AS max_id FROM tbanexos");
$row = mysqli_fetch_assoc($result);
$max_id = intval($row['max_id']) + 1;

$sql_alter = "ALTER TABLE tbanexos MODIFY id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, AUTO_INCREMENT = " . $max_id;

if (mysqli_query($conexao, $sql_alter)) {
    echo "   ✅ AUTO_INCREMENT adicionado com sucesso\n";
} else {
    echo "   ❌ Erro: " . mysqli_error($conexao) . "\n";
}

mysqli_close($conexao);
?>
