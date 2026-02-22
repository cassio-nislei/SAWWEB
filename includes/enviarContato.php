<?php
    // Requires //
    require_once("padrao.inc.php");

    // Definição de Variáveis //
        $strRetorno = 9;
        $idAtend = $_POST["idAtend"];
        $numero = $_POST["numero"];
        $nome = $_POST["nome"];
        $idCanal = isset($_POST["id_canal"]) ? $_POST["id_canal"] : "";
        $contatoNome = $_POST["contatoNome"];
        $contatoNumero = $_POST["contatoNumero"];
    // FIM Definição de Variáveis //

    // Insere o Registro na Tabela 'tbanexacontato' //
        $newSequence = newSequence($conexao, $idAtend, $numero, $idCanal);
        
        $strInsert = "INSERT INTO tbanexacontato(id, seq, numero, numero_contato, nome_contato) 
                        VALUES('".$idAtend."', '".$newSequence."', '".$numero."', '".$contatoNumero."', '".$contatoNome."')";
        $qryInsert = mysqli_query($conexao, $strInsert) or die(mysqli_error($conexao));
    // FIM Insere o Registro na Tabela 'tbanexacontato' //

    // Se encontrar registro no BD //
    if( $qryInsert ){ $strRetorno = 1; }

    // Retorno JSON //
    echo json_encode($strRetorno);