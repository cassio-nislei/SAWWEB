<?php
require_once("./includes/conexao.php");

echo "<h2>Debug Kanban - Atendimentos</h2>";

// Query exata do kanban
$query = "
    SELECT 
        ta.id,
        ta.numero as numero,
        ta.nome as nome,
        ta.situacao,
        ta.dt_atend,
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

echo "<h3>Query executada:</h3>";
echo "<pre>" . htmlspecialchars($query) . "</pre>";

$result = mysqli_query($conexao, $query);

if(!$result) {
    echo "<p style='color: red;'>Erro: " . mysqli_error($conexao) . "</p>";
    exit;
}

$count = 0;
echo "<h3>Resultados:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Numero</th><th>Nome</th><th>Situação</th><th>dt_atend</th><th>Protocolo</th></tr>";

while($row = mysqli_fetch_assoc($result)) {
    $count++;
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['numero'] . "</td>";
    echo "<td>" . $row['nome'] . "</td>";
    echo "<td><strong>" . $row['situacao'] . "</strong> (tipo: " . gettype($row['situacao']) . ", length: " . strlen($row['situacao']) . ", ord: " . ord($row['situacao']) . ")</td>";
    echo "<td>" . $row['dt_atend'] . "</td>";
    echo "<td>" . $row['protocolo'] . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "<p><strong>Total de atendimentos retornados: $count</strong></p>";

// Verificar todos os atendimentos na tbatendimento
echo "<h3>Todos os Atendimentos na tbatendimento:</h3>";

$queryAll = "SELECT id, numero, nome, situacao, dt_atend, protocolo FROM tbatendimento ORDER BY id DESC LIMIT 10";
$resultAll = mysqli_query($conexao, $queryAll);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Numero</th><th>Nome</th><th>Situação</th><th>dt_atend</th><th>Protocolo</th></tr>";

while($row = mysqli_fetch_assoc($resultAll)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['numero'] . "</td>";
    echo "<td>" . $row['nome'] . "</td>";
    echo "<td><strong>" . htmlspecialchars($row['situacao']) . "</strong></td>";
    echo "<td>" . $row['dt_atend'] . "</td>";
    echo "<td>" . $row['protocolo'] . "</td>";
    echo "</tr>";
}

echo "</table>";

?>
