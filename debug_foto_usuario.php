<?php
require_once("includes/padrao.inc.php");

// Verificar se usuário está logado
if (empty($_SESSION["usuariosaw"]["id"])) {
    echo "<h3 style='color:red;'>❌ Usuário não logado</h3>";
    exit;
}

$idUsuario = $_SESSION["usuariosaw"]["id"];
echo "<h2>🔍 Diagnóstico de Foto do Usuário</h2>";
echo "<hr>";

// 1. Verificar se coluna existe
echo "<h4>1. Verificando coluna 'foto' na tabela:</h4>";
$verificaColuna = mysqli_query($conexao, "SHOW COLUMNS FROM tbusuario LIKE 'foto'");
if (mysqli_num_rows($verificaColuna) > 0) {
    $coluna = mysqli_fetch_assoc($verificaColuna);
    echo "<p style='color:green;'>✅ Coluna 'foto' existe</p>";
    echo "<pre>Tipo: " . $coluna['Type'] . "\nNull: " . $coluna['Null'] . "\nDefault: " . $coluna['Default'] . "</pre>";
} else {
    echo "<p style='color:red;'>❌ Coluna 'foto' NÃO existe</p>";
}

echo "<hr>";

// 2. Buscar dados da foto do usuário atual
echo "<h4>2. Procurando por foto do usuário ID: " . $idUsuario . "</h4>";
$sql = "SELECT id, nome, foto FROM tbusuario WHERE id = '" . intval($idUsuario) . "' LIMIT 1";
$resultado = mysqli_query($conexao, $sql);

if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    echo "<p><strong>Nome:</strong> " . htmlspecialchars($usuario['nome']) . "</p>";
    
    if (!empty($usuario['foto'])) {
        echo "<p style='color:green;'>✅ Foto encontrada no banco de dados</p>";
        
        // Verificar tamanho
        $tamanho = strlen($usuario['foto']);
        echo "<p><strong>Tamanho da foto:</strong> " . number_format($tamanho / 1024, 2) . " KB</p>";
        
        // Verificar se é data URI
        if (strpos($usuario['foto'], 'data:image') === 0) {
            echo "<p style='color:green;'>✅ Foto é uma data URI válida</p>";
            // Extrair tipo MIME
            preg_match('/data:([^;]+)/', $usuario['foto'], $matches);
            $tipoMIME = $matches[1] ?? 'desconhecido';
            echo "<p><strong>Tipo MIME:</strong> " . htmlspecialchars($tipoMIME) . "</p>";
        } else if (preg_match('/^[A-Za-z0-9+\/=]+$/', substr($usuario['foto'], 0, 100))) {
            echo "<p style='color:orange;'>⚠️ Foto parece ser apenas base64 (sem prefixo data:image)</p>";
        } else {
            echo "<p style='color:red;'>❌ Foto tem formato desconhecido</p>";
        }
        
        // Mostrar preview
        echo "<h4>Preview da Foto:</h4>";
        if (strpos($usuario['foto'], 'data:image') === 0) {
            echo "<img src='" . htmlspecialchars($usuario['foto']) . "' style='max-width:200px; max-height:200px; border:1px solid #ccc;'>";
        } else {
            echo "<p style='color:orange;'>Não é possível fazer preview (falta prefixo data:image)</p>";
            echo "<p><strong>Primeiros 100 caracteres:</strong></p>";
            echo "<code>" . htmlspecialchars(substr($usuario['foto'], 0, 100)) . "...</code>";
        }
    } else {
        echo "<p style='color:orange;'>⚠️ Campo 'foto' está vazio</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Usuário não encontrado</p>";
}

echo "<hr>";

// 3. Testar carregarFotoUsuario.php
echo "<h4>3. Testando script carregarFotoUsuario.php:</h4>";
echo "<img src='carregarFotoUsuario.php' style='max-width:150px; max-height:150px; border:2px solid blue;' onerror=\"alert('Erro ao carregar imagem')\">";
echo "<p>Se a imagem apareceu acima, o script está funcionando!</p>";

echo "<hr>";

// 4. Lista de usuários com fotos
echo "<h4>4. Todos os usuários com fotos salvas:</h4>";
$sqlTodos = "SELECT id, nome, LENGTH(foto) as tamanho_foto FROM tbusuario WHERE foto IS NOT NULL AND foto != '' ORDER BY nome";
$resultadoTodos = mysqli_query($conexao, $sqlTodos);

if (mysqli_num_rows($resultadoTodos) > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Tamanho Foto (KB)</th></tr>";
    while ($row = mysqli_fetch_assoc($resultadoTodos)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
        echo "<td>" . number_format($row['tamanho_foto'] / 1024, 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:orange;'>⚠️ Nenhum usuário tem foto salva ainda</p>";
}

?>
