<?php
/**
 * MensagensStatusController
 * Controla operações de sincronização de status de mensagens
 * 
 * Endpoints:
 * - GET /mensagens/pendentes-status - Lista mensagens pendentes (para o Delphi verificar via WPPConnect)
 * - POST /mensagens/status/atualizar - Atualiza status após WPPConnect verificar
 * - POST /mensagens/status/processar-mult - Processa múltiplas atualizações
 * - GET /mensagens/status/relatorio - Relatório de mensagens por status
 * 
 * Data: 20/11/2025
 */

class MensagensStatusController
{
    /**
     * GET - Lista mensagens pendentes de verificação de status
     * O Delphi usa isso para obter a lista e verifica status via WPPConnect
     * 
     * Query Parameters:
     * - canal: string (obrigatório) - Canal de atendimento (ex: 'whatsapp')
     * - horas_atras: int (opcional, default: 24) - Quantidade de horas para buscar
     * - minutos_futuros: int (opcional, default: 10) - Minutos para frente
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "id_msg": 1,
     *       "chatid": "5585987654321@c.us",
     *       "dt_msg": "2025-11-20 15:30:45",
     *       "id_atend": 123,
     *       "situacao": "N",
     *       "status_msg": 1,
     *       "canal": "whatsapp"
     *     }
     *   ],
     *   "count": 1
     * }
     */
    public static function listarPendentes()
    {
        try {
            // Validar parâmetros
            $canal = $_GET['canal'] ?? '';
            $horas_atras = (int)($_GET['horas_atras'] ?? 24);
            $minutos_futuros = (int)($_GET['minutos_futuros'] ?? 10);

            if (empty($canal)) {
                Response::badRequest("Parâmetro 'canal' é obrigatório");
                return;
            }

            // Calcular datas
            $data_ini = date('Y-m-d H:i:s', strtotime("-{$horas_atras} hours"));
            $data_fim = date('Y-m-d H:i:s', strtotime("+{$minutos_futuros} minutes"));

            // Query - Mesma lógica da procedure VerificaStatusMessage_Mult
            $sql = "
                SELECT 
                    id_msg,
                    chatid,
                    dt_msg,
                    id_atend,
                    situacao,
                    status_msg,
                    canal
                FROM tbmsgatendimento
                WHERE dt_msg BETWEEN :dt_msg_ini AND :dt_msg_fim
                  AND situacao = 'N'
                  AND chatid IS NOT NULL
                  AND status_msg <> 3
                  AND id_atend > 0
                  AND canal = :canal
                ORDER BY dt_msg DESC
            ";

            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':dt_msg_ini', $data_ini);
            $stmt->bindValue(':dt_msg_fim', $data_fim);
            $stmt->bindValue(':canal', $canal);
            $stmt->execute();

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($resultado, count($resultado) . " mensagens encontradas para verificação");
        } catch (Exception $e) {
            Response::internalError("Erro ao listar mensagens pendentes: " . $e->getMessage());
        }
    }

    /**
     * POST - Atualizar status de mensagem após WPPConnect verificar
     * O Delphi chama WPPConnect.getMessageById(), recebe o status, e atualiza aqui
     * 
     * Request Body:
     * {
     *   "id_msg": 1,
     *   "chatid": "5585987654321@c.us",
     *   "novo_status": 2,
     *   "timestamp_verificacao": "2025-11-20 15:35:00"
     * }
     * 
     * Status válidos:
     * - 0: Pendente
     * - 1: Enviada
     * - 2: Entregue
     * - 3: Lida
     * - 4: Erro
     * 
     * Response:
     * {
     *   "success": true,
     *   "message": "Status atualizado com sucesso",
     *   "data": {
     *     "id_msg": 1,
     *     "status_anterior": 1,
     *     "status_novo": 2,
     *     "atualizado_em": "2025-11-20 15:36:00"
     *   }
     * }
     */
    public static function atualizarStatus()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $id_msg = (int)($input['id_msg'] ?? 0);
            $chatid = $input['chatid'] ?? '';
            $novo_status = (int)($input['novo_status'] ?? -1);

            if ($id_msg == 0 || empty($chatid) || $novo_status < 0 || $novo_status > 4) {
                Response::badRequest("Parâmetros inválidos. Verifique: id_msg, chatid, novo_status (0-4)");
                return;
            }

            // Obter status anterior
            $sql_select = "
                SELECT status_msg
                FROM tbmsgatendimento
                WHERE id_msg = :id_msg AND chatid = :chatid
                LIMIT 1
            ";

            $db = Database::connect();
            $stmt = $db->prepare($sql_select);
            $stmt->bindValue(':id_msg', $id_msg);
            $stmt->bindValue(':chatid', $chatid);
            $stmt->execute();

            $mensagem = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$mensagem) {
                Response::notFound("Mensagem não encontrada");
                return;
            }

            $status_anterior = $mensagem['status_msg'];

            // Atualizar status
            $sql_update = "
                UPDATE tbmsgatendimento
                SET status_msg = :novo_status,
                    dt_atualizacao = NOW()
                WHERE id_msg = :id_msg AND chatid = :chatid
            ";

            $stmt = $db->prepare($sql_update);
            $stmt->bindValue(':novo_status', $novo_status);
            $stmt->bindValue(':id_msg', $id_msg);
            $stmt->bindValue(':chatid', $chatid);
            $stmt->execute();

            $resposta = [
                'id_msg' => $id_msg,
                'status_anterior' => $status_anterior,
                'status_novo' => $novo_status,
                'atualizado_em' => date('Y-m-d H:i:s')
            ];

            Response::success($resposta, "Status atualizado com sucesso");
        } catch (Exception $e) {
            Response::internalError("Erro ao atualizar status: " . $e->getMessage());
        }
    }

    /**
     * POST - Processar múltiplas atualizações de status em lote
     * Útil quando o Delphi verifica várias mensagens e precisa atualizar múltiplas de uma vez
     * 
     * Request Body:
     * {
     *   "atualizacoes": [
     *     {"id_msg": 1, "chatid": "5585987654321@c.us", "novo_status": 2},
     *     {"id_msg": 2, "chatid": "5585987654321@c.us", "novo_status": 3}
     *   ]
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "processadas": 2,
     *     "atualizadas": 2,
     *     "erros": 0,
     *     "detalhes": [
     *       {"id_msg": 1, "status_novo": 2, "sucesso": true},
     *       {"id_msg": 2, "status_novo": 3, "sucesso": true}
     *     ]
     *   }
     * }
     */
    public static function processarMultiplasAtualizacoes()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $atualizacoes = $input['atualizacoes'] ?? [];

            if (empty($atualizacoes)) {
                Response::badRequest("Campo 'atualizacoes' é obrigatório e não pode estar vazio");
                return;
            }

            $db = Database::connect();
            $resultado = [
                'processadas' => count($atualizacoes),
                'atualizadas' => 0,
                'erros' => 0,
                'detalhes' => []
            ];

            // Processar cada atualização
            foreach ($atualizacoes as $atualizacao) {
                try {
                    $id_msg = (int)($atualizacao['id_msg'] ?? 0);
                    $chatid = $atualizacao['chatid'] ?? '';
                    $novo_status = (int)($atualizacao['novo_status'] ?? -1);

                    if ($id_msg == 0 || empty($chatid) || $novo_status < 0 || $novo_status > 4) {
                        throw new Exception("Dados inválidos");
                    }

                    $sql_update = "
                        UPDATE tbmsgatendimento
                        SET status_msg = :novo_status,
                            dt_atualizacao = NOW()
                        WHERE id_msg = :id_msg AND chatid = :chatid
                    ";

                    $stmt = $db->prepare($sql_update);
                    $stmt->bindValue(':novo_status', $novo_status);
                    $stmt->bindValue(':id_msg', $id_msg);
                    $stmt->bindValue(':chatid', $chatid);
                    $stmt->execute();

                    $resultado['atualizadas']++;
                    $resultado['detalhes'][] = [
                        'id_msg' => $id_msg,
                        'status_novo' => $novo_status,
                        'sucesso' => true
                    ];
                } catch (Exception $e) {
                    $resultado['erros']++;
                    $resultado['detalhes'][] = [
                        'id_msg' => $atualizacao['id_msg'] ?? 0,
                        'erro' => $e->getMessage(),
                        'sucesso' => false
                    ];
                }
            }

            Response::success($resultado);
        } catch (Exception $e) {
            Response::internalError("Erro ao processar múltiplas atualizações: " . $e->getMessage());
        }
    }

    /**
     * GET - Relatório de mensagens por status
     * 
     * Query Parameters:
     * - canal: string (obrigatório)
     * - data_ini: string (opcional, formato: YYYY-MM-DD)
     * - data_fim: string (opcional, formato: YYYY-MM-DD)
     * 
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "total": 100,
     *     "por_status": {
     *       "0": 10,
     *       "1": 30,
     *       "2": 50,
     *       "3": 10
     *     },
     *     "pendentes": 10,
     *     "entregues": 90
     *   }
     * }
     */
    public static function relatorioStatus()
    {
        try {
            $canal = $_GET['canal'] ?? '';
            $data_ini = $_GET['data_ini'] ?? date('Y-m-d', strtotime('-7 days'));
            $data_fim = $_GET['data_fim'] ?? date('Y-m-d');

            if (empty($canal)) {
                Response::badRequest("Parâmetro 'canal' é obrigatório");
                return;
            }

            $sql = "
                SELECT 
                    status_msg,
                    COUNT(*) as quantidade
                FROM tbmsgatendimento
                WHERE canal = :canal
                  AND DATE(dt_msg) BETWEEN :data_ini AND :data_fim
                GROUP BY status_msg
                ORDER BY status_msg
            ";

            $db = Database::connect();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':canal', $canal);
            $stmt->bindValue(':data_ini', $data_ini);
            $stmt->bindValue(':data_fim', $data_fim);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $por_status = [];
            $total = 0;

            foreach ($resultados as $row) {
                $por_status[$row['status_msg']] = $row['quantidade'];
                $total += $row['quantidade'];
            }

            $resposta = [
                'total' => $total,
                'por_status' => $por_status,
                'pendentes' => $por_status[0] ?? 0,
                'enviadas' => $por_status[1] ?? 0,
                'entregues' => $por_status[2] ?? 0,
                'lidas' => $por_status[3] ?? 0,
                'periodo' => [
                    'inicio' => $data_ini,
                    'fim' => $data_fim
                ]
            ];

            Response::success($resposta);
        } catch (Exception $e) {
            Response::internalError("Erro ao gerar relatório: " . $e->getMessage());
        }
    }
}
?>
