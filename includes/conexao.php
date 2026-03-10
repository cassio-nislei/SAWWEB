<?php
  $usuarioBD  = "root";
  $senhaBD    = "Ncm@647534";
  $servidorBD = "104.234.173.105";
  $bancoBD    = "saw_quality";

  //Faz a conexão com o Banco de dados MYSQL
  try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conexao = mysqli_init();
    $conexao->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
    $conexao->real_connect($servidorBD, $usuarioBD, $senhaBD, $bancoBD);
    mysqli_set_charset($conexao,"utf8mb4");
    
    // Define timezone do MySQL para Brasília (sincroniza com PHP)
    mysqli_query($conexao, "SET time_zone = 'America/Sao_Paulo'");
  } catch (mysqli_sql_exception $e) {
    die("Não foi possivel conectar, aguarde um momento");
  }