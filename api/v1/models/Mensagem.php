<?php
/**
 * SAW API v1 - Model Mensagem
 */

class Mensagem
{
    /**
     * Cria nova mensagem
     */
    public static function create($idAtendimento, $numero, $mensagem, $resposta = '', $idAtende = 0, $nomeChat = '', $situacao = 'E', $canal = 1, $chatIdResposta = null)
    {
        // Gera sequência
        $seqResult = Database::query(
            "SELECT COALESCE(MAX(seq), 0) + 1 as newSeq FROM tbmsgatendimento WHERE id = ? AND canal = ? AND numero = ?",
            [$idAtendimento, $canal, $numero]
        );

        $newSeq = $seqResult[0]['newSeq'] ?? 1;
        
        // Revert chatid to uniqid
        $chatId = uniqid('API_');

        $db = Database::getInstance();
        
        $numero = $db->real_escape_string($numero);
        $mensagem = $db->real_escape_string($mensagem);
        $resposta = $db->real_escape_string($resposta);
        $nomeChat = $db->real_escape_string($nomeChat);
        $situacao = $db->real_escape_string($situacao);
        $chatId = $db->real_escape_string($chatId);
        $chatIdResposta = !empty($chatIdResposta) ? "'" . $db->real_escape_string($chatIdResposta) . "'" : "NULL";

        $sql = "INSERT INTO tbmsgatendimento (id, seq, numero, msg, resp_msg, nome_chat, situacao, dt_msg, hr_msg, id_atend, canal, chatid, chatid_resposta) 
                VALUES ($idAtendimento, $newSeq, '$numero', '$mensagem', '$resposta', '$nomeChat', '$situacao', NOW(), CURTIME(), " . (int)$idAtende . ", " . (int)$canal . ", '$chatId', $chatIdResposta)";

        $result = Database::execute($sql);

        if ($result === false) {
            error_log("Erro ao criar mensagem: " . Database::getLastError());
            return false;
        }

        return self::getBySeq($idAtendimento, $numero, $newSeq);
    }

    /**
     * Obtém mensagem por sequência
     */
    public static function getBySeq($id, $numero, $seq)
    {
        $result = Database::query(
            "SELECT * FROM tbmsgatendimento WHERE id = ? AND numero = ? AND seq = ?",
            [$id, $numero, $seq]
        );

        return $result[0] ?? null;
    }

    /**
     * Lista mensagens de um atendimento
     */
    public static function listByAtendimento($id, $numero, $tipo = 'all')
    {
        $sql = "SELECT tma.*, ta.tipo_arquivo, ta.nome_original, ta.arquivo 
                FROM tbmsgatendimento tma 
                LEFT JOIN tbanexos ta ON tma.id = ta.id AND tma.seq = ta.seq AND tma.numero = ta.numero 
                WHERE tma.numero = ? AND tma.id = ?";

        $params = [$numero, $id];

        if ($tipo === 'att') {
            // Histórico do atendimento
        } elseif ($tipo === 'all') {
            // Histórico completo do cliente
            $sql = str_replace('AND tma.id = ?', '', $sql);
            array_pop($params);
        }

        $sql .= " ORDER BY tma.id, tma.seq";

        return Database::query($sql, $params);
    }

    /**
     * Lista mensagens pendentes (situação 'E')
     */
    public static function listPending($canal = null)
    {
        $sql = "SELECT tma.*, tc.nome_contato, tc.numero_contato 
                FROM tbmsgatendimento tma 
                LEFT JOIN tbanexacontato tc ON tma.id = tc.id AND tma.seq = tc.seq AND tma.numero = tc.numero 
                WHERE tma.situacao = 'E'";

        $params = [];

        if ($canal !== null) {
            $sql .= " AND tma.canal = ?";
            $params[] = $canal;
        }

        $sql .= " ORDER BY tma.numero, tma.seq";

        return Database::query($sql, $params);
    }

    /**
     * Atualiza situação da mensagem
     */
    public static function updateSituacao($id, $numero, $seq, $situacao)
    {
        $sql = "UPDATE tbmsgatendimento SET situacao = ? WHERE id = ? AND numero = ? AND seq = ?";
        return Database::execute($sql, [$situacao, $id, $numero, $seq]);
    }

    /**
     * Marca mensagens como visualizadas
     */
    public static function markAsViewed($id, $numero)
    {
        $sql = "UPDATE tbmsgatendimento SET visualizada = true WHERE id = ? AND numero = ?";
        return Database::execute($sql, [$id, $numero]);
    }

    /**
     * Adiciona reação a mensagem
     */
    public static function addReaction($chatId, $reacao)
    {
        $sql = "UPDATE tbmsgatendimento SET reagir = 1, reacao = ? WHERE chatid = ?";
        return Database::execute($sql, [$reacao, $chatId]);
    }

    /**
     * Deleta mensagem
     */
    public static function delete($id, $numero, $seq)
    {
        $sql = "DELETE FROM tbmsgatendimento WHERE id = ? AND numero = ? AND seq = ?";
        return Database::execute($sql, [$id, $numero, $seq]);
    }

    /**
     * Verifica se é mensagem duplicada
     */
    public static function isDuplicate($id, $numero, $seq, $mensagem)
    {
        $result = Database::query(
            "SELECT msg FROM tbmsgatendimento WHERE id = ? AND seq = ? - 1 AND numero = ?",
            [$id, $seq, $numero]
        );

        return count($result) > 0 && $result[0]['msg'] === $mensagem;
    }
}
?>
