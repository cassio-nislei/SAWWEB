<?php
require_once('includes/conexao.php');
$result = mysqli_query($conexao, "SHOW COLUMNS FROM tbchatoperadores LIKE 'anexo_base64'");
if ($result && mysqli_num_rows($result) === 0) {
    $alter = mysqli_query($conexao, "ALTER TABLE tbchatoperadores ADD COLUMN anexo_base64 LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER mensagem");
    echo $alter ? 'OK: Coluna adicionada' : 'ERRO: ' . mysqli_error($conexao);
} else {
    echo 'OK: Coluna ja existe';
}
mysqli_close($conexao);
