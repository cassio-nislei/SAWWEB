<?php
require_once("../../../includes/padrao.inc.php");

$id = $_POST['IdAgendamentos'];

// Utilizando declaração preparada para evitar injeção de SQL
$sql = "DELETE FROM tbmsgagendadasawcsv WHERE id = ?";
$stmt = mysqli_prepare($conexao, $sql);

// Vinculando os parâmetros
mysqli_stmt_bind_param($stmt, "i", $id);

// Executando a declaração preparada
mysqli_stmt_execute($stmt);

// Verificando se o DELETE foi bem-sucedido
if (mysqli_affected_rows($conexao) > 0) {
    echo "2"; // Sucesso
} else {
    echo "1"; // Falha
}

// Fechando a declaração preparada
mysqli_stmt_close($stmt);

// Fechando a conexão
mysqli_close($conexao);
?>