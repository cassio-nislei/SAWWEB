<?php
/**
 * MensagensController - Gerencia endpoints de mensagens
 * Q6: Verificar se mensagem é duplicada
 * Q8: Status de múltiplas mensagens
 * Q13: Mensagens pendentes de envio
 * Q14: Comparar duplicação
 * Q17: Próxima sequência
 * P4: Marcar como enviada
 * P5: Marcar como excluída
 * P6: Marcar reação
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class MensagensController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q6: Verificar se mensagem é duplicada
     * GET /mensagens/verificar-duplicada?chatid=msg_123456
     */
    public function verificarDuplicada()
    {
        try {
            $chatid = $_GET['chatid'] ?? '';

            if (empty($chatid)) {
                Response::error('chatid obrigatório', 400);
                return;
            }

            $sql = "SELECT id FROM tbmsgatendimento WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$chatid]);
            $existe = $stmt->fetch(\PDO::FETCH_ASSOC);

            Response::success([
                'existe' => !!$existe,
                'chatid' => $chatid
            ]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q8: Status de múltiplas mensagens
     * GET /mensagens/status-multiplas?canal=WhatsApp
     */
    public function statusMultiplas()
    {
        try {
            $canal = $_GET['canal'] ?? '';

            $sql = "SELECT * FROM tbmsgatendimento
                   WHERE DATE(data_msg) = CURDATE()
                   AND situacao = 'N'
                   ORDER BY data_msg DESC
                   LIMIT 100";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $mensagens = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::success(['mensagens' => $mensagens]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q13: Mensagens pendentes de envio
     * GET /mensagens/pendentes-envio?canal=WhatsApp
     */
    public function pendentesEnvio()
    {
        try {
            $canal = $_GET['canal'] ?? '';

            $sql = "SELECT * FROM tbmsgatendimento
                   WHERE situacao = 'E'
                   ORDER BY data_msg ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $mensagens = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::success(['mensagens' => $mensagens]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q17: Próxima sequência
     * GET /mensagens/proxima-sequencia?id_atendimento=456&numero=11999999999
     */
    public function proximaSequencia()
    {
        try {
            $id = (int)($_GET['id_atendimento'] ?? 0);
            $numero = $_GET['numero'] ?? '';

            $sql = "SELECT COALESCE(MAX(id), 0) + 1 as seq FROM tbmsgatendimento
                   WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            Response::success($resultado);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P5: Marcar mensagem como excluída (spAtualizaExcluida)
     * PUT /mensagens/marcar-excluida
     * Body: { chatid }
     */
    public function marcarExcluida()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || empty($data['chatid'])) {
                Response::error('chatid obrigatório', 400);
                return;
            }

            $chatid = $data['chatid'];

            $sql = "CALL spAtualizaExcluida(:pChatId)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pChatId', $chatid, \PDO::PARAM_STR);
            $stmt->execute();

            Response::success([
                'chatid' => $chatid,
                'executado' => true
            ], 'Mensagem marcada como excluída com sucesso');
        } catch (\PDOException $e) {
            Response::error('Erro ao marcar mensagem como excluída: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P6: Marcar reação
     * PUT /mensagens/marcar-reacao
     * Body: { chatid, reacao }
     */
    public function marcarReacao()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || empty($data['chatid'])) {
                Response::error('chatid obrigatório', 400);
                return;
            }

            $sql = "UPDATE tbmsgatendimento SET reacao = ? WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['reacao'] ?? '',
                $data['chatid']
            ]);

            Response::success(null, 'Reação marcada');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P4: Marcar como enviada
     * PUT /mensagens/marcar-enviada
     * Body: { id_agendamento, enviado, tempo_envio }
     */
    public function marcarEnviada()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $sql = "UPDATE tbmsgatendimento SET situacao = 'S', data_msg = NOW()
                   WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['id_agendamento'] ?? 0]);

            Response::success(null, 'Mensagem marcada como enviada');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q14: Comparar duplicação
     * POST /mensagens/comparar-duplicacao
     * Body: { id, seq_anterior, numero, msg_atual }
     */
    public function compararDuplicacao()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $sql = "SELECT mensagem FROM tbmsgatendimento WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['id'] ?? 0]);
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            $msgAnterior = $resultado['mensagem'] ?? '';
            $ehDuplicada = strtoupper(trim($data['msg_atual'] ?? '')) ===
                          strtoupper(trim($msgAnterior));

            Response::success([
                'eh_duplicada' => $ehDuplicada,
                'msg_anterior' => $msgAnterior
            ]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P7: Atualizar envio de mensagem (spAtualizarEnvioMensagem)
     * PUT /mensagens/atualizar-envio
     * Body: { id_agendamento, enviado, tempo_envio }
     * 
     * Atualiza o status de envio de uma mensagem agendada
     * @param id_agendamento: ID do agendamento (integer)
     * @param enviado: Flag de envio (1=sim, 0=não) (integer)
     * @param tempo_envio: Tempo de envio em ms (integer)
     */
    public function atualizarEnvio()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar parâmetros obrigatórios
            if (!isset($data['id_agendamento']) || !isset($data['enviado']) || !isset($data['tempo_envio'])) {
                Response::error('Parâmetros obrigatórios: id_agendamento, enviado, tempo_envio', 400);
                return;
            }

            $idAgendamento = (int)$data['id_agendamento'];
            $enviado = (int)$data['enviado'];
            $tempoEnvio = (int)$data['tempo_envio'];

            // Executar procedure
            $sql = "CALL spAtualizarEnvioMensagem(:pIdAgendamento, :pEnviado, :ptempo_envio)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pIdAgendamento', $idAgendamento, \PDO::PARAM_INT);
            $stmt->bindParam(':pEnviado', $enviado, \PDO::PARAM_INT);
            $stmt->bindParam(':ptempo_envio', $tempoEnvio, \PDO::PARAM_INT);
            $stmt->execute();

            Response::success([
                'id_agendamento' => $idAgendamento,
                'enviado' => $enviado,
                'tempo_envio' => $tempoEnvio,
                'executado' => true
            ], 'Envio de mensagem atualizado com sucesso');
        } catch (\PDOException $e) {
            Response::error('Erro ao atualizar envio: ' . $e->getMessage(), 500);
        }
    }
}
?>
