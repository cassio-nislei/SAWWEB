<?php
   include("../../includes/conexao.php");

      $ano = isset($_POST['ano']) ? intval($_POST['ano']) : intval(date('Y'));
      $mes = isset($_POST['mes']) ? intval($_POST['mes']) : intval(date('m'));
      if ($mes <= 0) $mes = intval(date('m'));

      $dtRef = sprintf('%04d-%02d-01', $ano, $mes);

      $sql = "SELECT
          DAY(a.data_atendimento) AS data_atendimento,
          TIME_FORMAT(
              COALESCE(SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, CONCAT(t.dt_atend, ' ', t.hr_atend), t.dt_fim))), '00:00:00'),'%H:%i'
          ) AS tempo_medio_atendimento
      FROM (
          SELECT DISTINCT DATE(dt_atend) AS data_atendimento
          FROM tbatendimento
          WHERE MONTH(dt_atend) = ? AND YEAR(dt_atend) = ?
          UNION
          SELECT DATE_FORMAT(? - INTERVAL (DAY(?)-1) DAY + INTERVAL n DAY, '%Y-%m-%d') AS data_atendimento
          FROM (
              SELECT (t4.n + t2.n * 10 + t1.n * 100) AS n
              FROM (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1
              CROSS JOIN (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2
              CROSS JOIN (SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4
              WHERE (t4.n + t2.n * 10 + t1.n * 100) < DAY(LAST_DAY(?))
          ) numbers
      ) a
      LEFT JOIN tbatendimento t ON a.data_atendimento = DATE(t.dt_atend)
      GROUP BY a.data_atendimento
      ORDER BY a.data_atendimento";

      $stmt = mysqli_prepare($conexao, $sql);
      mysqli_stmt_bind_param($stmt, 'iisss', $mes, $ano, $dtRef, $dtRef, $dtRef);
      mysqli_stmt_execute($stmt);
      $prod = mysqli_stmt_get_result($stmt);

      $listaDescricao = '';
      $listaQTD       = '';
     while ($produtos = mysqli_fetch_assoc($prod) ){
        $listaDescricao .= $produtos["data_atendimento"] . '|';
        $listaQTD       .= $produtos["tempo_medio_atendimento"] . '|';
     }
     $listaDescricao = substr($listaDescricao, 0, -1) . ';';
     $listaQTD       = substr($listaQTD, 0, -1);

     echo $listaDescricao . $listaQTD;

?>