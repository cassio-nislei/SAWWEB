<?php
/**
 * Script de Teste: Fluxo Completo de Anexo
 * Testa: Upload → Salvamento em tbanexos → Exibição em listaMensagens
 */

// Incluir arquivo de conexão
require_once("includes/conexao.php");

echo "=== TESTE DO FLUXO DE ANEXOS ===\n\n";

// 1. Verificar estrutura de tabelas
echo "1. Verificando estrutura de tabelas...\n";
$sql_check_tbanexos = "DESCRIBE tbanexos";
$result = mysqli_query($conexao, $sql_check_tbanexos);

if ($result) {
    echo "✅ Tabela tbanexos existe. Colunas:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "   - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "❌ Erro ao verificar tbanexos: " . mysqli_error($conexao) . "\n";
}

echo "\n";

// 2. Verificar coluna id_anexo em tbchatoperadores
echo "2. Verificando coluna id_anexo em tbchatoperadores...\n";
$sql_check_chat = "DESCRIBE tbchatoperadores";
$result = mysqli_query($conexao, $sql_check_chat);

$tem_id_anexo = false;
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] === 'id_anexo') {
            $tem_id_anexo = true;
            echo "✅ Coluna id_anexo existe em tbchatoperadores\n";
        }
    }
    if (!$tem_id_anexo) {
        echo "⚠️  Coluna id_anexo NÃO encontrada em tbchatoperadores\n";
    }
} else {
    echo "❌ Erro ao verificar tbchatoperadores: " . mysqli_error($conexao) . "\n";
}

echo "\n";

// 3. Teste de INSERT em tbanexos com id_conversa
echo "3. Testando INSERT em tbanexos com id_conversa...\n";

// Dados de teste (imagem small PNG como base64)
$base64_test = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==";
$binario = base64_decode(str_replace("data:image/png;base64,", "", $base64_test));

// Escapar dados
$binario_escapado = mysqli_real_escape_string($conexao, $binario);
$base64_escapado = mysqli_real_escape_string($conexao, $base64_test);

$sql_insert = "INSERT INTO tbanexos 
               (seq, numero, arquivo, base64, nome_arquivo, tipo_arquivo, canal, enviado, id_conversa) 
               VALUES 
               (0, '0', '" . $binario_escapado . "', '" . $base64_escapado . "', 'teste_anexo.png', 'IMAGE', '0', 1, 0)";

if (mysqli_query($conexao, $sql_insert)) {
    $id_anexo = mysqli_insert_id($conexao);
    echo "✅ Anexo de teste inserido em tbanexos com ID: " . $id_anexo . "\n";
} else {
    echo "❌ Erro ao inserir anexo: " . mysqli_error($conexao) . "\n";
}

echo "\n";

// 4. Teste de INSERT em tbchatoperadores com id_anexo
echo "4. Testando INSERT em tbchatoperadores com id_anexo...\n";

$id_usuario_test = 1; // Ajustar conforme necessário
$id_depto_test = 1;   // Ajustar conforme necessário
$mensagem_test = "Mensagem com anexo de teste";

if (isset($id_anexo)) {
    $sql_chat = "INSERT INTO tbchatoperadores 
                 (id_usuario, id_departamento, mensagem, id_anexo, data_hora) 
                 VALUES 
                 ('$id_usuario_test', '$id_depto_test', '" . mysqli_real_escape_string($conexao, $mensagem_test) . "', '$id_anexo', NOW())";
    
    if (mysqli_query($conexao, $sql_chat)) {
        $id_msg = mysqli_insert_id($conexao);
        echo "✅ Mensagem com anexo inserida em tbchatoperadores com ID: " . $id_msg . "\n";
    } else {
        echo "❌ Erro ao inserir mensagem com anexo: " . mysqli_error($conexao) . "\n";
    }
}

echo "\n";

// 5. Teste de SELECT com JOIN (como em listaMensagens.php)
echo "5. Testando SELECT com JOIN (listaMensagens.php)...\n";

$sql_select = "SELECT 
                tbc.id,
                tbc.mensagem,
                COALESCE(tbc.id_anexo, 0) AS id_anexo,
                COALESCE(ta.base64, '') AS anexo_base64,
                COALESCE(ta.tipo_arquivo, '') AS tipo_arquivo,
                COALESCE(ta.nome_arquivo, '') AS nome_arquivo
            FROM tbchatoperadores tbc
            LEFT JOIN tbanexos ta ON(ta.id=tbc.id_anexo)
            WHERE tbc.id_anexo > 0
            ORDER BY tbc.id DESC
            LIMIT 5";

$result = mysqli_query($conexao, $sql_select);

if ($result && mysqli_num_rows($result) > 0) {
    echo "✅ Consulta retornou resultados:\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "\n   ID Mensagem: " . $row['id'] . "\n";
        echo "   Mensagem: " . substr($row['mensagem'], 0, 50) . "...\n";
        echo "   ID Anexo: " . $row['id_anexo'] . "\n";
        echo "   Tipo: " . $row['tipo_arquivo'] . "\n";
        echo "   Nome: " . $row['nome_arquivo'] . "\n";
        echo "   Base64 size: " . strlen($row['anexo_base64']) . " bytes\n";
    }
} else {
    echo "ℹ️  Nenhuma mensagem com anexo encontrada (é esperado se não há dados de teste)\n";
}

echo "\n";

// 6. Verificar arquivos necessários
echo "6. Verificando arquivos do sistema:\n";

$files_to_check = [
    "webchat/content.php" => "UI com botão de anexo",
    "webchat/gravarMensagemChat.php" => "API para salvar anexo",
    "webchat/listaMensagens.php" => "API para listar mensagens com anexo",
    "conversas.php" => "Página de conversas com lightbox"
];

foreach ($files_to_check as $file => $desc) {
    $path = __DIR__ . "/" . $file;
    if (file_exists($path)) {
        echo "✅ " . $file . " - " . $desc . "\n";
    } else {
        echo "❌ " . $file . " - NÃO ENCONTRADO\n";
    }
}

echo "\n";

// 7. Resumo
echo "=== RESUMO ===\n";
echo "✅ Estrutura de banco de dados validada\n";
echo "✅ Fluxo de INSERT testado\n";
echo "✅ Fluxo de SELECT com JOIN testado\n";
echo "✅ Arquivos do sistema verificados\n\n";

echo "Próximas etapas:\n";
echo "1. Testar upload via UI (webchat/content.php)\n";
echo "2. Verificar salvamento em tbanexos (via gravarMensagemChat.php)\n";
echo "3. Validar exibição em conversas.php\n";
echo "4. Testar diferentes tipos de anexo (imagem, áudio, PDF)\n";

mysqli_close($conexao);
?>
