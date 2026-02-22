<?php
/**
 * AnexosController - Gerencia endpoints de anexos
 * Q19: Listar anexos pendentes de envio
 * Q20: Buscar anexo por PK
 * P16: Marcar anexo como enviado
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class AnexosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q19: Listar anexos pendentes de envio
     * GET /anexos/pendentes?canal=WhatsApp
     * 
     * Retorna lista de anexos com enviado=1 para serem processados
     * Joins com tbmsgatendimento para obter informações da mensagem
     */
    public function listarPendentes()
    {
        try {
            $canal = $_GET['canal'] ?? 'WhatsApp';

            $sql = "SELECT 
                        CONCAT(ta.id, ta.seq, ta.numero) AS pk,
                        ta.id,
                        ta.seq,
                        ta.numero,
                        ta.arquivo,
                        ta.nome_arquivo,
                        ta.enviado,
                        tm.msg,
                        tm.canal
                    FROM tbanexos ta
                    INNER JOIN tbmsgatendimento tm 
                        ON tm.id = ta.id 
                        AND tm.seq = ta.seq 
                        AND tm.numero = ta.numero
                    WHERE ta.enviado = 1 
                    AND tm.canal = ?
                    ORDER BY ta.id ASC, ta.seq ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$canal]);
            $anexos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::success([
                'canal' => $canal,
                'anexos' => $anexos,
                'total' => count($anexos)
            ], 'Anexos pendentes retornados');
        } catch (\PDOException $e) {
            Response::error('Erro ao listar anexos pendentes: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q20: Buscar anexo por PK (CONCAT(id, seq, numero))
     * GET /anexos/{pk}
     * 
     * Retorna detalhes completo de um anexo específico
     */
    public function buscarPorPK($pk = null)
    {
        try {
            if (!$pk) {
                $pk = $_GET['pk'] ?? '';
            }

            if (empty($pk)) {
                Response::error('PK obrigatório', 400);
                return;
            }

            // Parse PK format: CONCAT(id, seq, numero)
            // Exemplo: 1232msg_555 = id=1, seq=2, numero=msg_555
            // Precisamos extrair backwards: numero é sempre string com underscore
            
            // Estratégia: buscar primeiro para ver qual é o match
            $sql = "SELECT 
                        CONCAT(ta.id, ta.seq, ta.numero) AS pk,
                        ta.id,
                        ta.seq,
                        ta.numero,
                        ta.arquivo,
                        ta.nome_arquivo,
                        ta.enviado,
                        LENGTH(ta.arquivo) as tamanho_arquivo,
                        tm.msg,
                        tm.canal,
                        tm.data_msg
                    FROM tbanexos ta
                    INNER JOIN tbmsgatendimento tm 
                        ON tm.id = ta.id 
                        AND tm.seq = ta.seq 
                        AND tm.numero = ta.numero
                    WHERE CONCAT(ta.id, ta.seq, ta.numero) = ?
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$pk]);
            $anexo = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$anexo) {
                Response::error('Anexo não encontrado', 404);
                return;
            }

            Response::success($anexo, 'Anexo encontrado');
        } catch (\PDOException $e) {
            Response::error('Erro ao buscar anexo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P16: Marcar anexo como enviado (atualizar status)
     * PUT /anexos/{pk}/marcar-enviado
     * Body: { enviado: 0 } (para marcar como enviado após processamento)
     * 
     * Atualiza o status enviado do anexo
     */
    public function marcarEnviado($pk = null)
    {
        try {
            if (!$pk) {
                $data = json_decode(file_get_contents('php://input'), true);
                $pk = $data['pk'] ?? $_GET['pk'] ?? '';
            }

            if (empty($pk)) {
                Response::error('PK obrigatório', 400);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $enviado = isset($data['enviado']) ? (int)$data['enviado'] : 0;

            // Parse PK para extrair id, seq, numero
            // Formato esperado: CONCAT(id, seq, numero)
            // Exemplo: 1232msg_555 = id=1, seq=2, numero=msg_555
            
            // Primeiro, buscar o anexo para extrair os valores
            $sqlBusca = "SELECT ta.id, ta.seq, ta.numero
                        FROM tbanexos ta
                        WHERE CONCAT(ta.id, ta.seq, ta.numero) = ?
                        LIMIT 1";
            
            $stmtBusca = $this->db->prepare($sqlBusca);
            $stmtBusca->execute([$pk]);
            $resultado = $stmtBusca->fetch(\PDO::FETCH_ASSOC);

            if (!$resultado) {
                Response::error('Anexo não encontrado', 404);
                return;
            }

            $id = $resultado['id'];
            $seq = $resultado['seq'];
            $numero = $resultado['numero'];

            // Atualizar status
            $sql = "UPDATE tbanexos
                   SET enviado = ?
                   WHERE id = ? AND seq = ? AND numero = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $enviado,
                $id,
                $seq,
                $numero
            ]);

            Response::success([
                'pk' => $pk,
                'id' => $id,
                'seq' => $seq,
                'numero' => $numero,
                'enviado' => $enviado,
                'linhas_atualizadas' => $stmt->rowCount()
            ], 'Anexo marcado como ' . ($enviado ? 'pendente' : 'enviado'));
        } catch (\PDOException $e) {
            Response::error('Erro ao marcar anexo como enviado: ' . $e->getMessage(), 500);
        }
    }
}
?>
