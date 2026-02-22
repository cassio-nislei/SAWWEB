<?php
  $usuarioBD = getenv('DB_USER') ?: 'root';
  $senhaBD   = getenv('DB_PASS') ?: 'Ncm@647534';
  $servidorBD= getenv('DB_HOST') ?: '104.234.173.105';
  $bancoBD   = getenv('DB_NAME') ?: 'saw_quality';
  //Faz a conexão com o Banco de dados MYSQL
  @$conexao = mysqli_connect($servidorBD,$usuarioBD,$senhaBD,$bancoBD) or die("Não foi possivel conectar, aguarde um momento");
  mysqli_set_charset($conexao,"utf8mb4");
  
  // Define timezone do MySQL para Brasília (sincroniza com PHP)
  mysqli_query($conexao, "SET time_zone = 'America/Sao_Paulo'");