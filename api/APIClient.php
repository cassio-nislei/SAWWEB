<?php
/**
 * SAW API v1 - Cliente PHP para integração
 * 
 * Use esta classe para integrar a API no projeto existente
 * 
 * Exemplo:
 * $client = new APIClient();
 * $atendimentos = $client->listAtendimentos();
 * $atendimento = $client->createAtendimento($numero, $nome, ...);
 */

class APIClient
{
    private $baseUrl;
    private $timeout = 30;

    public function __construct($baseUrl = 'http://localhost/SAW-main/api/v1')
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Faz requisição HTTP
     */
    private function request($method, $endpoint, $data = null, $queryParams = [])
    {
        $url = $this->baseUrl . $endpoint;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("Erro na requisição: " . $error);
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 400) {
            $message = $decoded['message'] ?? 'Erro na requisição';
            throw new Exception($message, $httpCode);
        }

        return $decoded;
    }

    // ========================================
    // ATENDIMENTOS
    // ========================================

    /**
     * Lista atendimentos com filtros
     */
    public function listAtendimentos($page = 1, $perPage = 20, $filters = [])
    {
        $queryParams = array_merge(['page' => $page, 'perPage' => $perPage], $filters);
        return $this->request('GET', '/atendimentos', null, $queryParams);
    }

    /**
     * Cria novo atendimento
     */
    public function createAtendimento($numero, $nome, $idAtende, $nomeAtende, $situacao = 'P', $canal = 1, $setor = 1)
    {
        $data = compact('numero', 'nome', 'idAtende', 'nomeAtende', 'situacao', 'canal', 'setor');
        return $this->request('POST', '/atendimentos', $data);
    }

    /**
     * Obtém atendimento específico
     */
    public function getAtendimento($id, $numero)
    {
        return $this->request('GET', '/atendimentos/' . $id, null, ['numero' => $numero]);
    }

    /**
     * Lista atendimentos ativos
     */
    public function listAtendimentosAtivos($filters = [])
    {
        return $this->request('GET', '/atendimentos/ativos', null, $filters);
    }

    /**
     * Atualiza situação do atendimento
     */
    public function updateSituacao($id, $numero, $situacao)
    {
        return $this->request('PUT', '/atendimentos/' . $id . '/situacao', ['situacao' => $situacao], ['numero' => $numero]);
    }

    /**
     * Atualiza setor do atendimento
     */
    public function updateSetor($id, $numero, $setor)
    {
        return $this->request('PUT', '/atendimentos/' . $id . '/setor', ['setor' => $setor], ['numero' => $numero]);
    }

    /**
     * Finaliza atendimento
     */
    public function finalizeAtendimento($id, $numero)
    {
        return $this->request('POST', '/atendimentos/' . $id . '/finalizar', null, ['numero' => $numero]);
    }

    // ========================================
    // MENSAGENS
    // ========================================

    /**
     * Lista mensagens de um atendimento
     */
    public function listMensagens($id, $numero, $tipo = 'current')
    {
        return $this->request('GET', '/atendimentos/' . $id . '/mensagens', null, ['numero' => $numero, 'tipo' => $tipo]);
    }

    /**
     * Cria nova mensagem
     */
    public function createMensagem($id, $numero, $msg, $respMsg = '', $idAtend = 0, $nomeChat = '', $situacao = 'E', $canal = 1)
    {
        $data = [
            'numero' => $numero,
            'msg' => $msg,
            'resp_msg' => $respMsg,
            'id_atend' => $idAtend,
            'nome_chat' => $nomeChat,
            'situacao' => $situacao,
            'canal' => $canal
        ];
        return $this->request('POST', '/atendimentos/' . $id . '/mensagens', $data);
    }

    /**
     * Lista mensagens pendentes
     */
    public function listMensagensPendentes($id, $canal = null)
    {
        $params = [];
        if ($canal !== null) {
            $params['canal'] = $canal;
        }
        return $this->request('GET', '/atendimentos/' . $id . '/mensagens/pendentes', null, $params);
    }

    /**
     * Marca mensagens como visualizadas
     */
    public function markMensagensVisualizadas($id, $numero)
    {
        return $this->request('PUT', '/mensagens/' . $id . '/visualizar', null, ['numero' => $numero]);
    }

    /**
     * Adiciona reação a mensagem
     */
    public function addReaction($chatId, $reacao)
    {
        return $this->request('POST', '/mensagens/' . $chatId . '/reacao', ['reacao' => $reacao]);
    }

    /**
     * Deleta mensagem
     */
    public function deleteMensagem($chatId)
    {
        return $this->request('DELETE', '/mensagens/' . $chatId);
    }

    // ========================================
    // ANEXOS
    // ========================================

    /**
     * Cria novo anexo
     */
    public function createAnexo($id, $numero, $seq, $arquivo, $nomeArquivo, $tipoArquivo, $canal = 1)
    {
        $data = [
            'numero' => $numero,
            'seq' => $seq,
            'arquivo' => $arquivo,
            'nomeArquivo' => $nomeArquivo,
            'nomeOriginal' => $nomeArquivo,
            'tipoArquivo' => $tipoArquivo,
            'canal' => $canal,
            'enviado' => 1
        ];
        return $this->request('POST', '/atendimentos/' . $id . '/anexos', $data);
    }

    // ========================================
    // PARÂMETROS
    // ========================================

    /**
     * Obtém parâmetros do sistema
     */
    public function getParametros()
    {
        return $this->request('GET', '/parametros');
    }

    /**
     * Atualiza parâmetros
     */
    public function updateParametros($id, $data)
    {
        return $this->request('PUT', '/parametros/' . $id, $data);
    }

    // ========================================
    // MENUS
    // ========================================

    /**
     * Lista menus principais
     */
    public function listMenus()
    {
        return $this->request('GET', '/menus');
    }

    /**
     * Obtém menu específico
     */
    public function getMenu($id)
    {
        return $this->request('GET', '/menus/' . $id);
    }

    /**
     * Obtém resposta automática do menu
     */
    public function getAutoResponser($id)
    {
        return $this->request('GET', '/menus/' . $id . '/resposta-automatica');
    }

    /**
     * Lista submenus
     */
    public function listSubmenus($idPai)
    {
        return $this->request('GET', '/menus/submenus/' . $idPai);
    }

    // ========================================
    // HORÁRIOS
    // ========================================

    /**
     * Obtém horários de funcionamento
     */
    public function getHorariosFuncionamento($dia = null)
    {
        $params = [];
        if ($dia !== null) {
            $params['dia'] = $dia;
        }
        return $this->request('GET', '/horarios/funcionamento', null, $params);
    }

    /**
     * Verifica se está aberto
     */
    public function isOpen($dia = null)
    {
        $params = [];
        if ($dia !== null) {
            $params['dia'] = $dia;
        }
        return $this->request('GET', '/horarios/aberto', null, $params);
    }
}

/**
 * EXEMPLOS DE USO
 * 
 * // Instancia cliente
 * $api = new APIClient();
 * 
 * try {
 *     // Criar atendimento
 *     $atendimento = $api->createAtendimento(
 *         '5521999999999',
 *         'João Silva',
 *         1,
 *         'Maria',
 *         'P',
 *         1,
 *         1
 *     );
 *     
 *     $idAtendimento = $atendimento['data']['id'];
 *     
 *     // Criar mensagem
 *     $api->createMensagem(
 *         $idAtendimento,
 *         '5521999999999',
 *         'Olá! Como posso ajudar?',
 *         '',
 *         1,
 *         'Maria'
 *     );
 *     
 *     // Listar mensagens
 *     $mensagens = $api->listMensagens($idAtendimento, '5521999999999');
 *     
 *     // Finalizar atendimento
 *     $api->finalizeAtendimento($idAtendimento, '5521999999999');
 *     
 * } catch (Exception $e) {
 *     echo "Erro: " . $e->getMessage();
 * }
 */
?>
