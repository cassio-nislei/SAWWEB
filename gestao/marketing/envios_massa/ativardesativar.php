<?php
require_once("../../../includes/padrao.inc.php");

$id = intval($_POST['IdRespostaAutomatica']);
$marcado = ($_POST['ativo'] === 'true') ? 1 : 0; // Converte 'true'/'false' para 1 ou 0

$sql = "UPDATE tbenviomgsmassa SET ativo = $marcado WHERE id = $id";
$ativardesativar = mysqli_query($conexao, $sql);

if ($ativardesativar) {
    echo "2"; // Sucesso
} else {
    echo "1"; // Falha
}
?>