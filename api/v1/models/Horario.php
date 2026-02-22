<?php
/**
 * SAW API v1 - Model Horario
 */

class Horario
{
    /**
     * Obtém horários de funcionamento
     */
    public static function getFuncionamento($diaSemana = null)
    {
        $sql = "SELECT * FROM tbhorarios WHERE fechado = 0";
        $params = [];

        if ($diaSemana !== null) {
            $sql .= " AND dia_semana = ?";
            $params[] = $diaSemana;
        }

        return Database::query($sql, $params);
    }

    /**
     * Verifica se está aberto
     */
    public static function isOpen($diaSemana = null)
    {
        if ($diaSemana === null) {
            $diaSemana = date('w'); // 0-6 (Sunday to Saturday)
        }

        $result = Database::query(
            "SELECT COUNT(id) as total FROM tbhorarios 
             WHERE dia_semana = ? AND (fechado = 0 OR (NOW() BETWEEN hr_inicio AND hr_fim))",
            [$diaSemana]
        );

        return isset($result[0]) && $result[0]['total'] > 0;
    }
}
?>
