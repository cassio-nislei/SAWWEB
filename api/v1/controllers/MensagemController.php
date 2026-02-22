<?php
/**
 * SAW API v1 - Controller Mensagens
 */

class MensagemController
{
    /**
     * GET /api/v1/atendimentos/{id}/mensagens
     * Lista mensagens de um atendimento
     */
    public static function list($id)
    {
        $query = Router::getQueryParams();
        $numero = $query['numero'] ?? null;
        $tipo = $query['tipo'] ?? 'current'; // current, all, att

        if (!$numero) {
            Response::validationError(['numero' => "Parâmetro obrigatório"]);
        }

        $mensagens = Mensagem::listByAtendimento($id, $numero, $tipo);

        Response::success($mensagens, "Mensagens listadas com sucesso");
    }

    /**
     * POST /api/v1/atendimentos/{id}/mensagens
     * Cria nova mensagem
     */
    public static function create($id)
    {
        $body = Router::getJsonBody();
        $query = Router::getQueryParams();

        // Validação
        $required = ['numero', 'msg'];
        foreach ($required as $field) {
            if (!isset($body[$field])) {
                Response::validationError([$field => "Campo obrigatório"]);
            }
        }

        $mensagem = Mensagem::create(
            $id,
            $body['numero'],
            $body['msg'],
            $body['resp_msg'] ?? '',
            $body['id_atend'] ?? 0,
            $body['nome_chat'] ?? '',
            $body['situacao'] ?? 'E',
            $body['canal'] ?? 1,
            $body['chatid_resposta'] ?? null
        );

        if (!$mensagem) {
            Response::error("Erro ao criar mensagem", 500);
        }

        Response::success($mensagem, "Mensagem criada com sucesso", 201);
    }

    /**
     * GET /api/v1/atendimentos/{id}/mensagens/pendentes
     * Lista mensagens pendentes
     */
    public static function listPending($id)
    {
        $query = Router::getQueryParams();
        $canal = $query['canal'] ?? null;

        $mensagens = Mensagem::listPending($canal);

        Response::success($mensagens, "Mensagens pendentes listadas com sucesso");
    }

    /**
     * PUT /api/v1/mensagens/{id}/situacao
     * Atualiza situação da mensagem
     */
    public static function updateSituacao($chatId)
    {
        $body = Router::getJsonBody();

        if (!isset($body['situacao'])) {
            Response::validationError(['situacao' => "Campo obrigatório"]);
        }

        // Obtém a mensagem pelo chatid
        $query = "SELECT * FROM tbmsgatendimento WHERE chatid = ?";
        $result = Database::query($query, [$chatId]);

        if (empty($result)) {
            Response::notFound("Mensagem");
        }

        $msg = $result[0];

        $updated = Mensagem::updateSituacao(
            $msg['id'],
            $msg['numero'],
            $msg['seq'],
            $body['situacao']
        );

        if ($updated === false) {
            Response::error("Erro ao atualizar mensagem", 500);
        }

        Response::success(null, "Mensagem atualizada com sucesso");
    }

    /**
     * PUT /api/v1/mensagens/{id}/visualizar
     * Marca mensagens como visualizadas
     */
    public static function markAsViewed($id)
    {
        $query = Router::getQueryParams();
        $numero = $query['numero'] ?? null;

        if (!$numero) {
            Response::validationError(['numero' => "Parâmetro obrigatório"]);
        }

        $result = Mensagem::markAsViewed($id, $numero);

        if ($result === false) {
            Response::error("Erro ao atualizar visualização", 500);
        }

        Response::success(null, "Mensagens marcadas como visualizadas");
    }

    /**
     * POST /api/v1/mensagens/{id}/reacao
     * Adiciona reação a mensagem
     */
    public static function addReaction($chatId)
    {
        $body = Router::getJsonBody();

        if (!isset($body['reacao'])) {
            Response::validationError(['reacao' => "Campo obrigatório"]);
        }

        $result = Mensagem::addReaction($chatId, (int)$body['reacao']);

        if ($result === false) {
            Response::error("Erro ao adicionar reação", 500);
        }

        Response::success(null, "Reação adicionada com sucesso");
    }

    /**
     * DELETE /api/v1/mensagens/{id}
     * Deleta uma mensagem
     */
    public static function delete($chatId)
    {
        $query = Router::getQueryParams();

        // Obtém a mensagem
        $result = Database::query(
            "SELECT * FROM tbmsgatendimento WHERE chatid = ?",
            [$chatId]
        );

        if (empty($result)) {
            Response::notFound("Mensagem");
        }

        $msg = $result[0];

        $deleted = Mensagem::delete($msg['id'], $msg['numero'], $msg['seq']);

        if ($deleted === false) {
            Response::error("Erro ao deletar mensagem", 500);
        }

        Response::success(null, "Mensagem deletada com sucesso");
    }

    /**
     * POST /api/v1/atendimentos/{id}/anexos
     * Cria novo anexo
     */
    public static function createAnexo($id)
    {
        $body = Router::getJsonBody();

        $required = ['numero', 'seq', 'nomeArquivo', 'tipoArquivo', 'arquivo'];
        foreach ($required as $field) {
            if (!isset($body[$field])) {
                Response::validationError([$field => "Campo obrigatório"]);
            }
        }

        $anexo = Anexo::create(
            $id,
            $body['seq'],
            $body['numero'],
            $body['arquivo'],
            $body['nomeArquivo'],
            $body['nomeOriginal'] ?? $body['nomeArquivo'],
            $body['tipoArquivo'],
            $body['canal'] ?? 1,
            $body['enviado'] ?? 1
        );

        if ($anexo === false) {
            Response::error("Erro ao criar anexo", 500);
        }

        Response::success($anexo, "Anexo criado com sucesso", 201);
    }

    /**
     * GET /api/v1/anexos/{id}/download
     * Baixa arquivo anexo
     */
    public static function downloadAnexo($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                Response::validationError(['id' => 'ID do anexo inválido']);
                return;
            }

            $anexo = Anexo::getById($id);

            if (!$anexo) {
                Response::notFound('Anexo não encontrado');
                return;
            }

            // Caminho do arquivo
            $filepath = __DIR__ . '/../../' . $anexo['caminho'];

            if (!file_exists($filepath)) {
                Response::notFound('Arquivo não encontrado no servidor');
                return;
            }

            // Registrar download (auditoria)
            self::registerDownload($id);

            // Configurar headers para download
            header('Content-Type: ' . $anexo['tipo_arquivo']);
            header('Content-Disposition: attachment; filename="' . $anexo['nome_arquivo'] . '"');
            header('Content-Length: ' . filesize($filepath));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // Enviar arquivo
            readfile($filepath);
            exit;

        } catch (Exception $e) {
            Response::internalError($e->getMessage());
        }
    }

    /**
     * Registra download na auditoria
     */
    private static function registerDownload($anexoId)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO tb_audit_download (anexo_id, download_em, ip)
                VALUES (?, NOW(), ?)
            ");
            $stmt->execute([$anexoId, $_SERVER['REMOTE_ADDR']]);
        } catch (Exception $e) {
            // Log silently
        }
    }
}
?>
