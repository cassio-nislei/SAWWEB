<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

header('Content-Type: application/json');

try {
    // Pega todas as conversas com tratamento de erro
    $query = "
        SELECT 
            ta.id as idatendimento,
            ta.numero,
            ta.nome as nome,
            ta.situacao,
            ta.dt_atend as dt_inicio,
            ta.dt_fim,
            ta.canal,
            COALESCE(COUNT(tm.id), 0) as qtd_mensagens,
            COALESCE(SUM(CASE WHEN tm.id_atend = 0 AND tm.notificada = false THEN 1 ELSE 0 END), 0) as qtd_msg_novas,
            (SELECT msg FROM tbmsgatendimento WHERE id = ta.id ORDER BY seq DESC LIMIT 1) as ultima_msg,
            (SELECT DATE_FORMAT(dt_msg, '%H:%i') FROM tbmsgatendimento WHERE id = ta.id ORDER BY seq DESC LIMIT 1) as hora_ultima_msg
        FROM tbatendimento ta
        LEFT JOIN tbmsgatendimento tm ON tm.id = ta.id AND tm.canal = 0
        WHERE ta.situacao IN ('A', 'T', 'P', 'F')
        GROUP BY ta.id, ta.numero, ta.nome, ta.situacao, ta.dt_atend, ta.dt_fim, ta.canal
        ORDER BY CASE 
            WHEN ta.situacao = 'A' THEN 1
            WHEN ta.situacao = 'T' THEN 2
            WHEN ta.situacao = 'P' THEN 3
            ELSE 4
        END, ta.dt_atend DESC
    ";

    $result = mysqli_query($conexao, $query);
    
    if (!$result) {
        throw new Exception('Erro na query: ' . mysqli_error($conexao));
    }
    
    $conversas = array();

    while ($row = mysqli_fetch_assoc($result)) {
        // Limita a última mensagem a 100 caracteres
        if (!empty($row['ultima_msg']) && strlen($row['ultima_msg']) > 100) {
            $row['ultima_msg'] = substr($row['ultima_msg'], 0, 100) . '...';
        }
        
        // Mapeia a situação corretamente
        $situacaoMap = array(
            'A' => 'ativo',
            'T' => 'ativo',
            'P' => 'pendente',
            'F' => 'finalizado'
        );
        $row['situacao'] = $situacaoMap[$row['situacao']] ?? 'ativo';
        
        $conversas[] = $row;
    }

    echo json_encode($conversas);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
