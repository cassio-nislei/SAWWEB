<?php
/**
 * ParametrosController - Gerencia endpoints de parâmetros
 * Q10: Buscar parâmetros do sistema
 * P9: Verificar expediente
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class ParametrosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q10: Buscar parâmetros do sistema
     * GET /parametros/sistema
     */
    public function sistema()
    {
        try {
            $sql = "SELECT * FROM tbparametros ORDER BY id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $parametros = $stmt->fetch(\PDO::FETCH_ASSOC);

            Response::success($parametros ?? [], 'Parâmetros do sistema');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P9: Verificar expediente
     * GET /parametros/verificar-expediente
     */
    public function verificarExpediente()
    {
        try {
            $diaSemana = (int)date('w'); // 0=domingo, 6=sábado
            $horaAtual = date('H:i:s');

            $sql = "SELECT * FROM tbparametros WHERE id = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $horario = $stmt->fetch(\PDO::FETCH_ASSOC);

            $estaAberto = true; // Implementar lógica de verificação real

            Response::success([
                'esta_aberto' => $estaAberto,
                'hora_atual' => $horaAtual,
                'dia_semana' => $diaSemana,
                'horario' => $horario
            ]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }
}
?>
