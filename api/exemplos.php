<?php
/**
 * SAW API v1 - Exemplos de Uso
 * 
 * Este arquivo contém exemplos de como usar a API
 */

// Configuração
$apiBaseUrl = 'http://localhost/SAW-main/api/v1';

/**
 * Função auxiliar para fazer requisições HTTP
 */
function apiRequest($method, $endpoint, $data = null, $queryParams = [])
{
    $url = $apiBaseUrl . $endpoint;

    if (!empty($queryParams)) {
        $url .= '?' . http_build_query($queryParams);
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

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

echo "╔════════════════════════════════════════════════════╗\n";
echo "║       SAW API v1 - Exemplos de Uso              ║\n";
echo "╚════════════════════════════════════════════════════╝\n\n";

// ============================================
// 1. VERIFICAR SAÚDE DA API
// ============================================

echo "1️⃣  Verificando saúde da API...\n";
$response = apiRequest('GET', '/');
echo "Status: " . $response['code'] . "\n";
echo "Resposta: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// ============================================
// 2. CRIAR NOVO ATENDIMENTO
// ============================================

echo "2️⃣  Criando novo atendimento...\n";
$newAtendimento = [
    'numero' => '5521999999999',
    'nome' => 'João Silva',
    'idAtende' => 1,
    'nomeAtende' => 'Maria Atendente',
    'situacao' => 'P',
    'canal' => 1,
    'setor' => 1
];
$response = apiRequest('POST', '/atendimentos', $newAtendimento);
echo "Status: " . $response['code'] . "\n";
echo "Resposta: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

$atendimentoId = $response['body']['data']['id'] ?? null;

// ============================================
// 3. LISTAR ATENDIMENTOS
// ============================================

echo "3️⃣  Listando atendimentos...\n";
$response = apiRequest('GET', '/atendimentos', null, [
    'page' => 1,
    'perPage' => 10,
    'situacao' => 'P'
]);
echo "Status: " . $response['code'] . "\n";
echo "Total: " . $response['body']['pagination']['total'] . "\n";
echo "Página: " . $response['body']['pagination']['page'] . "\n\n";

// ============================================
// 4. LISTAR ATENDIMENTOS ATIVOS
// ============================================

echo "4️⃣  Listando atendimentos ativos...\n";
$response = apiRequest('GET', '/atendimentos/ativos');
echo "Status: " . $response['code'] . "\n";
echo "Quantidade: " . count($response['body']['data']) . "\n\n";

// ============================================
// 5. OBTER ATENDIMENTO ESPECÍFICO
// ============================================

if ($atendimentoId) {
    echo "5️⃣  Obtendo atendimento específico (ID: $atendimentoId)...\n";
    $response = apiRequest('GET', '/atendimentos/' . $atendimentoId, null, [
        'numero' => '5521999999999'
    ]);
    echo "Status: " . $response['code'] . "\n";
    echo "Resposta: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

    // ============================================
    // 6. CRIAR MENSAGEM
    // ============================================

    echo "6️⃣  Criando nova mensagem...\n";
    $newMensagem = [
        'numero' => '5521999999999',
        'msg' => 'Olá! Como podemos ajudar você hoje?',
        'resp_msg' => '',
        'id_atend' => 1,
        'nome_chat' => 'Maria Atendente',
        'situacao' => 'E',
        'canal' => 1
    ];
    $response = apiRequest('POST', '/atendimentos/' . $atendimentoId . '/mensagens', $newMensagem);
    echo "Status: " . $response['code'] . "\n";
    echo "Resposta: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

    // ============================================
    // 7. LISTAR MENSAGENS
    // ============================================

    echo "7️⃣  Listando mensagens do atendimento...\n";
    $response = apiRequest('GET', '/atendimentos/' . $atendimentoId . '/mensagens', null, [
        'numero' => '5521999999999',
        'tipo' => 'current'
    ]);
    echo "Status: " . $response['code'] . "\n";
    echo "Quantidade de mensagens: " . count($response['body']['data']) . "\n\n";

    // ============================================
    // 8. LISTAR MENSAGENS PENDENTES
    // ============================================

    echo "8️⃣  Listando mensagens pendentes...\n";
    $response = apiRequest('GET', '/atendimentos/' . $atendimentoId . '/mensagens/pendentes');
    echo "Status: " . $response['code'] . "\n";
    echo "Quantidade: " . count($response['body']['data']) . "\n\n";

    // ============================================
    // 9. ATUALIZAR SITUAÇÃO DO ATENDIMENTO
    // ============================================

    echo "9️⃣  Atualizando situação do atendimento...\n";
    $response = apiRequest('PUT', '/atendimentos/' . $atendimentoId . '/situacao', ['situacao' => 'A'], ['numero' => '5521999999999']);
    echo "Status: " . $response['code'] . "\n";
    echo "Nova situação: " . $response['body']['data']['situacao'] . "\n\n";

    // ============================================
    // 10. FINALIZAR ATENDIMENTO
    // ============================================

    echo "🔟  Finalizando atendimento...\n";
    $response = apiRequest('POST', '/atendimentos/' . $atendimentoId . '/finalizar', [], ['numero' => '5521999999999']);
    echo "Status: " . $response['code'] . "\n";
    echo "Status final: " . $response['body']['data']['situacao'] . "\n\n";
}

// ============================================
// 11. OBTER PARÂMETROS
// ============================================

echo "1️⃣1️⃣  Obtendo parâmetros do sistema...\n";
$response = apiRequest('GET', '/parametros');
echo "Status: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "Usar protocolo: " . ($response['body']['data']['usar_protocolo'] ? 'Sim' : 'Não') . "\n";
}
echo "\n";

// ============================================
// 12. OBTER MENUS
// ============================================

echo "1️⃣2️⃣  Obtendo menus...\n";
$response = apiRequest('GET', '/menus');
echo "Status: " . $response['code'] . "\n";
echo "Quantidade de menus: " . count($response['body']['data']) . "\n\n";

// ============================================
// 13. OBTER HORÁRIOS DE FUNCIONAMENTO
// ============================================

echo "1️⃣3️⃣  Obtendo horários de funcionamento...\n";
$response = apiRequest('GET', '/horarios/funcionamento');
echo "Status: " . $response['code'] . "\n";
echo "Quantidade de horários: " . count($response['body']['data']) . "\n\n";

// ============================================
// 14. VERIFICAR SE ESTÁ ABERTO
// ============================================

echo "1️⃣4️⃣  Verificando se está aberto...\n";
$response = apiRequest('GET', '/horarios/aberto');
echo "Status: " . $response['code'] . "\n";
echo "Aberto: " . ($response['body']['data']['aberto'] ? 'Sim' : 'Não') . "\n\n";

echo "✅ Exemplos concluídos!\n";
?>
