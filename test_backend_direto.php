<?php
/**
 * Teste direto da lógica de gravarMensagemChat.php
 */

require_once(__DIR__ . '/includes/conexao.php');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION["usuariosaw"]["id"] = 1; // ID do usuário

echo "=== TESTE DIRETO DO BACKEND ===\n\n";

// Simular POST
$_POST["idDepto"] = 1;
$_POST["strMensagem"] = "Mensagem de teste";
$_POST["ehPrivada"] = 0;
$_POST["idDestinatario"] = 0;

echo "1. Teste 1: Gravar apenas mensagem\n";
echo "   Input: idDepto=1, strMensagem='Mensagem de teste', ehPrivada=0\n\n";

try {
    // Copiar lógica do arquivo
    $idUsuario = intval($_SESSION["usuariosaw"]["id"]);
    $idDepto = intval($_POST["idDepto"] ?? 0);
    $strMensagem = trim($_POST["strMensagem"] ?? "");
    $ehPrivada = intval($_POST["ehPrivada"] ?? 0);
    $idDestinatario = intval($_POST["idDestinatario"] ?? 0);
    $anexoBase64 = $_POST["anexoBase64"] ?? null;

    echo "   Variáveis recebidas:\n";
    echo "   - idUsuario: " . $idUsuario . "\n";
    echo "   - idDepto: " . $idDepto . "\n";
    echo "   - strMensagem: " . $strMensagem . "\n";
    echo "   - ehPrivada: " . $ehPrivada . "\n";
    echo "   - anexoBase64: " . ($anexoBase64 ? "presente" : "null") . "\n\n";

    // Validações
    if (empty($strMensagem) && empty($anexoBase64)) {
        throw new Exception('Mensagem e anexo não podem estar vazios');
    }

    if (!empty($strMensagem) && strlen($strMensagem) > 5000) {
        throw new Exception('Mensagem muito longa (máximo 5000 caracteres)');
    }

    // Validar departamento
    $sqlValidaDepto = "SELECT id FROM tbdepartamentos WHERE id = '$idDepto' LIMIT 1";
    $resultDepto = mysqli_query($conexao, $sqlValidaDepto);
    
    if (!$resultDepto) {
        throw new Exception('Erro ao validar departamento: ' . mysqli_error($conexao));
    }
    
    if (mysqli_num_rows($resultDepto) === 0) {
        throw new Exception('Departamento ' . $idDepto . ' não existe');
    }

    echo "   ✅ Validações OK\n\n";

    // Sanitizar mensagem
    $strMensagem = mysqli_real_escape_string($conexao, $strMensagem);

    // Montar SQL
    $sqlInsert = "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, eh_privada)
                  VALUES('" . $idUsuario . "', '" . $idDepto . "', '" . $strMensagem . "', NOW(), 0)";

    echo "   SQL: " . substr($sqlInsert, 0, 100) . "...\n\n";

    // Executar
    if (mysqli_query($conexao, $sqlInsert)) {
        $idMsg = mysqli_insert_id($conexao);
        echo "   ✅ Mensagem inserida com sucesso!\n";
        echo "   ID da mensagem: " . $idMsg . "\n\n";
    } else {
        throw new Exception('Erro ao salvar mensagem: ' . mysqli_error($conexao));
    }

} catch (Exception $e) {
    echo "   ❌ ERRO: " . $e->getMessage() . "\n\n";
}

// Teste 2: Com anexo
echo "\n2. Teste 2: Gravar mensagem COM anexo\n";
echo "   Input: idDepto=1, strMensagem='Com anexo', anexoBase64='data:image/png;base64,...'\n\n";

