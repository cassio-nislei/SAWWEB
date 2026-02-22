<?php
/**
 * MenusController - Gerencia endpoints de menus
 * Q11: Menu principal
 * Q12: Submenus
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class MenusController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q11: Menu principal
     * GET /menus/principal
     */
    public function principal()
    {
        try {
            $sql = "SELECT * FROM tbmenu WHERE pai IS NULL OR pai = 0 ORDER BY id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $menus = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::success(['menus' => $menus]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q12: Submenus
     * GET /menus/submenus
     */
    public function submenus()
    {
        try {
            $sql = "SELECT * FROM tbmenu WHERE pai > 0 OR pai IS NOT NULL ORDER BY id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $submenus = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::success(['submenus' => $submenus]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }
}
?>
