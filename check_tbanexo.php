<?php
require_once('includes/conexao.php');
$result = mysqli_query($conexao, 'DESCRIBE tbanexo');
echo "=== Estrutura da tabela tbanexo ===\n";
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
mysqli_close($conexao);
