<?php
/**
 * Script para adicionar coluna de anexo em base64 na conversa
 */
// Conectar direto sem usar padrao.inc.php
require_once("includes/conexao.php");

$alerta = "";
$sucesso = false;

try {
    // Verificar se coluna já existe
    $checkColuna = mysqli_query($conexao, "SHOW COLUMNS FROM tbchatoperadores LIKE 'anexo_base64'");
    
    if (mysqli_num_rows($checkColuna) === 0) {
        // Coluna não existe, criar
        $sqlAlter = "ALTER TABLE tbchatoperadores ADD COLUMN anexo_base64 LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci AFTER mensagem";
        
        if (mysqli_query($conexao, $sqlAlter)) {
            $alerta = "✅ Coluna 'anexo_base64' adicionada com sucesso!";
            $sucesso = true;
        } else {
            $alerta = "❌ Erro ao adicionar coluna: " . mysqli_error($conexao);
        }
    } else {
        $alerta = "ℹ️  Coluna 'anexo_base64' já existe na tabela";
        $sucesso = true;
    }
} catch (Exception $e) {
    $alerta = "❌ Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Adicionar Coluna de Anexo</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciador de Banco de Dados</h1>
        
        <div class="alert <?php echo $sucesso ? 'success' : 'error'; ?> $alerta">
            <?php echo $alerta; ?>
        </div>
        
        <?php if ($sucesso): ?>
            <p>✅ Clique <a href="conversas.php">aqui</a> para voltar às conversas.</p>
        <?php endif; ?>
        
        <hr>
        <p><small>Este script adiciona a coluna 'anexo_base64' na tabela 'tbchatoperadores' para armazenar imagens e áudios codificados em base64.</small></p>
    </div>
</body>
</html>
