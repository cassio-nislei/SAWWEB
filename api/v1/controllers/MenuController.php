<?php
/**
 * SAW API v1 - Controller Menus
 */

class MenuController
{
    /**
     * GET /api/v1/menus
     * Lista menus
     */
    public static function list()
    {
        $menus = Menu::getMainMenus();

        Response::success($menus, "Menus listados com sucesso");
    }

    /**
     * GET /api/v1/menus/{id}
     * Obtém menu por ID
     */
    public static function getById($id)
    {
        $menu = Menu::getById($id);

        if (!$menu) {
            Response::notFound("Menu");
        }

        // Obtém submenus
        $submenus = Menu::getSubmenus($id);
        $menu['submenus'] = $submenus;

        Response::success($menu, "Menu obtido com sucesso");
    }

    /**
     * GET /api/v1/menus/{id}/resposta-automatica
     * Obtém resposta automática do menu
     */
    public static function getAutoResponser($id)
    {
        $resposta = Menu::getAutoResponser($id);

        if (!$resposta) {
            Response::notFound("Resposta automática");
        }

        Response::success($resposta, "Resposta automática obtida com sucesso");
    }

    /**
     * GET /api/v1/menus/submenus/{idPai}
     * Lista submenus de um menu pai
     */
    public static function listSubmenus($idPai)
    {
        $submenus = Menu::getSubmenus($idPai);

        Response::success($submenus, "Submenus listados com sucesso");
    }
}
?>
