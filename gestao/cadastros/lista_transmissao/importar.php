<?php
// Incluir a conexão com banco de dados
require_once("../../../includes/padrao.inc.php");

// Receber o arquivo do formulário
$arquivo = $_FILES['arquivo'];
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
        $numero = mysqli_real_escape_string($conexao, ($linha[0] ?? "NULL"));
        $dt          = explode('/', ($linha[1] ?? "NULL")) ;
        $data_envio = $dt[2].'-'.$dt[1].'-'.$dt[0];
        $hr     = mysqli_real_escape_string($conexao, ($linha[2] ?? "NULL"));
        $msg    = mysqli_real_escape_string($conexao, ($linha[3] ?? "NULL"));

      //  echo "Numero: $numero, Data: $dt, Hora: $dt, MSG: $msg";
      //  exit();
        $inseriu = mysqli_query($conexao,"INSERT INTO tbmsgsenviadaspelosaw (numero, dt_inclusao, dt_programada, hora_programada, msg) VALUES ('$numero', current_date(), '$data_envio', '$hr', '$msg')") or die(mysqli_error($conexao));

    }

  echo "1";

}
// Criar função valor por referência, isto é, quando alter o valor dentro da função, vale para a variável fora da função.
function converter(&$dados_arquivo)
{
    // Converter dados de ISO-8859-1 para UTF-8
    $dados_arquivo = mb_convert_encoding($dados_arquivo, "UTF-8", "ISO-8859-1");
}
