<?php
/**
 * SAW API v1 - Index
 * Ponto de entrada da API
 * 
 * Endpoint Base: /SAW-main/api/v1/
 * 
 * Exemplo de uso:
 * GET    /api/v1/atendimentos
 * POST   /api/v1/atendimentos
 * GET    /api/v1/atendimentos/{id}?numero=123456
 * GET    /api/v1/atendimentos/ativos
 * 
 * Data: 19/11/2025
 */

// ============================================
// CORS Headers
// ============================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH, HEAD');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Max-Age: 86400');

// Debug: Log das requisições (remover em produção)
error_log(date('Y-m-d H:i:s') . " - " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Carrega configurações
require_once __DIR__ . '/config.php';

// Cache do input uma única vez
if (!isset($GLOBALS['_php_input_cache'])) {
    $GLOBALS['_php_input_cache'] = file_get_contents('php://input');
}

// Carrega classes base
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/JWT.php';

// Carrega modelos
require_once __DIR__ . '/models/Atendimento.php';
require_once __DIR__ . '/models/Mensagem.php';
require_once __DIR__ . '/models/Anexo.php';
require_once __DIR__ . '/models/Parametro.php';
require_once __DIR__ . '/models/Menu.php';
require_once __DIR__ . '/models/Horario.php';
require_once __DIR__ . '/models/Usuario.php';

// Carrega controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AtendimentoController.php';
require_once __DIR__ . '/controllers/MensagemController.php';
require_once __DIR__ . '/controllers/ParametroController.php';
require_once __DIR__ . '/controllers/MenuController.php';
require_once __DIR__ . '/controllers/HorarioController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/UsuariosController.php';

// Carrega novos controllers (32 endpoints)
require_once __DIR__ . '/controllers/ContatosController.php';
require_once __DIR__ . '/controllers/AgendamentosController.php';
require_once __DIR__ . '/controllers/AtendimentosController.php';
require_once __DIR__ . '/controllers/MensagensController.php';
require_once __DIR__ . '/controllers/MensagensStatusController.php';
require_once __DIR__ . '/controllers/ParametrosController.php';
require_once __DIR__ . '/controllers/MenusController.php';
require_once __DIR__ . '/controllers/RespostasController.php';
require_once __DIR__ . '/controllers/DepartamentosController.php';
require_once __DIR__ . '/controllers/AvisosController.php';
require_once __DIR__ . '/controllers/AnexosController.php';
require_once __DIR__ . '/controllers/BancoDadosController.php';
require_once __DIR__ . '/controllers/ProceduresController.php';

try {
    // Cria router
    $router = new Router();

    // ============================================
    // ROTAS - AUTENTICAÇÃO
    // ============================================

    // POST - Login com JWT
    $router->post('/auth/login', function () {
        AuthController::login();
    });

    // POST - Renovar token JWT
    $router->post('/auth/refresh', function () {
        AuthController::refresh();
    });

    // GET - Validar token JWT
    $router->get('/auth/validate', function () {
        AuthController::validate();
    });

    // ============================================
    // ROTAS - ATENDIMENTOS
    // ============================================

    // GET - Lista atendimentos com filtros
    $router->get('/atendimentos', function () {
        AtendimentoController::list();
    });

    // POST - Cria novo atendimento
    $router->post('/atendimentos', function () {
        AtendimentoController::create();
    });

    // GET - Lista atendimentos ativos
    $router->get('/atendimentos/ativos', function () {
        AtendimentoController::listActive();
    });

    // GET - Busca atendimento por número de telefone
    $router->get('/atendimentos/por-numero/{numero}', function ($numero) {
        AtendimentoController::getByNumber($numero);
    });

    // GET - Obtém atendimento específico
    $router->get('/atendimentos/{id}', function ($id) {
        AtendimentoController::getById($id);
    });

    // GET - Lista anexos de um atendimento
    $router->get('/atendimentos/{id}/anexos', function ($id) {
        AtendimentoController::getAnexos($id);
    });

    // PUT - Atualiza situação do atendimento
    $router->put('/atendimentos/{id}/situacao', function ($id) {
        AtendimentoController::updateSituacao($id);
    });

    // PUT - Atualiza setor do atendimento
    $router->put('/atendimentos/{id}/setor', function ($id) {
        AtendimentoController::updateSetor($id);
    });

    // POST - Finaliza atendimento
    $router->post('/atendimentos/{id}/finalizar', function ($id) {
        AtendimentoController::finalize($id);
    });

    // ============================================
    // ROTAS - MENSAGENS
    // ============================================

    // GET - Lista mensagens de um atendimento
    $router->get('/atendimentos/{id}/mensagens', function ($id) {
        MensagemController::list($id);
    });

    // POST - Cria nova mensagem
    $router->post('/atendimentos/{id}/mensagens', function ($id) {
        MensagemController::create($id);
    });

    // GET - Lista mensagens pendentes
    $router->get('/atendimentos/{id}/mensagens/pendentes', function ($id) {
        MensagemController::listPending($id);
    });

    // PUT - Atualiza situação da mensagem
    $router->put('/mensagens/{id}/situacao', function ($id) {
        MensagemController::updateSituacao($id);
    });

    // PUT - Marca mensagens como visualizadas
    $router->put('/mensagens/{id}/visualizar', function ($id) {
        MensagemController::markAsViewed($id);
    });

    // POST - Adiciona reação a mensagem
    $router->post('/mensagens/{id}/reacao', function ($id) {
        MensagemController::addReaction($id);
    });

    // DELETE - Deleta mensagem
    $router->delete('/mensagens/{id}', function ($id) {
        MensagemController::delete($id);
    });

    // ============================================
    // ROTAS - ANEXOS
    // ============================================

    // POST - Cria novo anexo
    $router->post('/atendimentos/{id}/anexos', function ($id) {
        MensagemController::createAnexo($id);
    });

    // GET - Download de anexo
    $router->get('/anexos/{id}/download', function ($id) {
        MensagemController::downloadAnexo($id);
    });

    // ============================================
    // ROTAS - PARÂMETROS
    // ============================================

    // GET - Obtém todos os parâmetros
    $router->get('/parametros', function () {
        ParametroController::getAll();
    });

    // PUT - Atualiza parâmetros
    $router->put('/parametros/{id}', function ($id) {
        ParametroController::update($id);
    });

    // ============================================
    // ROTAS - MENUS
    // ============================================

    // GET - Lista menus
    $router->get('/menus', function () {
        MenuController::list();
    });

    // GET - Obtém menu específico
    $router->get('/menus/{id}', function ($id) {
        MenuController::getById($id);
    });

    // GET - Obtém resposta automática do menu
    $router->get('/menus/{id}/resposta-automatica', function ($id) {
        MenuController::getAutoResponser($id);
    });

    // GET - Lista submenus
    $router->get('/menus/submenus/{idPai}', function ($idPai) {
        MenuController::listSubmenus($idPai);
    });

    // ============================================
    // ROTAS - HORÁRIOS
    // ============================================

    // GET - Obtém horários de funcionamento
    $router->get('/horarios/funcionamento', function () {
        HorarioController::getFuncionamento();
    });

    // GET - Verifica se está aberto
    $router->get('/horarios/aberto', function () {
        HorarioController::isOpen();
    });

    // ============================================
    // ROTAS - DASHBOARD
    // ============================================

    // GET - Estatísticas do ano atual
    $router->get('/dashboard/ano-atual', function () {
        DashboardController::yearStats();
    });

    // GET - Atendimentos por mês
    $router->get('/dashboard/atendimentos-mensais', function () {
        DashboardController::monthlyStats();
    });

    // ============================================
    // ROTAS - USUÁRIOS
    // ============================================

    // GET - Lista usuários
    $router->get('/usuarios', function () {
        UsuariosController::list();
    });

    // GET - Usuário autenticado
    $router->get('/usuarios/me', function () {
        UsuariosController::me();
    });

    // ============================================
    // ROTA - HEALTH CHECK
    // ============================================

    // ============================================
    // ROTAS - NOVOS ENDPOINTS (32 endpoints)
    // ============================================

    // ============================================
    // CONTATOS (2 endpoints)
    // ============================================

    // Q1: POST - Exportar contatos
    $router->post('/contatos/exportar', function () {
        $controller = new ContatosController();
        $controller->exportar();
    });

    // Q7: GET - Buscar nome do contato por telefone
    $router->get('/contatos/buscar-nome', function () {
        $controller = new ContatosController();
        $controller->buscarNome();
    });

    // ============================================
    // AGENDAMENTOS (1 endpoint)
    // ============================================

    // Q2: GET - Mensagens agendadas pendentes
    $router->get('/agendamentos/pendentes', function () {
        $controller = new AgendamentosController();
        $controller->pendentes();
    });

    // ============================================
    // ATENDIMENTOS (6 endpoints)
    // ============================================

    // Q3: GET - Verificar atendimento pendente
    $router->get('/atendimentos/verificar-pendente', function () {
        $controller = new AtendimentosController();
        $controller->verificarPendente();
    });

    // P2: POST - Criar novo atendimento
    $router->post('/atendimentos/criar', function () {
        $controller = new AtendimentosController();
        $controller->criar();
    });

    // P1: PUT - Finalizar atendimento
    $router->put('/atendimentos/finalizar', function () {
        $controller = new AtendimentosController();
        $controller->finalizar();
    });

    // P3: POST - Gravar mensagem de atendimento
    $router->post('/atendimentos/gravar-mensagem', function () {
        $controller = new AtendimentosController();
        $controller->gravarMensagem();
    });

    // P8: PUT - Atualizar setor do atendimento
    $router->put('/atendimentos/atualizar-setor', function () {
        $controller = new AtendimentosController();
        $controller->atualizarSetor();
    });

    // Q16: GET - Buscar atendimentos inativos
    $router->get('/atendimentos/inativos', function () {
        $controller = new AtendimentosController();
        $controller->inativos();
    });

    // ============================================
    // MENSAGENS (8 endpoints)
    // ============================================

    // Q6: GET - Verificar se mensagem é duplicada
    $router->get('/mensagens/verificar-duplicada', function () {
        $controller = new MensagensController();
        $controller->verificarDuplicada();
    });

    // Q8: GET - Status de múltiplas mensagens
    $router->get('/mensagens/status-multiplas', function () {
        $controller = new MensagensController();
        $controller->statusMultiplas();
    });

    // Q13: GET - Mensagens pendentes de envio
    $router->get('/mensagens/pendentes-envio', function () {
        $controller = new MensagensController();
        $controller->pendentesEnvio();
    });

    // Q17: GET - Próxima sequência
    $router->get('/mensagens/proxima-sequencia', function () {
        $controller = new MensagensController();
        $controller->proximaSequencia();
    });

    // P5: PUT - Marcar mensagem como excluída
    $router->put('/mensagens/marcar-excluida', function () {
        $controller = new MensagensController();
        $controller->marcarExcluida();
    });

    // P6: PUT - Marcar reação
    $router->put('/mensagens/marcar-reacao', function () {
        $controller = new MensagensController();
        $controller->marcarReacao();
    });

    // P4: PUT - Marcar como enviada
    $router->put('/mensagens/marcar-enviada', function () {
        $controller = new MensagensController();
        $controller->marcarEnviada();
    });

    // P15: PUT - Atualizar envio de mensagem
    $router->put('/mensagens/atualizar-envio', function () {
        $controller = new MensagensController();
        $controller->atualizarEnvio();
    });

    // Q14: POST - Comparar duplicação
    $router->post('/mensagens/comparar-duplicacao', function () {
        $controller = new MensagensController();
        $controller->compararDuplicacao();
    });

    // ============================================
    // PARÂMETROS (2 endpoints)
    // ============================================

    // Q10: GET - Buscar parâmetros do sistema
    $router->get('/parametros/sistema', function () {
        $controller = new ParametrosController();
        $controller->sistema();
    });

    // P9: GET - Verificar expediente
    $router->get('/parametros/verificar-expediente', function () {
        $controller = new ParametrosController();
        $controller->verificarExpediente();
    });

    // ============================================
    // MENUS (2 endpoints)
    // ============================================

    // Q11: GET - Menu principal
    $router->get('/menus/principal', function () {
        $controller = new MenusController();
        $controller->principal();
    });

    // Q12: GET - Submenus
    $router->get('/menus/submenus', function () {
        $controller = new MenusController();
        $controller->submenus();
    });

    // ============================================
    // RESPOSTAS AUTOMÁTICAS (1 endpoint)
    // ============================================

    // Q4: GET - Buscar resposta automática
    $router->get('/respostas-automaticas', function () {
        $controller = new RespostasController();
        $controller->buscar();
    });

    // ============================================
    // DEPARTAMENTOS (1 endpoint)
    // ============================================

    // Q5: GET - Buscar departamento por menu
    $router->get('/departamentos/por-menu', function () {
        $controller = new DepartamentosController();
        $controller->porMenu();
    });

    // ============================================
    // AVISOS (4 endpoints)
    // ============================================

    // P7: POST - Registrar aviso sem expediente
    $router->post('/avisos/registrar-sem-expediente', function () {
        $controller = new AvisosController();
        $controller->registrar();
    });

    // P11: DELETE - Limpar avisos antigos
    $router->delete('/avisos/limpar-antigos', function () {
        $controller = new AvisosController();
        $controller->limparAntigos();
    });

    // P14: DELETE - Limpar avisos de número específico
    $router->delete('/avisos/limpar-numero', function () {
        $controller = new AvisosController();
        $controller->limparNumero();
    });

    // P15: GET - Verificar aviso existente
    $router->get('/avisos/verificar-existente', function () {
        $controller = new AvisosController();
        $controller->verificarExistente();
    });

    // Q18: GET - Buscar avisos por número (com limpeza de antigos)
    $router->get('/avisos/buscar-por-numero', function () {
        $controller = new AvisosController();
        $controller->buscarPorNumero();
    });

    // ============================================
    // ANEXOS (3 endpoints)
    // ============================================

    // Q19: GET - Listar anexos pendentes
    $router->get('/anexos/pendentes', function () {
        $controller = new AnexosController();
        $controller->listarPendentes();
    });

    // Q20: GET - Buscar anexo por PK
    $router->get('/anexos/{pk}', function ($pk) {
        $controller = new AnexosController();
        $controller->buscarPorPK($pk);
    });

    // P16: PUT - Marcar anexo como enviado
    $router->put('/anexos/{pk}/marcar-enviado', function ($pk) {
        $controller = new AnexosController();
        $controller->marcarEnviado($pk);
    });

    // ============================================
    // MENSAGENS - STATUS E SINCRONIZAÇÃO
    // ============================================

    // GET - Listar mensagens pendentes de verificação (para o Delphi verificar via WPPConnect)
    $router->get('/mensagens/pendentes-status', function () {
        MensagensStatusController::listarPendentes();
    });

    // POST - Atualizar status após WPPConnect verificar
    $router->post('/mensagens/status/atualizar', function () {
        MensagensStatusController::atualizarStatus();
    });

    // POST - Processar múltiplas atualizações de status em lote
    $router->post('/mensagens/status/processar-mult', function () {
        MensagensStatusController::processarMultiplasAtualizacoes();
    });

    // GET - Relatório de mensagens por status
    $router->get('/mensagens/status/relatorio', function () {
        MensagensStatusController::relatorioStatus();
    });

    // ============================================
    // BANCO DE DADOS - ESTRUTURA E VERIFICAÇÃO
    // ============================================

    // GET - Listar todas as tabelas
    $router->get('/banco-dados/tabelas', function () {
        BancoDadosController::listarTabelas();
    });

    // GET - Verificar se tabela existe
    $router->get('/banco-dados/tabela/existe', function () {
        BancoDadosController::tabelaExiste();
    });

    // GET - Verificar se campo existe em tabela
    $router->get('/banco-dados/campo/existe', function () {
        BancoDadosController::campoExiste();
    });

    // GET - Obter estrutura completa da tabela
    $router->get('/banco-dados/tabela/estrutura', function () {
        BancoDadosController::estruturaTabela();
    });

    // GET - Obter informações detalhadas de um campo
    $router->get('/banco-dados/campo/info', function () {
        BancoDadosController::infoCampo();
    });

    // POST - Criar nova tabela (admin only)
    $router->post('/banco-dados/tabela/criar', function () {
        BancoDadosController::criarTabela();
    });

    // POST - Adicionar campo a tabela (admin only)
    $router->post('/banco-dados/campo/adicionar', function () {
        BancoDadosController::adicionarCampo();
    });

    // ============================================
    // PROCEDURES - CRIAÇÃO E EXECUÇÃO
    // ============================================

    // GET - Listar todas as procedures
    $router->get('/procedures/listar', function () {
        ProceduresController::listar();
    });

    // GET - Verificar se procedure existe
    $router->get('/procedures/existe', function () {
        ProceduresController::existe();
    });

    // POST - Executar uma procedure
    $router->post('/procedures/executar', function () {
        ProceduresController::executar();
    });

    // POST - Criar nova procedure (admin only)
    $router->post('/procedures/criar', function () {
        ProceduresController::criar();
    });

    // POST - Remover procedure (admin only)
    $router->post('/procedures/droppar', function () {
        ProceduresController::droppar();
    });

    // POST - Executar SQL arbitrário (admin only)
    $router->post('/sql/executar', function () {
        ProceduresController::executarSQL();
    });

    // POST - Sincronizar estrutura de tabelas e colunas
    $router->post('/tabelas/sincronizar-estrutura', function () {
        ProceduresController::sincronizarEstrutura();
    });

    // ============================================
    // SWAGGER JSON - Com suporte completo a CORS
    // ============================================
    
    $router->get('/swagger.json', function () {
        serveSwaggerJson();
    });

    // Função para servir Swagger JSON
    function serveSwaggerJson() {
        // Headers CORS - DEVE estar ANTES de qualquer output
        header('Access-Control-Allow-Origin: *', true);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD, PATCH', true);
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Content-Length', true);
        header('Access-Control-Expose-Headers: Content-Length, Content-Type', true);
        header('Content-Type: application/json; charset=utf-8', true);
        header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0', true);
        header('Pragma: no-cache', true);
        header('Expires: 0', true);

        $swaggerFile = __DIR__ . '/../swagger.json';
        
        if (!file_exists($swaggerFile)) {
            http_response_code(404);
            echo json_encode(['error' => 'Swagger specification not found'], JSON_UNESCAPED_SLASHES);
            exit;
        }

        $content = file_get_contents($swaggerFile);
        if ($content === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Could not read swagger.json'], JSON_UNESCAPED_SLASHES);
            exit;
        }

        $decoded = json_decode($content, true);
        if ($decoded === null) {
            http_response_code(500);
            echo json_encode(['error' => 'Invalid JSON in swagger.json'], JSON_UNESCAPED_SLASHES);
            exit;
        }

        // Detectar URL do servidor atual
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:7080';
        $basePath = '/api/v1';
        $currentServerUrl = $protocol . '://' . $host . $basePath;

        // Atualizar servers dinamicamente
        if (isset($decoded['servers']) && is_array($decoded['servers'])) {
            foreach ($decoded['servers'] as &$server) {
                if (strpos($currentServerUrl, 'localhost') !== false || strpos($currentServerUrl, '127.0.0.1') !== false) {
                    if (strpos($server['description'] ?? '', 'Desenvolvimento') !== false || 
                        strpos($server['description'] ?? '', 'Development') !== false) {
                        $server['url'] = $currentServerUrl;
                    }
                }
            }
        }

        http_response_code(200);
        echo json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit;
    }

    // Alias para compatibilidade
    $router->get('/swagger-spec', function () {
        include __DIR__ . '/swagger-spec.php';
    });

    $router->get('/', function () {
        Response::success([
            'api' => 'SAW API',
            'version' => API_VERSION,
            'status' => 'running',
            'timestamp' => date('Y-m-d H:i:s')
        ], "API funcionando corretamente");
    });

    // Dispara a rota
    $router->dispatch();

} catch (Exception $e) {
    error_log("EXCEPTION: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
    Response::internalError($e->getMessage());
} catch (Error $e) {
    error_log("ERROR: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
    Response::internalError($e->getMessage());
} finally {
    Database::close();
}
?>
