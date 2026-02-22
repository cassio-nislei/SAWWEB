<?php
// Incluir a conexão com banco de dados
require_once("../../../includes/padrao.inc.php");

// Receber o arquivo do formulário
$arquivo = $_FILES['arquivo'];
$idmsg   = $_POST["id_msg"];

//Deleto as mensagens caso exista importações anteriores para essa mensagem
$apagar = mysqli_query($conexao,"delete from tbenviomassanumero where id_msg = $idmsg ");

//var_dump($arquivo);

// Variáveis de validação
$primeira_linha = true;
$linhas_importadas = 0;
$linhas_nao_importadas = 0;
$usuarios_nao_importado = "";

// Verificar se é arquivo csv
if($arquivo['type'] == "text/csv"){

    // Ler os dados do arquivo
    $dados_arquivo = fopen($arquivo['tmp_name'], "r");

    // Percorrer os dados do arquivo
    while($linha = fgetcsv($dados_arquivo, 1000, ";")){

        // Como ignorar a primeira linha do Excel
        if($primeira_linha){
            $primeira_linha = false;
            continue;
        }

        // Usar array_walk_recursive para criar função recursiva com PHP
        array_walk_recursive($linha, 'converter');
        //var_dump($linha);

        // Criar a QUERY para salvar o usuário no banco de dados
        $numero  = mysqli_real_escape_string($conexao, ($linha[0] ?? "NULL"));
        $nome    = mysqli_real_escape_string($conexao, ($linha[3] ?? "NULL"));

       

      //  echo "Numero: $numero, Data: $dt, Hora: $dt, MSG: $msg";
      //  exit();
        $inseriu = mysqli_query($conexao,"INSERT INTO tbenviomassanumero (canal, id_msg, numero, nome, enviada) VALUES (1, $idmsg, '$numero', '$nome', false)") or die(mysqli_error($conexao));

    }

  echo "1";

}
// Criar função valor por referência, isto é, quando alter o valor dentro da função, vale para a variável fora da função.
function converter(&$dados_arquivo)
{
    // Converter dados de ISO-8859-1 para UTF-8
    $dados_arquivo = mb_convert_encoding($dados_arquivo, "UTF-8", "ISO-8859-1");
}