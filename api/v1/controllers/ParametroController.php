<?php
/**
 * SAW API v1 - Controller Parâmetros
 */

class ParametroController
{
    /**
     * GET /api/v1/parametros
     * Obtém todos os parâmetros
     */
    public static function getAll()
    {
        $parametros = Parametro::getAll();

        if (!$parametros) {
            Response::notFound("Parâmetros");
        }

        Response::success($parametros, "Parâmetros obtidos com sucesso");
    }

    /**
     * PUT /api/v1/parametros/{id}
     * Atualiza parâmetro
     */
    public static function update($id)
    {
        $body = Router::getJsonBody();

        if (empty($body)) {
            Response::validationError(['data' => "Dados obrigatórios"]);
        }

        $result = Parametro::update($id, $body);

        if ($result === false) {
            Response::error("Erro ao atualizar parâmetros", 500);
        }

        $parametros = Parametro::getAll();
        Response::success($parametros, "Parâmetros atualizados com sucesso");
    }
}
?>
