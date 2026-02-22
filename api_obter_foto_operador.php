<?php
/**
 * API para recuperar foto do operador em base64
 * GET /api_obter_foto_operador.php?id=1
 * 
 * Parâmetros:
 * - id (opcional): ID do usuário. Se não fornecido, usa o ID da sessão atual
 */

header('Content-Type: application/json; charset=utf-8');

require_once("includes/padrao.inc.php");

try {
    // Verificar se usuário está autenticado
    if (!isset($_SESSION["usuariosaw"])) {
        http_response_code(401);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Usuário não autenticado'
        ]);
        exit;
    }
    
    // Obter ID do usuário
    $idUsuario = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION["usuariosaw"]['id'];
    
    if (!$idUsuario) {
        http_response_code(400);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'ID do usuário não especificado'
        ]);
        exit;
    }
    
    // Buscar foto no banco de dados
    $stmt = $GLOBALS['pdo']->prepare("SELECT foto_base64 FROM tbusuario WHERE id = ? LIMIT 1");
    $stmt->execute([$idUsuario]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resultado) {
        http_response_code(404);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Usuário não encontrado'
        ]);
        exit;
    }
    
    $fotoBase64 = $resultado['foto_base64'];
    
    if ($fotoBase64) {
        // Garantir que tenha o prefixo data:image se não tiver
        if (!preg_match('/^data:image/', $fotoBase64)) {
            // Detectar tipo da imagem (default para PNG)
            $fotoBase64 = 'data:image/png;base64,' . $fotoBase64;
        }
        
        http_response_code(200);
        echo json_encode([
            'sucesso' => true,
            'foto' => $fotoBase64,
            'idUsuario' => $idUsuario
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Usuário não possui foto registrada',
            'idUsuario' => $idUsuario
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao recuperar foto: ' . $e->getMessage()
    ]);
}
?>
