<?php
/**
 * SAW API v1 - Guia de Migração
 * 
 * Este arquivo mostra como migrar o código existente para usar a API
 */

echo "═══════════════════════════════════════════════════════════════\n";
echo "       GUIA DE MIGRAÇÃO - Código Existente para API         \n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "📋 ANTES (Código Atual):\n";
echo "├── Queries diretas ao banco\n";
echo "├── Lógica no PHP da página\n";
echo "├── Sem separação de camadas\n";
echo "└── Acoplamento entre apresentação e dados\n\n";

echo "✨ DEPOIS (Com API):\n";
echo "├── Cliente HTTP (APIClient)\n";
echo "├── Camada de Controladores\n";
echo "├── Camada de Modelos\n";
echo "├── Camada de Roteamento\n";
echo "└── Banco de dados isolado\n\n";

echo "═══════════════════════════════════════════════════════════════\n\n";

// ============================================
// EXEMPLO 1: CRIAR ATENDIMENTO
// ============================================

echo "🔄 EXEMPLO 1: Criar Atendimento\n\n";

echo "❌ CÓDIGO ANTIGO (atendimento/gerarAtendimento.php):\n";
echo "─────────────────────────────────────────────────────────────\n";
echo <<<'PHP'
<?php
require_once("../includes/padrao.inc.php");

$s_celular_atendimento = $_POST["numero"];
$s_nome = $_POST["nome"];
$id_atend = $_SESSION["usuariosaw"]["id"];
$nome_atend = $_SESSION["usuariosaw"]["nome"];

$qryaux = mysqli_query(
    $conexao,
    "SELECT coalesce(max(id),0)+1 SEQ 
     FROM tbatendimento
     WHERE numero = '$s_celular_atendimento'"
);

$listaqryaux = mysqli_fetch_array($qryaux);
$I_SEQ = $listaqryaux['SEQ'];
$s_id_atendimento = $I_SEQ;

$qryaux = mysqli_query(
    $conexao,
    "INSERT INTO tbatendimento (id,situacao,nome,id_atend,nome_atend,numero,setor,dt_atend,hr_atend,canal,protocolo)
     VALUES('$s_id_atendimento','A','$s_nome','$id_atend','$s_nome','$s_celular_atendimento','$idDepartamento',CURDATE(),CURTIME(),'$idCanal','$protocolo')"
);

if( $qryaux ){ echo $s_id_atendimento; }
else{ echo "erro"; }
?>
PHP;

echo "\n\n✅ CÓDIGO NOVO (com API):\n";
echo "─────────────────────────────────────────────────────────────\n";
echo <<<'PHP'
<?php
// Carrega cliente da API
require_once("../api/APIClient.php");
$api = new APIClient();

try {
    // Dados da requisição
    $numero = $_POST["numero"];
    $nome = $_POST["nome"];
    $idAtende = $_SESSION["usuariosaw"]["id"];
    $nomeAtende = $_SESSION["usuariosaw"]["nome"];
    $setor = isset($idDepartamento) ? $idDepartamento : 1;
    
    // Cria atendimento via API
    $response = $api->createAtendimento(
        $numero,
        $nome,
        $idAtende,
        $nomeAtende,
        'P', // situacao
        1,   // canal
        $setor
    );
    
    // Retorna ID do atendimento criado
    echo $response['data']['id'];
    
} catch (Exception $e) {
    echo "erro";
}
?>
PHP;

echo "\n\n";

// ============================================
// EXEMPLO 2: CRIAR MENSAGEM
// ============================================

echo "🔄 EXEMPLO 2: Criar Mensagem\n\n";

echo "❌ CÓDIGO ANTIGO (atendimento/gravarMensagem.php):\n";
echo "─────────────────────────────────────────────────────────────\n";
echo <<<'PHP'
<?php
require_once("../includes/padrao.inc.php");

$strNumero = $_POST["numero"];
$idAtendimento = $_POST["id_atendimento"];
$strMensagem = $_POST["msg"];

$newSequence = newSequence($conexao, $idAtendimento, $strNumero, $idCanal);

$inseremsg = mysqli_query(
    $conexao, 
    "INSERT INTO tbmsgatendimento(id,seq,numero,msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal)
     VALUES('".$idAtendimento."','".$newSequence."' ,'".$strNumero."','".$strMensagem."','...',
     'E',NOW(),CURTIME(),'".$intUserId."','".$idCanal."')"
);

if( $inseremsg ){ echo "1"; }
else{ echo "0"; }
?>
PHP;

echo "\n\n✅ CÓDIGO NOVO (com API):\n";
echo "─────────────────────────────────────────────────────────────\n";
echo <<<'PHP'
<?php
require_once("../api/APIClient.php");
$api = new APIClient();

