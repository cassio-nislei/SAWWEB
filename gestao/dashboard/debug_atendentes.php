<?php
  require_once("../../includes/padrao.inc.php");

  // Definições de Variáveis //
  $id = isset($_GET["idDepartamento"]) ? $_GET["idDepartamento"] : "";
  $condicao = ($id !== "") ? " AND tud.id_departamento = '$id'" : " GROUP BY tu.id";
  // FIM Definições de Variáveis //

  // Query para buscar usuários online
  $strSQL = "SELECT tu.id, tu.nome, tu.datetime_online
              FROM tbusuario tu
              INNER JOIN tbusuariodepartamento tud ON tud.id_usuario = tu.id
              WHERE tu.situacao NOT IN('I')"
              . $condicao;

  echo "<h2>Resultado da Query:</h2>";
  echo "<pre>$strSQL</pre>";
  
  $usuarios = mysqli_query($conexao, $strSQL);
  
  if(!$usuarios){
    echo "Erro na query: " . mysqli_error($conexao);
    exit;
  }

  // Recupera o Tempo para definir se o Usuário está Offline //
  $qryParametros = mysqli_query($conexao, "SELECT minutos_offline FROM tbparametros");
  $arrParametros = mysqli_fetch_assoc($qryParametros);
  
  if(!$arrParametros || empty($arrParametros["minutos_offline"])){
    $minutos_offline = 5; // valor padrão
  }
  else{
    $minutos_offline = $arrParametros["minutos_offline"];
  }

  echo "<h2>Minutos Offline Configurado: " . $minutos_offline . "</h2>";
  echo "<h2>Atendentes Encontrados:</h2>";

  if(mysqli_num_rows($usuarios) == 0){
    echo '<p>Nenhum atendente disponível</p>';
  }
  else{
    echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
    echo '<tr><th>ID</th><th>Nome</th><th>Última Data/Hora</th><th>Status</th><th>Minutos desde última atividade</th></tr>';
    
    while($ln = mysqli_fetch_assoc($usuarios)){
      $status = "Offline";
      $minutos_desde = "N/A";
      
      if(!empty($ln["datetime_online"])){
        try {
          $start_date = new DateTime($ln["datetime_online"]);
          $now = new DateTime();
          $since_start = $start_date->diff($now);
          
          $minutosTotais = (intval($since_start->days)*(24*60))
                              + (intval($since_start->h)*(60))
                              + (intval($since_start->i));
          
          $minutos_desde = $minutosTotais;
          
          if($minutosTotais < $minutos_offline){
            $status = "<span style='color: green;'>✓ Online</span>";
          }
          else{
            $status = "<span style='color: red;'>✗ Offline</span>";
          }
        } catch (Exception $e) {
          $status = "Erro ao processar data";
        }
      }
      
      echo '<tr>';
      echo '<td>' . $ln["id"] . '</td>';
      echo '<td>' . htmlspecialchars($ln["nome"]) . '</td>';
      echo '<td>' . $ln["datetime_online"] . '</td>';
      echo '<td>' . $status . '</td>';
      echo '<td>' . $minutos_desde . '</td>';
      echo '</tr>';
    }
    echo '</table>';
  }
?>
