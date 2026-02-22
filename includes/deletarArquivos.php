<?php
    // Requires //
    require_once("padrao.inc.php");

    // Declarção de Variáveis //
    $numero = $_POST["numero"];

    // Instancia a Function para deletar os Arquivos //
    deletarArquivos($conexao, $numero);