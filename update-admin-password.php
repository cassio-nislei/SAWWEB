<?php
$conn = mysqli_connect('104.234.173.105', 'root', 'Ncm@647534', 'saw15');

if (!$conn) {
    echo "Erro: " . mysqli_connect_error() . "\n";
    exit;
}

// Atualizar a senha do usuário admin para '123456'
$query = "UPDATE tbusuario SET senha = '123456' WHERE login = 'admin'";

if (mysqli_query($conn, $query)) {
    echo "✅ Senha atualizada com sucesso!\n";
    
    // Verificar
    $result = mysqli_query($conn, "SELECT login, senha FROM tbusuario WHERE login = 'admin'");
    $user = mysqli_fetch_assoc($result);
    echo "Novo usuário: " . json_encode($user) . "\n";
} else {
    echo "❌ Erro ao atualizar: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
