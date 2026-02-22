#!/usr/bin/env php
<?php
/**
 * SAW API v1 - Testes de API
 * 
 * Execute: php test.php
 */

// Cores para terminal
class Color
{
    const GREEN = "\033[32m";
    const RED = "\033[31m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const RESET = "\033[0m";
}

// Classe de teste
class APITester
{
    private $baseUrl;
    private $totalTests = 0;
    private $passedTests = 0;
    private $failedTests = 0;

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
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $httpCode,
            'body' => json_decode($response, true)
        ];
    }

    /**
     * Testa endpoint
     */
    public function test($name, $method, $endpoint, $expectedCode = 200, $data = null, $queryParams = [])
    {
        $this->totalTests++;

        $response = $this->request($method, $endpoint, $data, $queryParams);

        if ($response['code'] === $expectedCode) {
            $this->passedTests++;
            echo Color::GREEN . "✓ PASS: " . Color::RESET . $name . " (" . $response['code'] . ")\n";
            return true;
        } else {
            $this->failedTests++;
            echo Color::RED . "✗ FAIL: " . Color::RESET . $name . " (esperado: $expectedCode, recebido: " . $response['code'] . ")\n";
            return false;
        }
    }

    /**
     * Exibe resumo dos testes
     */
    public function showSummary()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Testes Realizados: " . $this->totalTests . "\n";
        echo Color::GREEN . "✓ Passaram: " . $this->passedTests . Color::RESET . "\n";
        echo Color::RED . "✗ Falharam: " . $this->failedTests . Color::RESET . "\n";
        
        $percentage = $this->totalTests > 0 ? ($this->passedTests / $this->totalTests) * 100 : 0;
        echo "Taxa de Sucesso: " . number_format($percentage, 2) . "%\n";
        echo str_repeat("=", 60) . "\n";
    }
}

// Inicia testes
echo Color::BLUE . "╔════════════════════════════════════════════════════╗\n";
echo "║       SAW API v1 - Suite de Testes              ║\n";
echo "╚════════════════════════════════════════════════════╝" . Color::RESET . "\n\n";

$tester = new APITester();

// Testes
echo Color::YELLOW . "🧪 Testando Endpoints da API...\n\n" . Color::RESET;

// Health Check
echo "📋 Health Check:\n";
$tester->test("GET /", "GET", "/", 200);
echo "\n";

// Atendimentos
echo "📞 Atendimentos:\n";
$tester->test("GET /atendimentos", "GET", "/atendimentos", 200);
$tester->test("GET /atendimentos/ativos", "GET", "/atendimentos/ativos", 200);
$tester->test("POST /atendimentos (válido)", "POST", "/atendimentos", 201, [
    'numero' => '5521988776655',
    'nome' => 'Teste API',
    'idAtende' => 1,
    'nomeAtende' => 'Tester'
]);
$tester->test("POST /atendimentos (inválido)", "POST", "/atendimentos", 400, []);
echo "\n";

// Parâmetros
echo "⚙️  Parâmetros:\n";
$tester->test("GET /parametros", "GET", "/parametros", 200);
echo "\n";

// Menus
echo "📊 Menus:\n";
$tester->test("GET /menus", "GET", "/menus", 200);
echo "\n";

// Horários
echo "⏰ Horários:\n";
$tester->test("GET /horarios/funcionamento", "GET", "/horarios/funcionamento", 200);
$tester->test("GET /horarios/aberto", "GET", "/horarios/aberto", 200);
echo "\n";

// Testes com ID inválido
echo "❌ Tratamento de Erro:\n";
$tester->test("GET atendimento inexistente", "GET", "/atendimentos/999999", 404, null, ['numero' => '1234567890']);
$tester->test("DELETE mensagem inexistente", "DELETE", "/mensagens/999999", 404);
echo "\n";

// Exibe resumo
$tester->showSummary();

echo "\n" . Color::GREEN . "Testes concluídos!" . Color::RESET . "\n";
?>
