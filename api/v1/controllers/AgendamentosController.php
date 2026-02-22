<?php
/**
 * AgendamentosController - Gerencia endpoints de agendamentos
 * Q2: Mensagens agendadas pendentes
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class AgendamentosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q2: Mensagens agendadas pendentes
     * GET /agendamentos/pendentes?canal=1
     */
    public function pendentes()
    {
        try {
            $canal = (int)($_GET['canal'] ?? 1);

            $sql = "SELECT * FROM tbatendimento
                   WHERE DATE(data_criacao) = CURDATE()
                   AND TIME(data_criacao) >= CURRENT_TIME() - INTERVAL 2 MINUTE
                   AND situacao = 'P'";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $agendamentos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::success(['agendamentos' => $agendamentos], 'Agendamentos pendentes');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }
}
?>
