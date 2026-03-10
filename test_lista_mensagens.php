<?php
/**
 * Teste do listaMensagens.php
 */

require_once(__DIR__ . '/includes/conexao.php');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION["usuariosaw"]["id"] = 1;

echo "=== TESTE DO listaMensagens.php ===\n\n";

// Simular GET
$_GET["idDepto"] = 1;

echo "1. Verificando últimas mensagens com anexo...\n\n";

// Copiar lógica do arquivo
$idDepartamento = intval($_GET["idDepto"] ?? 0);
$usuarioAtualId = intval($_SESSION["usuariosaw"]["id"] ?? 0);

echo "   idDepartamento: " . $idDepartamento . "\n";
echo "   usuarioAtualId: " . $usuarioAtualId . "\n\n";

// SQL CORRIGIDA (com JOIN)
$sqlMensagens = "SELECT 
                    tbc.id,
                    tbu.id AS idUsuario, 
                    tbd.id AS idDepartamento, 
                    tbc.mensagem, 
                    tbu.nome, 
                    tbu.foto AS fotoUsuario,
                    tbd.departamento, 
                    DATE_FORMAT(tbc.data_hora, '%d/%m %H:%i') AS data_hora,
                    TIME_FORMAT(tbc.data_hora, '%H:%i') AS hora,
                    COALESCE(tbc.eh_privada, 0) AS eh_privada,
                    COALESCE(tbc.id_destinatario, 0) AS id_destinatario,
                    COALESCE(tbc.visualizado, 0) AS visualizado,
                    COALESCE(tuu.nome, '') AS nome_destinatario,
                    COALESCE(tbc.id_anexo, 0) AS id_anexo,
                    COALESCE(ta.base64, '') AS anexo_base64,
                    COALESCE(ta.tipo_arquivo, '') AS tipo_arquivo,
                    COALESCE(ta.nome_arquivo, '') AS nome_arquivo
                FROM tbchatoperadores tbc
                INNER JOIN tbusuario tbu ON(tbu.id=tbc.id_usuario)
                LEFT JOIN tbdepartamentos tbd ON(tbd.id=tbc.id_departamento)
                LEFT JOIN tbusuario tuu ON(tuu.id=tbc.id_destinatario)
                LEFT JOIN tbanexos ta ON(ta.id=tbc.id_anexo)
                WHERE DATE(tbc.data_hora) = CURDATE()";

if ($usuarioAtualId > 0) {
    $sqlMensagens .= " AND (
                    tbc.eh_privada = 0 OR 
                    (tbc.eh_privada = 1 AND (tbc.id_usuario = '".intval($usuarioAtualId)."' OR tbc.id_destinatario = '".intval($usuarioAtualId)."'))
                )";
}

$sqlMensagens .= " ORDER BY tbc.id DESC LIMIT 10";

echo "2. Executando query SQL...\n";

$qryMensagens = mysqli_query($conexao, $sqlMensagens);

if (!$qryMensagens) {
    echo "   ❌ Erro na query: " . mysqli_error($conexao) . "\n";
    exit;
}

$totalMensagens = mysqli_num_rows($qryMensagens);
echo "   ✅ Query OK - Total de mensagens: " . $totalMensagens . "\n\n";

if ($totalMensagens === 0) {
    echo "   ℹ️  Nenhuma mensagem encontrada\n";
} else {
    echo "3. Detalhes das mensagens:\n\n";
    
    while ($row = mysqli_fetch_assoc($qryMensagens)) {
        echo "   -----------------------------------\n";
        echo "   ID Mensagem: " . $row["id"] . "\n";
        echo "   Usuário: " . $row["nome"] . "\n";
        echo "   Mensagem: " . substr($row["mensagem"], 0, 60) . (strlen($row["mensagem"]) > 60 ? "..." : "") . "\n";
        echo "   Data/Hora: " . $row["data_hora"] . "\n";
        
        if (!empty($row["id_anexo"])) {
            echo "   \n   📎 ANEXO:\n";
            echo "   - ID Anexo: " . $row["id_anexo"] . "\n";
            echo "   - Tipo: " . $row["tipo_arquivo"] . "\n";
            echo "   - Nome: " . $row["nome_arquivo"] . "\n";
            echo "   - Base64 presente: " . (strlen($row["anexo_base64"]) > 0 ? "✅ SIM (" . strlen($row["anexo_base64"]) . " bytes)" : "❌ NÃO") . "\n";
            
            if (!empty($row["anexo_base64"])) {
                $tip = $row["tipo_arquivo"];
                if ($tip === "IMAGE") {
                    echo "   - Renderização: <img src=\"[base64]\" onclick=\"abrirImagemGrande()\"> \n";
                } else if ($tip === "AUDIO") {
                    echo "   - Renderização: <audio controls> <source src=\"[base64]\"> </audio> \n";
                } else if ($tip === "PDF") {
                    echo "   - Renderização: <a href=\"[base64]\" download> 📄 " . $row["nome_arquivo"] . " </a> \n";
                }
            }
        } else {
            echo "   (sem anexo)\n";
        }
        echo "\n";
    }
}

echo "=== TESTE CONCLUÍDO ===\n";

mysqli_close($conexao);
?>