$_POST["strMensagem"] = "Mensagem com anexo";
$_POST["anexoBase64"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==";
$_POST["anexoNome"] = "teste.png";
$_POST["anexoTipo"] = "image/png";

try {
    $idUsuario = intval($_SESSION["usuariosaw"]["id"]);
    $idDepto = intval($_POST["idDepto"] ?? 0);
    $strMensagem = trim($_POST["strMensagem"] ?? "");
    $anexoBase64 = $_POST["anexoBase64"] ?? null;
    $anexoNome = $_POST["anexoNome"] ?? null;
    $anexoTipo = $_POST["anexoTipo"] ?? null;

    // Processar anexo
    $idAnexo = null;
    if (!empty($anexoBase64)) {
        echo "   Processando anexo...\n";
        
        if (strpos($anexoBase64, 'data:') !== 0) {
            throw new Exception('Anexo em formato inválido');
        }

        // Extrair MIME
        $tipoMIME = $anexoTipo;
        $binarioBase64 = $anexoBase64;
        
        if (preg_match('/^data:([^;]+);base64,(.+)$/', $anexoBase64, $matches)) {
            $tipoMIME = $matches[1];
            $binarioBase64 = $matches[2];
            echo "   - MIME type: " . $tipoMIME . "\n";
            echo "   - Base64 size: " . strlen($binarioBase64) . " bytes\n";
        }

        // Determinar tipo
        $tipoArquivo = 'ARQUIVO';
        if (strpos($tipoMIME, 'image/') === 0) {
            $tipoArquivo = 'IMAGE';
        }
        echo "   - Tipo de arquivo: " . $tipoArquivo . "\n";

        // Sanitizar nome
        $nomeFileSanitizado = preg_replace('/[^a-zA-Z0-9._-]/', '_', $anexoNome ?? 'arquivo');

        // Escapar para SQL
        $binarioEscapado = mysqli_real_escape_string($conexao, $binarioBase64);
        $base64Escapado = mysqli_real_escape_string($conexao, $anexoBase64);
        
        $sqlInsertAnexo = "INSERT INTO tbanexos (seq, numero, arquivo, base64, nome_arquivo, tipo_arquivo, canal, enviado)
                           VALUES (0, '0', '" . $binarioEscapado . "', '" . $base64Escapado . "', '" . $nomeFileSanitizado . "', '" . $tipoArquivo . "', '0', 1)";
        
        if (!mysqli_query($conexao, $sqlInsertAnexo)) {
            throw new Exception('Erro ao salvar anexo: ' . mysqli_error($conexao));
        }
        
        $idAnexo = mysqli_insert_id($conexao);
        echo "   ✅ Anexo salvo com ID: " . $idAnexo . "\n\n";
    }

    // Validar departamento
    $sqlValidaDepto = "SELECT id FROM tbdepartamentos WHERE id = '$idDepto' LIMIT 1";
    $resultDepto = mysqli_query($conexao, $sqlValidaDepto);
    
    if (mysqli_num_rows($resultDepto) === 0) {
        throw new Exception('Departamento inválido');
    }

    // Sanitizar mensagem
    $strMensagem = mysqli_real_escape_string($conexao, $strMensagem);

    // Montar SQL com anexo
    $idAnexoColuna = $idAnexo ? ", id_anexo" : "";
    $idAnexoValores = $idAnexo ? ", '" . $idAnexo . "'" : "";
    
    $sqlInsert = "INSERT INTO tbchatoperadores(id_usuario, id_departamento, mensagem, data_hora, eh_privada" . $idAnexoColuna . ")
                  VALUES('" . $idUsuario . "', '" . $idDepto . "', '" . $strMensagem . "', NOW(), 0" . $idAnexoValores . ")";

    if (mysqli_query($conexao, $sqlInsert)) {
        $idMsg = mysqli_insert_id($conexao);
        echo "   ✅ Mensagem com anexo inserida com sucesso!\n";
        echo "   ID da mensagem: " . $idMsg . "\n";
        echo "   ID do anexo: " . $idAnexo . "\n\n";
    } else {
        throw new Exception('Erro ao salvar mensagem: ' . mysqli_error($conexao));
    }

} catch (Exception $e) {
    echo "   ❌ ERRO: " . $e->getMessage() . "\n\n";
}

echo "=== TESTE CONCLUÍDO ===\n";
mysqli_close($conexao);
?>
