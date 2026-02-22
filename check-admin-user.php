<?php
$conn = mysqli_connect('104.234.173.105', 'root', 'Ncm@647534', 'saw15');

if (!$conn) {
    echo "Erro na conexão: " . mysqli_connect_error() . "\n";
    exit;
}

$result = mysqli_query($conn, "SELECT id, login, nome, email, situacao, senha FROM tbusuario WHERE login = 'admin'");

if (!$result) {
    echo "Erro na query: " . mysqli_error($conn) . "\n";
    exit;
}

$row = mysqli_fetch_assoc($result);

if ($row) {
    echo "Usuário encontrado:\n";
    print_r($row);
} else {
    echo "Usuário não encontrado\n";
    echo "\nListando todos os usuários:\n";
    $result2 = mysqli_query($conn, "SELECT id, login, nome FROM tbusuario");
    while ($user = mysqli_fetch_assoc($result2)) {
        echo "  - ID: " . $user['id'] . ", Login: " . $user['login'] . ", Nome: " . $user['nome'] . "\n";
    }
}

mysqli_close($conn);
?>
