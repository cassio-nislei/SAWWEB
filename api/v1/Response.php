<?php
/**
 * SAW API v1 - Classe Response
 * Padroniza respostas da API
 */

class Response
{
    /**
     * Envia resposta de sucesso
     */
    public static function success($data = null, $message = "Sucesso", $statusCode = 200)
    {
        http_response_code($statusCode);

        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Envia resposta de erro
     */
    public static function error($message = "Erro", $statusCode = 400, $errors = null)
    {
        http_response_code($statusCode);

        $response = [
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Envia resposta com lista paginada
     */
    public static function paginated($data = [], $total = 0, $page = 1, $perPage = 20, $message = "Sucesso")
    {
        http_response_code(200);

        $totalPages = ceil($total / $perPage);

        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => (int)$page,
                'perPage' => (int)$perPage,
                'totalPages' => $totalPages
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Envia erro 404
     */
    public static function notFound($resource = "Recurso")
    {
        self::error($resource . " não encontrado", 404);
    }

    /**
     * Envia erro de validação
     */
    public static function validationError($errors)
    {
        self::error("Erro de validação", 400, $errors);
    }

    /**
     * Envia erro de autorização
     */
    public static function unauthorized($message = "Não autorizado")
    {
        self::error($message, 401);
    }

    /**
     * Envia erro de permissão
     */
    public static function forbidden($message = "Acesso proibido")
    {
        self::error($message, 403);
    }

    /**
     * Envia erro interno
     */
    public static function internalError($message = "Erro interno do servidor")
    {
        self::error($message, 500);
    }
}
?>
