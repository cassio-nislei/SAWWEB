<?php
require_once("../../../includes/padrao.inc.php");

// Verificar se a coluna foto já existe
$query = "SHOW COLUMNS FROM tbusuario WHERE Field = 'foto'";
$result = mysqli_query($conexao, $query);

if (mysqli_num_rows($result) == 0) {
    // Coluna não existe, criar
    $alter = "ALTER TABLE tbusuario ADD COLUMN foto LONGTEXT COLLATE utf8mb4_unicode_ci AFTER nome_chat";
    
    if (mysqli_query($conexao, $alter)) {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 10px 0;'>";
        echo "<strong>✓ Coluna 'foto' adicionada com sucesso!</strong>";
        echo "</div>";
    } else {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0;'>";
        echo "<strong>✗ Erro ao adicionar coluna:</strong> " . mysqli_error($conexao);
        echo "</div>";
    }
} else {
    echo "<div style='background-color: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 10px 0;'>";
    echo "<strong>ℹ Coluna 'foto' já existe na tabela tbusuario</strong>";
    echo "</div>";
}

// Exibir estrutura da tabela
echo "<h3 style='margin-top: 20px;'>Estrutura da Tabela tbusuario:</h3>";
echo "<table style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
echo "<tr style='background-color: #f5f5f5;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Campo</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Tipo</th>";
echo "</tr>";

$describe = mysqli_query($conexao, "DESCRIBE tbusuario");
while ($row = mysqli_fetch_assoc($describe)) {
    $bgColor = ($row['Field'] == 'foto') ? '#fff3cd' : '#fff';
    echo "<tr style='background-color: $bgColor;'>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['Field']) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($row['Type']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
