<?php
/**
 * DepartamentosController - Gerencia endpoints de departamentos
 * Q5: Buscar departamento por menu
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class DepartamentosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q5: Buscar departamento por menu
     * GET /departamentos/por-menu?id_menu=1
     */
    public function porMenu()
    {
        try {
            $idMenu = (int)($_GET['id_menu'] ?? 0);

            if ($idMenu <= 0) {
                Response::error('id_menu obrigatório', 400);
                return;
            }

            $sql = "SELECT * FROM tbdepartamento WHERE id = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$idMenu]);
            $departamento = $stmt->fetch(\PDO::FETCH_ASSOC);

            Response::success($departamento ?? [], 'Departamento encontrado');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }
}
?>
