<?php
/**
 * Script para adicionar a coluna enviar_foto_aut à tabela tbparametros
 * Executa: ALTER TABLE tbparametros ADD COLUMN IF NOT EXISTS enviar_foto_aut varchar(1) DEFAULT '0'
 */

require_once("includes/conexao.php");

echo "Iniciando adição da coluna enviar_foto_aut...<br/>";

// Verifica se a coluna já existe
$verificar = mysqli_query($conexao, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                                    WHERE TABLE_NAME = 'tbparametros' 
                                    AND COLUMN_NAME = 'enviar_foto_aut' 
                                    AND TABLE_SCHEMA = DATABASE()");

if(mysqli_num_rows($verificar) > 0) {
    echo "<font color='green'>✓ Coluna enviar_foto_aut já existe em tbparametros</font><br/>";
} else {
    echo "Adicionando coluna enviar_foto_aut...<br/>";
    
    $sql = "ALTER TABLE tbparametros ADD COLUMN enviar_foto_aut varchar(1) DEFAULT '0' AFTER enviar_audio_aut";
    
    if(mysqli_query($conexao, $sql)) {
        echo "<font color='green'>✓ Coluna enviar_foto_aut adicionada com sucesso!</font><br/>";
        
        // Define um valor padrão para registros existentes
        $update = mysqli_query($conexao, "UPDATE tbparametros SET enviar_foto_aut = '0' WHERE enviar_foto_aut IS NULL");
        echo "<font color='green'>✓ Valores padrão definidos</font><br/>";
    } else {
        echo "<font color='red'>✗ Erro ao adicionar coluna: " . mysqli_error($conexao) . "</font><br/>";
    }
}

// Verifica a estrutura resultante
echo "<br/>Estrutura final da tabela tbparametros:<br/>";
$resultado = mysqli_query($conexao, "DESCRIBE tbparametros");

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

while($row = mysqli_fetch_array($resultado)) {
    echo "<tr>";
    echo "<td>".$row[0]."</td>";
    echo "<td>".$row[1]."</td>";
    echo "<td>".$row[2]."</td>";
    echo "<td>".$row[3]."</td>";
    echo "<td>".$row[4]."</td>";
    echo "<td>".$row[5]."</td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($conexao);
?>
