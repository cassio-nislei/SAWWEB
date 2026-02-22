<?php
/**
 * SAW API v1 - Controller Usuários
 * 
 * Endpoints:
 * - GET /usuarios
 * - GET /usuarios/me
 */

class UsuariosController {
    
    /**
     * GET /api/v1/usuarios
     * Lista todos os usuários
     */
    public static function list() {
        try {
            $query = Router::getQueryParams();
            $page = isset($query['page']) ? (int)$query['page'] : 1;
            $perPage = isset($query['perPage']) ? (int)$query['perPage'] : 50;
            $perPage = min($perPage, 100); // Limita a 100
            
            $situacao = isset($query['situacao']) ? $query['situacao'] : 'A';
            
            $db = Database::getInstance();
            
            // Contar total
            $stmtCount = $db->prepare("
                SELECT COUNT(*) as total
                FROM tbusuario
                WHERE situacao = ?
            ");
            $stmtCount->execute([$situacao]);
            $totalResult = $stmtCount->fetch(PDO::FETCH_ASSOC);
            $total = (int)$totalResult['total'];
            
            // Buscar usuários com paginação
            $offset = ($page - 1) * $perPage;
            
            $stmt = $db->prepare("
                SELECT 
                    id,
                    nome,
                    email,
                    login,
                    situacao
                FROM tbusuario
                WHERE situacao = ?
                ORDER BY nome ASC
                LIMIT ?,?
            ");
            
            $stmt->execute([$situacao, $offset, $perPage]);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            Response::paginated(
                $usuarios,
                $total,
                $page,
                $perPage,
                'Usuários listados com sucesso'
            );
            
        } catch (Exception $e) {
            Response::internalError($e->getMessage());
        }
    }
    
    /**
     * GET /api/v1/usuarios/me
     * Retorna dados do usuário autenticado
     */
    public static function me() {
        try {
            // Extrair token do header
            $token = self::extractToken();
            
            if (!$token) {
                Response::error('Token não fornecido', 401);
                return;
            }
            
            // Decodificar token
            $decoded = JWT::decode($token);
            
            if (!$decoded) {
                Response::error('Token inválido ou expirado', 401);
                return;
            }
            
            // Buscar usuário atualizado (MySQLi)
            $conn = Database::connect();
            if (!$conn) {
                Response::error('Erro de conexão com banco', 500);
                return;
            }
            
            $query = "SELECT id, nome, email, login, situacao FROM tbusuario WHERE id = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            
            if (!$stmt) {
                Response::error('Erro ao preparar query', 500);
                return;
            }
            
            $stmt->bind_param('i', $decoded['id']);
            if (!$stmt->execute()) {
                Response::error('Erro ao executar query', 500);
                return;
            }
            
            $result = $stmt->get_result();
            $usuario = $result->fetch_assoc();
            $stmt->close();
            
            if (!$usuario) {
                Response::notFound('Usuário não encontrado');
                return;
            }
            
            // Adicionar informações de token
            $usuario['token_expira_em'] = date('c', $decoded['exp']);
            $usuario['tempo_restante_segundos'] = $decoded['exp'] - time();
            
            Response::success($usuario, 'Dados do usuário autenticado');
            
        } catch (Exception $e) {
            Response::internalError($e->getMessage());
        }
    }
    
    /**
     * Extrai token do header Authorization
     */
    private static function extractToken() {
        // Tentar getallheaders() primeiro (mais compatível com Apache)
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                $header = $headers['Authorization'];
                if (preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
                    return trim($matches[1]);
                }
            }
        }
        
        // Fallback para $_SERVER
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!empty($header) && preg_match('/Bearer\s+(.+)/i', $header, $matches)) {
            return trim($matches[1]);
        }
        
        return null;
    }
}
?>
