<?php
/**
 * SAW API v1 - Controller Atendimentos
 */

class AtendimentoController
{
    /**
     * GET /api/v1/atendimentos.
     * Lista atendimentos com filtros e paginação
     */
    public static function list()
    {
        $query = Router::getQueryParams();
        $page = isset($query['page']) ? (int)$query['page'] : 1;
        $perPage = isset($query['perPage']) ? (int)$query['perPage'] : 20;
        $perPage = min($perPage, 100); // Limita a 100 por página

        $filters = [];
        if (isset($query['situacao'])) $filters['situacao'] = $query['situacao'];
        if (isset($query['canal'])) $filters['canal'] = $query['canal'];
        if (isset($query['numero'])) $filters['numero'] = $query['numero'];
        if (isset($query['setor'])) $filters['setor'] = $query['setor'];

        $result = Atendimento::list($page, $perPage, $filters);

        Response::paginated(
            $result['data'],
            $result['total'],
            $result['page'],
            $result['perPage'],
            "Atendimentos listados com sucesso"
        );
    }

    /**
     * POST /api/v1/atendimentos
     * Cria novo atendimento
     */
    public static function create()
    {
        try {
            $body = Router::getJsonBody();

            // Validação
            $required = ['numero', 'nome', 'idAtende', 'nomeAtende'];
            foreach ($required as $field) {
                if (!isset($body[$field]) || empty($body[$field])) {
                    Response::validationError([$field => "Campo obrigatório"]);
                    return;
                }
            }

            // Verifica se já existe atendimento ativo
            $existing = Atendimento::checkActive($body['numero']);
            if ($existing) {
                Response::error("Já existe um atendimento ativo para este cliente", 409);
                return;
            }

            // Enforce Triagem for new attendances
            $situacao = 'T'; // T = Triagem
            $canal = (int)($body['canal'] ?? 1);
            $setor = 0; // 0 = Sem setor (Triagem)

            $atendimento = Atendimento::create(
                $body['numero'],
                $body['nome'],
                $body['idAtende'],
                $body['nomeAtende'],
                $situacao,
                $canal,
                $setor
            );

            if (!$atendimento) {
                Response::error("Erro ao criar atendimento: " . Database::getLastError(), 500);
                return;
            }

            Response::success($atendimento, "Atendimento criado com sucesso", 201);
        } catch (Exception $e) {
            Response::internalError("Erro ao processar requisição: " . $e->getMessage());
        }
    }

    /**
     * GET /api/v1/atendimentos/{id}
     * Obtém detalhes de um atendimento
     */
    public static function getById($id)
    {
        $query = Router::getQueryParams();
        $numero = $query['numero'] ?? null;

        if (!$numero) {
            Response::validationError(['numero' => "Campo obrigatório"]);
        }

        $atendimento = Atendimento::getById($id, $numero);

        if (!$atendimento) {
            Response::notFound("Atendimento");
        }

        Response::success($atendimento, "Atendimento obtido com sucesso");
    }

    /**
     * PUT /api/v1/atendimentos/{id}/situacao
     * Atualiza situação do atendimento
     */
    public static function updateSituacao($id)
    {
        $body = Router::getJsonBody();
        $query = Router::getQueryParams();

        if (!isset($body['situacao']) || empty($body['situacao'])) {
            Response::validationError(['situacao' => "Campo obrigatório"]);
        }

        if (!isset($query['numero']) || empty($query['numero'])) {
            Response::validationError(['numero' => "Parâmetro obrigatório"]);
        }

        $result = Atendimento::updateSituacao($id, $query['numero'], $body['situacao']);

        if ($result === false) {
            Response::error("Erro ao atualizar atendimento", 500);
        }

        $atendimento = Atendimento::getById($id, $query['numero']);
        Response::success($atendimento, "Situação atualizada com sucesso");
    }

    /**
     * PUT /api/v1/atendimentos/{id}/setor
     * Atualiza setor do atendimento
     */
    public static function updateSetor($id)
    {
        $body = Router::getJsonBody();
        $query = Router::getQueryParams();

        if (!isset($body['setor']) || empty($body['setor'])) {
            Response::validationError(['setor' => "Campo obrigatório"]);
        }

        if (!isset($query['numero']) || empty($query['numero'])) {
            Response::validationError(['numero' => "Parâmetro obrigatório"]);
        }

        $result = Atendimento::updateSetor($id, $query['numero'], $body['setor']);

        if ($result === false) {
            Response::error("Erro ao atualizar setor", 500);
        }

        $atendimento = Atendimento::getById($id, $query['numero']);
        Response::success($atendimento, "Setor atualizado com sucesso");
    }

    /**
     * POST /api/v1/atendimentos/{id}/finalizar
     * Finaliza um atendimento
     */
    public static function finalize($id)
    {
        $query = Router::getQueryParams();

        if (!isset($query['numero']) || empty($query['numero'])) {
            Response::validationError(['numero' => "Parâmetro obrigatório"]);
        }

        $result = Atendimento::finalize($id, $query['numero']);

        if ($result === false) {
            Response::error("Erro ao finalizar atendimento", 500);
        }

        $atendimento = Atendimento::getById($id, $query['numero']);
        Response::success($atendimento, "Atendimento finalizado com sucesso");
    }

    /**
     * GET /api/v1/atendimentos/ativos
     * Lista atendimentos ativos
     */
    public static function listActive()
    {
        $query = Router::getQueryParams();

        $filters = [];
        if (isset($query['canal'])) $filters['canal'] = $query['canal'];
        if (isset($query['numero'])) $filters['numero'] = $query['numero'];

        $atendimentos = Atendimento::listActive($filters);

        Response::success($atendimentos, "Atendimentos ativos listados com sucesso");
    }

    /**
     * GET /api/v1/atendimentos/por-numero/{numero}
     * Busca atendimento ativo por número de telefone
     */
    public static function getByNumber($numero)
    {
        try {
            if (empty($numero)) {
                Response::validationError(['numero' => 'Número de telefone é obrigatório']);
                return;
            }

            $atendimento = Atendimento::getByNumber($numero);

            if (!$atendimento) {
                Response::notFound('Nenhum atendimento ativo encontrado para este número');
                return;
            }

            Response::success($atendimento, "Atendimento encontrado");

        } catch (Exception $e) {
            Response::internalError($e->getMessage());
        }
    }

    /**
     * GET /api/v1/atendimentos/{id}/anexos
     * Lista anexos de um atendimento
     */
    public static function getAnexos($id)
    {
        try {
            if (empty($id) || !is_numeric($id)) {
                Response::validationError(['id' => 'ID do atendimento inválido']);
                return;
            }

            // Verificar se atendimento existe
            $atendimento = Atendimento::getById($id);
            if (!$atendimento) {
                Response::notFound('Atendimento não encontrado');
                return;
            }

            $anexos = Atendimento::getAnexos($id);

            Response::success([
                'atendimento_id' => $id,
                'total' => count($anexos),
                'data' => $anexos
            ], "Anexos listados com sucesso");

        } catch (Exception $e) {
            Response::internalError($e->getMessage());
        }
    }
}
?>
