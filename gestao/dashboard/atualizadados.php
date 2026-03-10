<?php
   include("../../includes/conexao.php");

     $ano = isset($_POST['ano']) ? intval($_POST['ano']) : intval(date('Y'));

     $mesesArr = [];
     for ($m = 1; $m <= 12; $m++) {
         $stmt = mysqli_prepare($conexao, "SELECT COUNT(id) as total FROM tbatendimento WHERE MONTH(dt_atend) = ? AND YEAR(dt_atend) = ?");
         mysqli_stmt_bind_param($stmt, 'ii', $m, $ano);
         mysqli_stmt_execute($stmt);
         $result = mysqli_stmt_get_result($stmt);
         $row = mysqli_fetch_assoc($result);
         $mesesArr[] = $row['total'];
         mysqli_stmt_close($stmt);
     }

     echo implode(',', $mesesArr);

?>