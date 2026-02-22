<?php
/**
 * BancoDadosController
 * Controla operações de verificação e estrutura do banco de dados
 * 
 * Endpoints:
 * - GET /banco-dados/tabelas - Lista todas as tabelas
 * - GET /banco-dados/tabela/existe - Verifica se tabela existe
 * - GET /banco-dados/campo/existe - Verifica se campo existe em tabela
 * - GET /banco-dados/estrutura-tabela - Obtém estrutura completa da tabela
 * - POST /banco-dados/criar-tabela - Cria nova tabela (admin only)
 * - POST /banco-dados/adicionar-campo - Adiciona campo a tabela (admin only)
 * 
 * Data: 21/11/2025
 */

class BancoDadosController
{
    /**
     * GET - Lista todas as tabelas do banco de dados
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     "tbmsgatendimento",
     *     "tbusuarios",
     *     "tbcontatos",
     *     ...
     *   ],
     *   "count": 15
     * }
     */
    public static function listarTabelas()
    {
        try {
            $sql = "SHOW TABLES";
            
            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->execute();
            
            $resultados = $stmt->fetchAll(PDO::FETCH_NUM);
            $tabelas = [];
            
            foreach ($resultados as $row) {
                $tabelas[] = $row[0];
            }
            
            Response::success($tabelas, count($tabelas) . " tabelas encontradas");
        } catch (Exception $e) {
            Response::internalError("Erro ao listar tabelas: " . $e->getMessage());
        }
    }

    /**
     * GET - Verifica se uma tabela existe no banco de dados
     * Equivalente a: TabelaExistenoMYSQL(NomeTabela, BancoDados)
     * 
     * Query Parameters:
     * - tabela: string (obrigatório) - Nome da tabela
     * 
     * Response Sucesso:
     * {
     *   "success": true,
     *   "data": {
     *     "tabela": "tbmsgatendimento",
     *     "existe": true,
     *     "verificado_em": "2025-11-21 10:30:00"
     *   }
     * }
     * 
     * Response Não Existe:
     * {
     *   "success": true,
     *   "data": {
     *     "tabela": "tabela_inexistente",
     *     "existe": false,
     *     "verificado_em": "2025-11-21 10:30:00"
     *   }
     * }
     */
    public static function tabelaExiste()
    {
        try {
            $nomeTabela = $_GET['tabela'] ?? '';
            
            if (empty($nomeTabela)) {
                Response::badRequest("Parâmetro 'tabela' é obrigatório");
                return;
            }
            
            // Sanitizar nome da tabela (apenas alphanumério, underscore)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nomeTabela)) {
                Response::badRequest("Nome de tabela inválido");
                return;
            }
            
            $sql = "SHOW TABLES LIKE :tabela";
            
            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':tabela', $nomeTabela);
            $stmt->execute();
            
            $existe = $stmt->rowCount() > 0;
            
            $resposta = [
                'tabela' => $nomeTabela,
                'existe' => $existe,
                'verificado_em' => date('Y-m-d H:i:s')
            ];
            
