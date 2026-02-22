<?php
/**
 * SAW API v1 - Classe Router
 * Gerencia roteamento de requisições
 */

class Router
{
    private $routes = [];
    private $currentMethod = null;
    private $currentPath = null;

    public function __construct()
    {
        $this->currentMethod = $_SERVER['REQUEST_METHOD'];
        $this->currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove o prefixo da API - suporta múltiplos caminhos
        $this->currentPath = preg_replace('#^(/SAW-main)?/api/v1#', '', $this->currentPath);
        
        if (empty($this->currentPath)) {
            $this->currentPath = '/';
        }
    }

    /**
     * Registra rota GET
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
        return $this;
    }

    /**
     * Registra rota POST
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
        return $this;
    }

    /**
     * Registra rota PUT
     */
    public function put($path, $callback)
    {
        $this->routes['PUT'][$path] = $callback;
        return $this;
    }

    /**
     * Registra rota DELETE
     */
    public function delete($path, $callback)
    {
        $this->routes['DELETE'][$path] = $callback;
        return $this;
    }

    /**
     * Registra rota PATCH
     */
    public function patch($path, $callback)
    {
        $this->routes['PATCH'][$path] = $callback;
        return $this;
    }

    /**
     * Dispara a rota apropriada
     */
    public function dispatch()
    {
        $method = $this->currentMethod;
        $path = $this->currentPath;

        // Verifica rota exata
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }

        // Verifica rotas com parâmetros
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            if ($this->matchRoute($route, $path, $matches)) {
                return call_user_func_array($callback, $matches);
            }
        }

        // Rota não encontrada
        Response::error('Endpoint não encontrado', 404);
    }

    /**
     * Verifica se a rota corresponde ao padrão
     */
    private function matchRoute($route, $path, &$matches)
    {
        // Converte rota em regex
        $pattern = preg_replace_callback('/{([a-zA-Z_][a-zA-Z0-9_]*)}/', function ($m) {
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $route);

        $pattern = '^' . $pattern . '$';

        if (preg_match('#' . $pattern . '#', $path, $regexMatches)) {
            // Remove os matches numéricos do preg_match
            $matches = [];
            foreach ($regexMatches as $key => $value) {
                if (!is_numeric($key)) {
                    $matches[$key] = $value;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Obtém parâmetros da URL
     */
    public static function getQueryParams()
    {
        return $_GET;
    }

    /**
     * Obtém corpo da requisição como JSON
     */
    public static function getJsonBody()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    /**
     * Obtém método HTTP atual
     */
    public function getCurrentMethod()
    {
        return $this->currentMethod;
    }

    /**
     * Obtém caminho atual
     */
    public function getCurrentPath()
    {
        return $this->currentPath;
    }
}
?>
