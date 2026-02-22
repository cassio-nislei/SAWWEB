<?php
// Set header BEFORE any includes or output
header('Content-Type: application/json; charset=utf-8');

// Only include database connection - no session handling needed for API
require_once("../../includes/conexao.php");

// Debug: verificar tabelas disponíveis
$debug = isset($_GET['debug']) ? true : false;

// Buscar atendimentos agrupados por situação
$query = "
    SELECT 
        ta.id,
        ta.numero as numero,
        ta.nome as nome,
        ta.situacao,
        COALESCE(td.departamento, 'Sem Departamento') as departamento,
        ta.nome_atend as atendente,
        DATE_FORMAT(ta.dt_atend, '%H:%i') as hora,
        ta.hr_atend,
        ta.canal as id_canal,
        tc.foto_perfil as foto_perfil,
        ta.protocolo as protocolo
    FROM tbatendimento ta
    LEFT JOIN tbdepartamentos td ON ta.setor = td.id
    LEFT JOIN tbcontatos tc ON ta.numero = tc.numero
    WHERE (
        (ta.situacao IN ('TRIAGEM', 'PENDENTE', 'ATENDENDO') OR ta.situacao IN ('T', 'P', 'A'))
        OR (ta.situacao IN ('FINALIZADO', 'F') AND DATE(ta.dt_atend) = CURDATE())
    )
    ORDER BY ta.dt_atend DESC
    LIMIT 1000
";

$result = mysqli_query($conexao, $query);

$atendimentos = [];

if(!$result) {
    $error = mysqli_error($conexao);
    if($debug) {
        echo json_encode([
            'error' => $error, 
            'query' => $query,
            'total' => 0
        ], JSON_PRETTY_PRINT);
    } else {
        echo json_encode([]);
    }
    exit;
}

while($row = mysqli_fetch_assoc($result)) {
    // Mapear situação para o padrão esperado
    $situacao = strtoupper($row['situacao']);
    if($situacao === 'T') $situacao = 'TRIAGEM';
    if($situacao === 'P') $situacao = 'PENDENTE';
    if($situacao === 'A') $situacao = 'ATENDENDO';
    if($situacao === 'F') $situacao = 'FINALIZADO';
    
    // Processar foto de perfil
    $fotoPerfil = '';
    if(!empty($row['foto_perfil'])) {
        // Se já for base64, usar direto; caso contrário, converter
        if(strpos($row['foto_perfil'], 'data:image') === 0) {
            $fotoPerfil = $row['foto_perfil'];
        } else {
            $fotoPerfil = 'data:image/jpeg;base64,' . $row['foto_perfil'];
        }
    }
    
    $atendimentos[] = [
        'id' => $row['id'],
        'numero' => $row['numero'],
        'unique_id' => $row['id'] . '_' . $row['numero'],
        'nome' => $row['nome'],
        'situacao' => $situacao,
        'departamento' => $row['departamento'],
        'atendente' => $row['atendente'],
        'hora' => $row['hora'],
        'hr_atend' => $row['hr_atend'],
        'id_canal' => $row['id_canal'],
        'foto_perfil' => $fotoPerfil,
        'protocolo' => $row['protocolo']
    ];
}

if($debug) {
    echo json_encode([
        'total' => count($atendimentos), 
        'query' => $query,
        'data' => $atendimentos
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode($atendimentos);
}
?>

