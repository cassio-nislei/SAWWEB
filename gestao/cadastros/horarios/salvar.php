<?php
  require_once("../../../includes/padrao.inc.php");

  //Domingo
  $hora_inicio_expediente_domingo     = $_POST['hora_inicio_expediente_domingo'];
  $hora_fim_expediente_domingo        = $_POST['hora_fim_expediente_domingo'];
  isset($_POST["fechado_domingo"]) ? $fechado_domingo = 1 : $fechado_domingo = 0;
  //Segunda
  $hora_inicio_expediente_segunda     = $_POST['hora_inicio_expediente_segunda'];
  $hora_fim_expediente_segunda        = $_POST['hora_fim_expediente_segunda'];

  isset($_POST["fechado_segunda"]) ? $fechado_segunda = 1 : $fechado_segunda = 0;
  //Terça
  $hora_inicio_expediente_terca     = $_POST['hora_inicio_expediente_terca'];
  $hora_fim_expediente_terca        = $_POST['hora_fim_expediente_terca'];
  isset($_POST["fechado_terca"]) ? $fechado_terca = 1 : $fechado_terca = 0;
  //Quarta
  $hora_inicio_expediente_quarta     = $_POST['hora_inicio_expediente_quarta'];
  $hora_fim_expediente_quarta        = $_POST['hora_fim_expediente_quarta'];
  isset($_POST["fechado_quarta"]) ? $fechado_quarta = 1 : $fechado_quarta = 0;
  //Quinta
  $hora_inicio_expediente_quinta     = $_POST['hora_inicio_expediente_quinta'];
  $hora_fim_expediente_quinta        = $_POST['hora_fim_expediente_quinta'];
  isset($_POST["fechado_quinta"]) ? $fechado_quinta = 1 : $fechado_quinta = 0;
  //Sexta
  $hora_inicio_expediente_sexta     = $_POST['hora_inicio_expediente_sexta'];
  $hora_fim_expediente_sexta        = $_POST['hora_fim_expediente_sexta'];
  isset($_POST["fechado_sexta"]) ? $fechado_sexta = 1 : $fechado_sexta = 0;
  //Sabado
  $hora_inicio_expediente_sabado     = $_POST['hora_inicio_expediente_sabado'];
  $hora_fim_expediente_sabado        = $_POST['hora_fim_expediente_sabado'];
  isset($_POST["fechado_sabado"]) ? $fechado_sabado = 1 : $fechado_sabado = 0;

  $atualizar1 = mysqli_query($conexao, "
  UPDATE tbhorarios 
  SET 
      hr_inicio = '$hora_inicio_expediente_domingo', 
      hr_fim = '$hora_fim_expediente_domingo', 
      fechado = ".$fechado_domingo." 
  WHERE dia_semana = 6
") or die("Erro na atualização 1: " . mysqli_error($conexao));

$atualizar2 = mysqli_query($conexao, "
    UPDATE tbhorarios 
    SET 
        hr_inicio = '$hora_inicio_expediente_segunda', 
        hr_fim = '$hora_fim_expediente_segunda', 
        fechado = ".$fechado_segunda."  
    WHERE dia_semana = 0
    ") or die("Erro na atualização 1: " . mysqli_error($conexao));



$atualizar3 = mysqli_query($conexao, "
  UPDATE tbhorarios 
  SET 
      hr_inicio = '$hora_inicio_expediente_terca', 
      hr_fim = '$hora_fim_expediente_terca', 
      fechado = ".$fechado_terca."  
  WHERE dia_semana = 1
") or die("Erro na atualização 3: " . mysqli_error($conexao));

$atualizar4 = mysqli_query($conexao, "
  UPDATE tbhorarios 
  SET 
      hr_inicio = '$hora_inicio_expediente_quarta', 
      hr_fim = '$hora_fim_expediente_quarta', 
      fechado = ".$fechado_quarta."  
  WHERE dia_semana = 2
") or die("Erro na atualização 4: " . mysqli_error($conexao));

$atualizar5 = mysqli_query($conexao, "
  UPDATE tbhorarios 
  SET 
      hr_inicio = '$hora_inicio_expediente_quinta', 
      hr_fim = '$hora_fim_expediente_quinta', 
      fechado = ".$fechado_quinta."  
  WHERE dia_semana = 3
") or die("Erro na atualização 5: " . mysqli_error($conexao));

$atualizar6 = mysqli_query($conexao, "
  UPDATE tbhorarios 
  SET 
      hr_inicio = '$hora_inicio_expediente_sexta', 
      hr_fim = '$hora_fim_expediente_sexta', 
      fechado = ".$fechado_sexta."  
  WHERE dia_semana = 4
") or die("Erro na atualização 6: " . mysqli_error($conexao));

$atualizar7 = mysqli_query($conexao, "
  UPDATE tbhorarios 
  SET 
      hr_inicio = '$hora_inicio_expediente_sabado', 
      hr_fim = '$hora_fim_expediente_sabado', 
      fechado = ".$fechado_sabado."
  WHERE dia_semana = 5
") or die("Erro na atualização 7: " . mysqli_error($conexao));

// Retorno = 1 Sucesso na Inclusão
if ($atualizar1 && $atualizar2 && $atualizar3 && $atualizar4 && $atualizar5 && $atualizar6 && $atualizar7) {
    echo 1;
} else {
    echo 2;
}
?>