            Response::success($resposta);
        } catch (Exception $e) {
            Response::internalError("Erro ao verificar tabela: " . $e->getMessage());
        }
    }

    /**
     * GET - Verifica se um campo existe em uma tabela
     * Equivalente a: CampoExiste(NomeTabela, NomeCampo, Conexao)
     * 
     * Query Parameters:
     * - tabela: string (obrigatório) - Nome da tabela
     * - campo: string (obrigatório) - Nome do campo
     * 
     * Response Sucesso:
     * {
     *   "success": true,
     *   "data": {
     *     "tabela": "tbmsgatendimento",
     *     "campo": "id_msg",
     *     "existe": true,
     *     "tipo": "int",
     *     "permite_null": false,
     *     "padrao": null,
     *     "verificado_em": "2025-11-21 10:30:00"
     *   }
     * }
     * 
     * Response Não Existe:
     * {
     *   "success": true,
     *   "data": {
     *     "tabela": "tbmsgatendimento",
     *     "campo": "campo_inexistente",
     *     "existe": false,
     *     "verificado_em": "2025-11-21 10:30:00"
     *   }
     * }
     */
    public static function campoExiste()
    {
        try {
            $nomeTabela = $_GET['tabela'] ?? '';
            $nomeCampo = $_GET['campo'] ?? '';
            
            if (empty($nomeTabela) || empty($nomeCampo)) {
                Response::badRequest("Parâmetros 'tabela' e 'campo' são obrigatórios");
                return;
            }
            
            // Sanitizar nomes
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nomeTabela)) {
                Response::badRequest("Nome de tabela inválido");
                return;
            }
            
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nomeCampo)) {
                Response::badRequest("Nome de campo inválido");
                return;
            }
            
            $sql = "
                SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = :tabela 
                  AND COLUMN_NAME = :campo
                LIMIT 1
            ";
            
            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':tabela', $nomeTabela);
            $stmt->bindValue(':campo', $nomeCampo);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $existe = $resultado !== false;
            
            $resposta = [
                'tabela' => $nomeTabela,
                'campo' => $nomeCampo,
                'existe' => $existe,
                'verificado_em' => date('Y-m-d H:i:s')
            ];
            
            if ($existe) {
                $resposta['tipo'] = $resultado['COLUMN_TYPE'];
                $resposta['permite_null'] = $resultado['IS_NULLABLE'] === 'YES';
                $resposta['padrao'] = $resultado['COLUMN_DEFAULT'];
                $resposta['chave'] = $resultado['COLUMN_KEY'] ?: 'N';
            }
            
            Response::success($resposta);
        } catch (Exception $e) {
            Response::internalError("Erro ao verificar campo: " . $e->getMessage());
        }
    }

    /**
     * GET - Obtém estrutura completa de uma tabela
     * 
     * Query Parameters:
     * - tabela: string (obrigatório) - Nome da tabela
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "tabela": "tbmsgatendimento",
     *     "total_campos": 15,
     *     "campos": [
     *       {
     *         "nome": "id_msg",
     *         "tipo": "int",
     *         "permite_null": false,
     *         "padrao": null,
     *         "chave": "PRI"
     *       },
     *       {
     *         "nome": "chatid",
     *         "tipo": "varchar(255)",
     *         "permite_null": false,
     *         "padrao": null,
     *         "chave": ""
     *       },
     *       ...
     *     ]
     *   }
     * }
     */
    public static function estruturaTabela()
    {
        try {
            $nomeTabela = $_GET['tabela'] ?? '';
            
            if (empty($nomeTabela)) {
                Response::badRequest("Parâmetro 'tabela' é obrigatório");
                return;
            }
            
            // Sanitizar nome da tabela
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nomeTabela)) {
                Response::badRequest("Nome de tabela inválido");
                return;
            }
            
            // Verificar se tabela existe
            $sql_check = "SHOW TABLES LIKE :tabela";
            $db = Database::connect();
            $stmt_check = $db->prepare($sql_check);
            $stmt_check->bindValue(':tabela', $nomeTabela);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() === 0) {
                Response::notFound("Tabela '$nomeTabela' não encontrada");
                return;
            }
            
            // Obter estrutura
            $sql = "
                SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = :tabela
                ORDER BY ORDINAL_POSITION
            ";
            
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':tabela', $nomeTabela);
            $stmt->execute();
            
            $campos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $campos[] = [
                    'nome' => $row['COLUMN_NAME'],
                    'tipo' => $row['COLUMN_TYPE'],
                    'permite_null' => $row['IS_NULLABLE'] === 'YES',
                    'padrao' => $row['COLUMN_DEFAULT'],
                    'chave' => $row['COLUMN_KEY'] ?: ''
                ];
            }
            
            $resposta = [
                'tabela' => $nomeTabela,
                'total_campos' => count($campos),
                'campos' => $campos
            ];
            
            Response::success($resposta);
        } catch (Exception $e) {
            Response::internalError("Erro ao obter estrutura: " . $e->getMessage());
        }
    }

    /**
     * POST - Criar nova tabela (ADMIN ONLY)
     * 
     * Request Body:
     * {
     *   "nome": "tb_exemplo",
     *   "campos": [
     *     {
     *       "nome": "id",
     *       "tipo": "INT",
     *       "chave_primaria": true,
     *       "auto_incremento": true
     *     },
     *     {
     *       "nome": "titulo",
     *       "tipo": "VARCHAR(255)",
     *       "permite_null": false
     *     },
     *     {
     *       "nome": "data_criacao",
     *       "tipo": "DATETIME",
     *       "padrao": "CURRENT_TIMESTAMP"
     *     }
     *   ]
     * }
     */
    public static function criarTabela()
    {
        try {
            // Verificar permissão de admin
            if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                Response::unauthorized("Token obrigatório");
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $nome = $input['nome'] ?? '';
            $campos = $input['campos'] ?? [];
            
            if (empty($nome) || empty($campos)) {
                Response::badRequest("Nome e campos são obrigatórios");
                return;
            }
            
            // Sanitizar nome da tabela
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nome)) {
                Response::badRequest("Nome de tabela inválido");
                return;
            }
            
            // Montar SQL de criação
            $sql = "CREATE TABLE `$nome` (\n";
            $partes = [];
            
            foreach ($campos as $campo) {
                $sql_campo = "`" . $campo['nome'] . "` " . $campo['tipo'];
                
                if (!($campo['permite_null'] ?? true)) {
                    $sql_campo .= " NOT NULL";
                }
                
                if (isset($campo['padrao'])) {
                    $sql_campo .= " DEFAULT " . $campo['padrao'];
                }
                
                if ($campo['auto_incremento'] ?? false) {
                    $sql_campo .= " AUTO_INCREMENT";
                }
                
                $partes[] = $sql_campo;
            }
            
            // Adicionar chave primária
            $chaves_primarias = array_filter(array_map(function($c) {
                return ($c['chave_primaria'] ?? false) ? $c['nome'] : null;
            }, $campos));
            
            if (!empty($chaves_primarias)) {
                $partes[] = "PRIMARY KEY (`" . implode("`, `", $chaves_primarias) . "`)";
            }
            
            $sql .= implode(",\n", $partes) . "\n)";
            
            $db = Database::connect();
            $db->exec($sql);
            
            Response::success([
                'nome' => $nome,
                'criada' => true,
                'campos' => count($campos)
            ], "Tabela '$nome' criada com sucesso");
        } catch (Exception $e) {
            Response::internalError("Erro ao criar tabela: " . $e->getMessage());
        }
    }

    /**
     * POST - Adicionar campo a tabela (ADMIN ONLY)
     * 
     * Request Body:
     * {
     *   "tabela": "tb_exemplo",
     *   "nome": "novo_campo",
     *   "tipo": "VARCHAR(255)",
     *   "permite_null": false,
     *   "padrao": null,
     *   "apos": "campo_anterior"
     * }
     */
    public static function adicionarCampo()
    {
        try {
            // Verificar permissão de admin
            if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                Response::unauthorized("Token obrigatório");
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $tabela = $input['tabela'] ?? '';
            $nome = $input['nome'] ?? '';
            $tipo = $input['tipo'] ?? '';
            $apos = $input['apos'] ?? '';
            
            if (empty($tabela) || empty($nome) || empty($tipo)) {
                Response::badRequest("Tabela, nome e tipo são obrigatórios");
                return;
            }
            
            // Sanitizar nomes
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $tabela) || 
                !preg_match('/^[a-zA-Z0-9_]+$/', $nome)) {
                Response::badRequest("Nomes inválidos");
                return;
            }
            
            // Verificar se tabela existe
            $sql_check = "SHOW TABLES LIKE :tabela";
            $db = Database::connect();
            $stmt_check = $db->prepare($sql_check);
            $stmt_check->bindValue(':tabela', $tabela);
            $stmt_check->execute();
            
            if ($stmt_check->rowCount() === 0) {
                Response::notFound("Tabela não encontrada");
                return;
            }
            
            // Montar SQL de adição
            $sql = "ALTER TABLE `$tabela` ADD COLUMN `$nome` $tipo";
            
            if (!($input['permite_null'] ?? true)) {
                $sql .= " NOT NULL";
            }
            
            if (isset($input['padrao'])) {
                $sql .= " DEFAULT " . $input['padrao'];
            }
            
            if (!empty($apos)) {
                $sql .= " AFTER `$apos`";
            }
            
            $db->exec($sql);
            
            Response::success([
                'tabela' => $tabela,
                'campo' => $nome,
                'tipo' => $tipo,
                'adicionado' => true
            ], "Campo '$nome' adicionado com sucesso");
        } catch (Exception $e) {
            Response::internalError("Erro ao adicionar campo: " . $e->getMessage());
        }
    }

    /**
     * GET - Obter informações sobre uma coluna específica
     * 
     * Query Parameters:
     * - tabela: string (obrigatório)
     * - campo: string (obrigatório)
     */
    public static function infoCampo()
    {
        try {
            $tabela = $_GET['tabela'] ?? '';
            $campo = $_GET['campo'] ?? '';
            
            if (empty($tabela) || empty($campo)) {
                Response::badRequest("Parâmetros 'tabela' e 'campo' são obrigatórios");
                return;
            }
            
            $sql = "
                SELECT *
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = :tabela 
                  AND COLUMN_NAME = :campo
                LIMIT 1
            ";
            
            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':tabela', $tabela);
            $stmt->bindValue(':campo', $campo);
            $stmt->execute();
            
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$info) {
                Response::notFound("Campo não encontrado");
                return;
            }
            
            $resposta = [
                'tabela' => $info['TABLE_NAME'],
                'campo' => $info['COLUMN_NAME'],
                'tipo' => $info['COLUMN_TYPE'],
                'permite_null' => $info['IS_NULLABLE'] === 'YES',
                'padrao' => $info['COLUMN_DEFAULT'],
                'chave' => $info['COLUMN_KEY'] ?: 'N',
                'extra' => $info['EXTRA'] ?: '',
                'posicao' => $info['ORDINAL_POSITION']
            ];
            
            Response::success($resposta);
        } catch (Exception $e) {
            Response::internalError("Erro ao obter informações: " . $e->getMessage());
        }
    }
}
?>
