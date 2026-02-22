<?php
/**
 * SAW API v1 - Model Menu
 */

class Menu
{
    /**
     * Obtém menus principais (sem pai)
     */
    public static function getMainMenus()
    {
        return Database::query(
            "SELECT * FROM tbmenu WHERE pai IS NULL OR pai = 0 ORDER BY id",
            []
        );
    }

    /**
     * Obtém submenus
     */
    public static function getSubmenus($idPai)
    {
        return Database::query(
            "SELECT * FROM tbmenu WHERE pai = ? ORDER BY id",
            [$idPai]
        );
    }

    /**
     * Obtém menu por ID
     */
    public static function getById($id)
    {
        $result = Database::query(
            "SELECT * FROM tbmenu WHERE id = ?",
            [$id]
        );

        return $result[0] ?? null;
    }

    /**
     * Obtém resposta automática do menu
     */
    public static function getAutoResponser($idMenu)
    {
        $result = Database::query(
            "SELECT * FROM tbrespostasautomaticas WHERE id_menu = ? LIMIT 1",
            [$idMenu]
        );

        return $result[0] ?? null;
    }
}
?>
