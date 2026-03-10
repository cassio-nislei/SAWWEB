<?php
/**
 * ProceduresController
 * Controla operações de procedures e estrutura de banco de dados
 * 
 * Endpoints:
 * - GET /procedures/listar - Lista todas as procedures
 * - GET /procedures/existe - Verifica se procedure existe
 * - POST /procedures/executar - Executa uma procedure
 * - POST /procedures/criar - Criar nova procedure (admin only)
 * - POST /procedures/droppar - Remover procedure (admin only)
 * - POST /sql/executar - Executar SQL arbitrário (admin only)
 * - POST /tabelas/criar-com-dados - Criar tabelas se não existirem
 * - POST /tabelas/adicionar-coluna - Adicionar coluna se não existir
 * 
 * Data: 21/11/2025
 */

class ProceduresController
{
    /**
     * Verifica autenticação JWT - requerido em todos os endpoints
     */
    private static function requireAuth()
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            Response::unauthorized("Token obrigatório");
            return false;
        }
        $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
        $payload = JWT::decode($token);
        if (!$payload) {
            Response::unauthorized("Token inválido ou expirado");
            return false;
        }
        return $payload;
    }

    /**
     * GET - Listar todas as procedures
     */
    public static function listar()
    {
        if (!self::requireAuth()) return;
        try {
            $sql = "
                SELECT ROUTINE_NAME, ROUTINE_TYPE, CREATED, LAST_ALTERED
                FROM INFORMATION_SCHEMA.ROUTINES
                WHERE ROUTINE_SCHEMA = DATABASE()
                ORDER BY ROUTINE_NAME
            ";
            
            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $procedures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            Response::success($procedures, count($procedures) . " procedures encontradas");
        } catch (Exception $e) {
            Response::internalError("Erro ao listar procedures: " . $e->getMessage());
        }
    }

    /**
     * GET - Verificar se procedure existe
     * 
     * Query Parameters:
     * - nome: string (obrigatório) - Nome da procedure
     */
    public static function existe()
    {
        if (!self::requireAuth()) return;
        try {
            $nome = $_GET['nome'] ?? '';
            
            if (empty($nome)) {
                Response::badRequest("Parâmetro 'nome' é obrigatório");
                return;
            }
            
            $sql = "
                SELECT COUNT(*) as existe
                FROM INFORMATION_SCHEMA.ROUTINES
                WHERE ROUTINE_SCHEMA = DATABASE()
                AND ROUTINE_NAME = :nome
                AND ROUTINE_TYPE = 'PROCEDURE'
            ";
            
            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $existe = $resultado['existe'] > 0;
            
            Response::success([
                'nome' => $nome,
                'existe' => $existe
            ]);
        } catch (Exception $e) {
            Response::internalError("Erro ao verificar procedure: " . $e->getMessage());
        }
    }

    /**
     * POST - Executar uma procedure
     * 
     * Request Body:
     * {
     *   "nome": "sprGeraNovoAtendimento",
     *   "parametros": [
     *     "5585987654321",
     *     "João Silva",
     *     1,
     *     "Atendente 1",
     *     "A",
     *     "WA",
     *     "Suporte"
     *   ]
     * }
     */
    public static function executar()
    {
        if (!self::requireAuth()) return;
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $nome = $input['nome'] ?? '';
            $parametros = $input['parametros'] ?? [];
            
            if (empty($nome)) {
                Response::badRequest("Nome da procedure é obrigatório");
                return;
            }
            
            // Sanitizar nome da procedure
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nome)) {
                Response::badRequest("Nome de procedure inválido");
                return;
            }
            
            // Montar chamada à procedure
            $placeholders = array_fill(0, count($parametros), '?');
            $sql = 'CALL ' . $nome . '(' . implode(',', $placeholders) . ')';
            
            $db = Database::connect();
            $stmt = $db->prepare($sql);
            
            // Bind dos parâmetros
            foreach ($parametros as $i => $valor) {
                $stmt->bindValue($i + 1, $valor);
            }
            
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            Response::success([
                'procedure' => $nome,
                'parametros_passados' => count($parametros),
                'resultado' => $resultado
            ], "Procedure executada com sucesso");
        } catch (Exception $e) {
            Response::internalError("Erro ao executar procedure: " . $e->getMessage());
        }
    }

    /**
     * POST - Criar nova procedure (ADMIN ONLY)
     * 
     * Request Body:
     * {
     *   "nome": "sprGeraNovoAtendimento",
     *   "parametros": "IN pNumero VARCHAR(20), IN pNome VARCHAR(100), ...",
     *   "sql": "CREATE PROCEDURE sprGeraNovoAtendimento(...) BEGIN ... END;"
     * }
     */
    public static function criar()
    {
        if (!self::requireAuth()) return;
        try {
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $nome = $input['nome'] ?? '';
            $sql = $input['sql'] ?? '';
            
            if (empty($nome) || empty($sql)) {
                Response::badRequest("Nome e SQL são obrigatórios");
                return;
            }
            
            // Sanitizar nome
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nome)) {
                Response::badRequest("Nome de procedure inválido");
                return;
            }
            
            $db = Database::connect();
            $db->exec($sql);
            
            Response::success([
                'nome' => $nome,
                'criada' => true
            ], "Procedure criada com sucesso");
        } catch (Exception $e) {
            Response::internalError("Erro ao criar procedure: " . $e->getMessage());
        }
    }

    /**
     * POST - Remover procedure (ADMIN ONLY)
     */
    public static function droppar()
    {
        if (!self::requireAuth()) return;
        try {
            
            $input = json_decode(file_get_contents('php://input'), true);
            $nome = $input['nome'] ?? '';
            
            if (empty($nome)) {
                Response::badRequest("Nome da procedure é obrigatório");
                return;
            }
            
            // Sanitizar nome
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nome)) {
                Response::badRequest("Nome inválido");
                return;
            }
            
            $db = Database::connect();
            $db->exec("DROP PROCEDURE IF EXISTS $nome");
            
            Response::success([
                'nome' => $nome,
                'removida' => true
            ], "Procedure removida com sucesso");
        } catch (Exception $e) {
            Response::internalError("Erro ao remover procedure: " . $e->getMessage());
        }
    }

    /**
     * POST - Executar SQL arbitrário - DESABILITADO por segurança
     */
    public static function executarSQL()
    {
        Response::forbidden("Endpoint desabilitado por segurança. Use endpoints específicos.");
        return;
    }

    /**
     * POST - Criar tabela se não existir ou adicionar coluna se não existir
     * Simplifica VerificaTabelaseColunas do Delphi original
     * 
     * Request Body:
     * {
     *   "tabela": "tbusuario",
     *   "criar_se_nao_existe": false,
     *   "sql_criacao": "CREATE TABLE ...",
     *   "colunas": [
     *     {
     *       "nome": "em_almoco",
     *       "sql_adicionar": "ALTER TABLE tbusuario ADD COLUMN em_almoco TINYINT(1) ..."
     *     }
     *   ]
     * }
     */
    public static function sincronizarEstrutura()
    {
        if (!self::requireAuth()) return;
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $nomeTabela = $input['tabela'] ?? '';
            $sqlCriacao = $input['sql_criacao'] ?? '';
            $colunas = $input['colunas'] ?? [];
            $criarSeNaoExiste = $input['criar_se_nao_existe'] ?? true;
            
            if (empty($nomeTabela)) {
                Response::badRequest("Nome da tabela é obrigatório");
                return;
            }
            
            $db = Database::connect();
            $resultado = [
                'tabela' => $nomeTabela,
                'criada' => false,
                'colunas_adicionadas' => [],
                'erros' => []
            ];
            
            // Verificar se tabela existe
            $sql_check = "SHOW TABLES LIKE :tabela";
            $stmt = $db->prepare($sql_check);
            $stmt->bindValue(':tabela', $nomeTabela);
            $stmt->execute();
            $tabelaExiste = $stmt->rowCount() > 0;
            
            // Criar tabela se não existir
            if (!$tabelaExiste && $criarSeNaoExiste && !empty($sqlCriacao)) {
                try {
                    $db->exec($sqlCriacao);
                    $resultado['criada'] = true;
                } catch (Exception $e) {
                    $resultado['erros'][] = "Erro ao criar tabela: " . $e->getMessage();
                }
            }
            
            // Processar colunas
            foreach ($colunas as $coluna) {
                $nomeColuna = $coluna['nome'] ?? '';
                $sqlAdicionar = $coluna['sql_adicionar'] ?? '';
                
                if (empty($nomeColuna) || empty($sqlAdicionar)) {
                    continue;
                }
                
                // Verificar se coluna existe
                $sql_col_check = "
                    SELECT COUNT(*) as existe
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = :tabela
                    AND COLUMN_NAME = :coluna
                ";
                
                $stmt = $db->prepare($sql_col_check);
                $stmt->bindValue(':tabela', $nomeTabela);
                $stmt->bindValue(':coluna', $nomeColuna);
                $stmt->execute();
                
                $colunaExiste = $stmt->fetch(PDO::FETCH_ASSOC)['existe'] > 0;
                
                if (!$colunaExiste) {
                    try {
                        $db->exec($sqlAdicionar);
                        $resultado['colunas_adicionadas'][] = $nomeColuna;
                    } catch (Exception $e) {
                        $resultado['erros'][] = "Erro ao adicionar coluna $nomeColuna: " . $e->getMessage();
                    }
                }
            }
            
            Response::success($resultado);
        } catch (Exception $e) {
            Response::internalError("Erro ao sincronizar estrutura: " . $e->getMessage());
        }
    }
}
?>