try {
    $numero = $_POST["numero"];
    $idAtendimento = $_POST["id_atendimento"];
    $mensagem = $_POST["msg"];
    
    // Cria mensagem via API
    $response = $api->createMensagem(
        $idAtendimento,
        $numero,
        $mensagem,
        '',
        $_SESSION["usuariosaw"]["id"],
        $_SESSION["usuariosaw"]["nome"]
    );
    
    echo "1";
    
} catch (Exception $e) {
    echo "0";
}
?>
PHP;

echo "\n\n";

// ============================================
// EXEMPLO 3: LISTAR ATENDIMENTOS
// ============================================

echo "🔄 EXEMPLO 3: Listar Atendimentos\n\n";

echo "❌ CÓDIGO ANTIGO (com query direta):\n";
echo "─────────────────────────────────────────────────────────────\n";
echo <<<'PHP'
<?php
require_once("includes/padrao.inc.php");

$qry = mysqli_query($conexao, "SELECT * FROM tbatendimento WHERE situacao IN ('P','A','T') ORDER BY dt_atend DESC");

while($obj = mysqli_fetch_object($qry)) {
    echo "<tr>";
    echo "<td>" . $obj->numero . "</td>";
    echo "<td>" . $obj->nome . "</td>";
    echo "<td>" . $obj->situacao . "</td>";
    echo "</tr>";
}
?>
PHP;

echo "\n\n✅ CÓDIGO NOVO (com API):\n";
echo "─────────────────────────────────────────────────────────────\n";
echo <<<'PHP'
<?php
require_once("api/APIClient.php");
$api = new APIClient();

try {
    // Lista atendimentos ativos via API
    $response = $api->listAtendimentosAtivos();
    
    $atendimentos = $response['data'];
    
    foreach($atendimentos as $atendimento) {
        echo "<tr>";
        echo "<td>" . $atendimento['numero'] . "</td>";
        echo "<td>" . $atendimento['nome'] . "</td>";
        echo "<td>" . $atendimento['situacao'] . "</td>";
        echo "</tr>";
    }
    
} catch (Exception $e) {
    echo "<tr><td colspan='3'>Erro: " . $e->getMessage() . "</td></tr>";
}
?>
PHP;

echo "\n\n";

// ============================================
// BENEFÍCIOS DA MIGRAÇÃO
// ============================================

echo "═══════════════════════════════════════════════════════════════\n\n";
echo "✨ BENEFÍCIOS DA MIGRAÇÃO:\n\n";

$beneficios = [
    "1. Separação de Responsabilidades" => [
        "• Dados isolados em modelos",
        "• Lógica em controladores",
        "• Apresentação no frontend",
    ],
    "2. Reutilização de Código" => [
        "• API pode ser usada por múltiplos clientes",
        "• Mobile, Desktop, Web compartilham mesma API",
        "• Reduz duplicação de código",
    ],
    "3. Manutenibilidade" => [
        "• Mudanças localizadas em um lugar",
        "• Testes mais fáceis",
        "• Debugging simplificado",
    ],
    "4. Segurança" => [
        "• Validação centralizada",
        "• Proteção contra SQL Injection",
        "• Controle de acesso por rota",
    ],
    "5. Performance" => [
        "• Cache em nível de API",
        "• Otimização de queries",
        "• Reuso de conexões",
    ],
    "6. Escalabilidade" => [
        "• Mais fácil adicionar novos recursos",
        "• API versioning (v1, v2, v3...)",
        "• Preparado para microserviços",
    ]
];

foreach ($beneficios as $titulo => $items) {
    echo "📌 $titulo\n";
    foreach ($items as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

// ============================================
// PLANO DE MIGRAÇÃO
// ============================================

echo "═══════════════════════════════════════════════════════════════\n\n";
echo "📊 PLANO DE MIGRAÇÃO GRADUAL:\n\n";

$fases = [
    "Fase 1: Preparação (Semana 1)" => [
        "✓ Testes da API em ambiente de desenvolvimento",
        "✓ Documentação atualizada",
        "✓ APIClient testado",
    ],
    "Fase 2: Refatoração Gradual (Semana 2-3)" => [
        "• Migrar gerarAtendimento.php",
        "• Migrar gravarMensagem.php",
        "• Migrar listaConversas.php",
        "• Testar cada mudança",
    ],
    "Fase 3: Componentes Complexos (Semana 4)" => [
        "• Dashboard",
        "• Relatórios",
        "• Agendamentos",
    ],
    "Fase 4: Teste Completo (Semana 5)" => [
        "• Testes de integração",
        "• Teste de carga",
        "• Validação em staging",
    ],
    "Fase 5: Deploy (Semana 6)" => [
        "• Deploy em produção",
        "• Monitoramento",
        "• Suporte técnico",
    ]
];

foreach ($fases as $fase => $items) {
    echo "📌 $fase\n";
    foreach ($items as $item) {
        echo "   $item\n";
    }
    echo "\n";
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "\n✅ Migração planejada com sucesso!\n";
?>
