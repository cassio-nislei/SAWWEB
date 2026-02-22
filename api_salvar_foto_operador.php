<?php
/**
 * API para salvar foto do operador em base64
 * POST /api_salvar_foto_operador.php
 * 
 * Parâmetros:
 * - fotoBas64 (string): Imagem em base64
 * - tipo (string): Opcional - tipo da imagem (image/png, image/jpeg, etc)
 */

header('Content-Type: application/json; charset=utf-8');

// Verificar se usuário está autenticado
if (!isset($_SESSION["usuariosaw"])) {
    http_response_code(401);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Usuário não autenticado'
    ]);
    exit;
}

require_once("includes/padrao.inc.php");

try {
    // Obter dados do POST
    $dados = json_decode(file_get_contents('php://input'), true);
    
    if (!$dados) {
        $dados = $_POST;
    }
    
    // Validar entrada
    if (!isset($dados['fotoBase64']) || empty($dados['fotoBase64'])) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Foto em base64 não fornecida'
        ]);
        exit;
    }
    
    $fotoBase64 = $dados['fotoBase64'];
    $idUsuario = $_SESSION["usuariosaw"]['id'] ?? null;
    
    if (!$idUsuario) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'ID do usuário não encontrado na sessão'
        ]);
        exit;
    }
    
    // Validar se é base64 válido
    if (!preg_match('/^data:image\/(png|jpeg|jpg|gif|webp);base64,/', $fotoBase64)) {
        // Se não tiver o prefixo data:image, verificar se é base64 puro
        if (!base64_encode(base64_decode($fotoBase64, true)) === $fotoBase64) {
            // Remover prefixo se existente
            $fotoBase64 = preg_replace('/^data:image\/[a-z]+;base64,/', '', $fotoBase64);
        }
    } else {
        // Remover o prefixo data:image/...;base64,
        $fotoBase64 = preg_replace('/^data:image\/[a-z]+;base64,/', '', $fotoBase64);
    }
    
    // Verificar tamanho máximo (5MB em base64)
    if (strlen($fotoBase64) > 5000000) {
        http_response_code(413);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Arquivo muito grande (máximo 5MB)'
        ]);
        exit;
    }
    
    // Preparar statement
    $stmt = $GLOBALS['pdo']->prepare("UPDATE tbusuario SET foto_base64 = ? WHERE id = ?");
    $resultado = $stmt->execute([$fotoBase64, $idUsuario]);
    
    if ($resultado) {
        http_response_code(200);
        echo json_encode([
            'sucesso' => true,
            'mensagem' => 'Foto salva com sucesso',
            'idUsuario' => $idUsuario
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao salvar foto no banco de dados'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro: ' . $e->getMessage()
    ]);
}
?>
