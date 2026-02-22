<?php
// Conectar ao banco diretamente
$usuarioBD = getenv('DB_USER') ?: 'root';
$senhaBD   = getenv('DB_PASS') ?: 'Ncm@647534';
$servidorBD= getenv('DB_HOST') ?: '104.234.173.105';
$bancoBD   = getenv('DB_NAME') ?: 'saw_quality';

$conexao = mysqli_connect($servidorBD, $usuarioBD, $senhaBD, $bancoBD);

if (!$conexao) {
    die("❌ Erro de conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");

// Verificar se a coluna 'foto' existe
$resultado = mysqli_query($conexao, "SHOW COLUMNS FROM tbusuario LIKE 'foto'");

if (mysqli_num_rows($resultado) == 0) {
    // Coluna não existe, criar
    $sql = "ALTER TABLE tbusuario ADD COLUMN foto LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER nome_chat";
    
    if (mysqli_query($conexao, $sql)) {
        echo "✅ Coluna 'foto' criada com sucesso em tbusuario!";
    } else {
        echo "❌ Erro ao criar coluna: " . mysqli_error($conexao);
    }
} else {
    echo "✅ Coluna 'foto' já existe em tbusuario!";
}

mysqli_close($conexao);
?>
