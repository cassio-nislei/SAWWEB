<?php
  $usuarioBD = 'papion07';
  $senhaBD   = 'Lavr4s2022';
  $servidorBD= 'mysql.papion.com.br';
  //Faz a conexão com o Banco de dados MYSQL
  $conexao = mysqli_connect($servidorBD,$usuarioBD,$senhaBD,'papion07') or die("Não foi possivel conectar, aguarde um momento");
  mysqli_set_charset($conexao,"utf8mb4");
?>