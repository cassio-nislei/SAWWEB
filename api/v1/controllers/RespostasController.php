<?php
/**
 * RespostasController - Gerencia endpoints de respostas automáticas
 * Q4: Buscar resposta automática
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class RespostasController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q4: Buscar resposta automática
     * GET /respostas-automaticas?id_menu=1
     */
    public function buscar()
    {
        try {
            $idMenu = (int)($_GET['id_menu'] ?? 0);

            if ($idMenu <= 0) {
                Response::error('id_menu obrigatório', 400);
                return;
            }

            $sql = "SELECT * FROM tbresposta WHERE id = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idMenu]);
            $resposta = $stmt->fetch(\PDO::FETCH_ASSOC);

            Response::success($resposta ?? [], 'Resposta encontrada');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }
}
?>
