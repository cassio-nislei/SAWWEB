<?php
/**
 * SAW API v1 - Controller Horários
 */

class HorarioController
{
    /**
     * GET /api/v1/horarios/funcionamento
     * Obtém horários de funcionamento
     */
    public static function getFuncionamento()
    {
        $query = Router::getQueryParams();
        $diaSemana = isset($query['dia']) ? (int)$query['dia'] : null;

        $horarios = Horario::getFuncionamento($diaSemana);

        Response::success($horarios, "Horários de funcionamento obtidos com sucesso");
    }

    /**
     * GET /api/v1/horarios/aberto
     * Verifica se está aberto
     */
    public static function isOpen()
    {
        $query = Router::getQueryParams();
        $diaSemana = isset($query['dia']) ? (int)$query['dia'] : null;

        $aberto = Horario::isOpen($diaSemana);

        Response::success(['aberto' => $aberto], "Status verificado com sucesso");
    }
}
?>
