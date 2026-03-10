<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexao direto sem includes
$servidor = "104.234.173.105";
$usuario = "root";
$senha = "Ncm@647534";
$banco = "saw_quality";

$conexao = mysqli_connect($servidor, $usuario, $senha, $banco);

if (!$conexao) {
    die("ERRO na conexao: " . mysqli_connect_error());
}

echo "========== ESTRUTURA TABELA TBANEXOS ==========\n\n";

$resultado = mysqli_query($conexao, "DESCRIBE tbanexos");

if (!$resultado) {
    die("ERRO ao descrever tabela: " . mysqli_error($conexao));
}

while ($coluna = mysqli_fetch_assoc($resultado)) {
    echo "Campo: " . $coluna['Field'] . "\n";
    echo "  Tipo: " . $coluna['Type'] . "\n";
    echo "  Null: " . $coluna['Null'] . "\n";
    echo "  Key: " . $coluna['Key'] . "\n";
    echo "  Default: " . $coluna['Default'] . "\n";
    echo "  Extra: " . $coluna['Extra'] . "\n\n";
}

echo "\n========== ULTIMOS 3 REGISTROS ==========\n\n";

$resultado = mysqli_query($conexao, "SELECT id, seq, numero, nome_arquivo, tipo_arquivo, enviado FROM tbanexos ORDER BY id DESC LIMIT 3");

if (mysqli_num_rows($resultado) > 0) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        print_r($linha);
        echo "\n";
    }
} else {
    echo "Nenhum registro encontrado\n";
}

mysqli_close($conexao);
?>
