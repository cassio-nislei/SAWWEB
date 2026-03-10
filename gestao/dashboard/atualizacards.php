<?php
   include("../../includes/conexao.php");

   $ano = isset($_POST['ano']) ? intval($_POST['ano']) : intval(date('Y'));
   $mes = isset($_POST['mes']) ? intval($_POST['mes']) : 0;

   $whereAno = "AND YEAR(dt_atend) = ?";
   $params = [];

   if ($mes > 0) {
       $sql = "SELECT
           (SELECT COUNT(situacao) FROM tbatendimento WHERE situacao = 'T' AND YEAR(dt_atend) = ? AND MONTH(dt_atend) = ?) as triagem,
           (SELECT COUNT(id) FROM tbatendimento WHERE situacao = 'P' AND YEAR(dt_atend) = ? AND MONTH(dt_atend) = ?) as pendentes,
           (SELECT COUNT(id) FROM tbatendimento WHERE situacao = 'A' AND YEAR(dt_atend) = ? AND MONTH(dt_atend) = ?) as atendendo,
           (SELECT COUNT(id) FROM tbatendimento WHERE situacao = 'F' AND finalizado_por != 'Transferencia' AND COALESCE(id_atend, 0) NOT IN(0) AND YEAR(dt_atend) = ? AND MONTH(dt_atend) = ?) as finalizados";
       $stmt = mysqli_prepare($conexao, $sql);
       mysqli_stmt_bind_param($stmt, 'iiiiiiii', $ano, $mes, $ano, $mes, $ano, $mes, $ano, $mes);
   } else {
       $sql = "SELECT
           (SELECT COUNT(situacao) FROM tbatendimento WHERE situacao = 'T' AND YEAR(dt_atend) = ?) as triagem,
           (SELECT COUNT(id) FROM tbatendimento WHERE situacao = 'P' AND YEAR(dt_atend) = ?) as pendentes,
           (SELECT COUNT(id) FROM tbatendimento WHERE situacao = 'A' AND YEAR(dt_atend) = ?) as atendendo,
           (SELECT COUNT(id) FROM tbatendimento WHERE situacao = 'F' AND finalizado_por != 'Transferencia' AND COALESCE(id_atend, 0) NOT IN(0) AND YEAR(dt_atend) = ?) as finalizados";
       $stmt = mysqli_prepare($conexao, $sql);
       mysqli_stmt_bind_param($stmt, 'iiii', $ano, $ano, $ano, $ano);
   }

   mysqli_stmt_execute($stmt);
   $result = mysqli_stmt_get_result($stmt);
   $card = mysqli_fetch_assoc($result);
   mysqli_stmt_close($stmt);

   header('Content-Type: application/json');
   echo json_encode($card);
?>
