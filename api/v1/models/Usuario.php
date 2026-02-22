<?php
/**
 * SAW API v1 - Model Usuario
 */

class Usuario
{
    /**
     * Busca usuário por ID
     */
    public static function getById($id)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT 
                    id,
                    nome,
                    email,
                    usuario,
                    id_atendente,
                    setor,
                    ativo,
                    permissoes,
                    created_at,
                    updated_at
                FROM tbusuario
                WHERE id = ?
                LIMIT 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (Exception $e) {
            error_log("getById Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca usuário por username
     */
    public static function getByUsername($usuario)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT 
                    id,
                    nome,
                    email,
                    usuario,
                    id_atendente,
                    setor,
                    ativo,
                    senha,
                    created_at
                FROM tbusuario
                WHERE usuario = ?
                LIMIT 1
            ");
            $stmt->execute([$usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (Exception $e) {
            error_log("getByUsername Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista usuários com paginação
     */
    public static function list($page = 1, $perPage = 50, $filters = [])
    {
        try {
            $offset = ($page - 1) * $perPage;
            $db = Database::getInstance();

            // Construir where clause
            $where = ['ativo = 1'];
            $params = [];

            if (isset($filters['setor']) && !empty($filters['setor'])) {
                $where[] = 'setor = ?';
                $params[] = $filters['setor'];
            }

            if (isset($filters['nome']) && !empty($filters['nome'])) {
                $where[] = 'nome LIKE ?';
                $params[] = '%' . $filters['nome'] . '%';
            }

            $whereClause = implode(' AND ', $where);

            // Contar total
            $stmtCount = $db->prepare("
                SELECT COUNT(*) as total
                FROM tbusuario
                WHERE $whereClause
            ");
            $stmtCount->execute($params);
            $totalResult = $stmtCount->fetch(PDO::FETCH_ASSOC);
            $total = (int)$totalResult['total'];

            // Buscar registros
            $allParams = array_merge($params, [$perPage, $offset]);
            
            $stmt = $db->prepare("
                SELECT 
                    id,
                    nome,
                    email,
                    usuario,
                    id_atendente,
                    setor,
                    ativo,
                    created_at,
                    updated_at
                FROM tbusuario
                WHERE $whereClause
                ORDER BY nome ASC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute($allParams);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'data' => $usuarios,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage
            ];

        } catch (Exception $e) {
            error_log("list Error: " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'page' => $page, 'perPage' => $perPage];
        }
    }
}
?>
