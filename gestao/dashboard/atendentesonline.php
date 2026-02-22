<?php
  require_once("../../includes/padrao.inc.php");

  mysqli_next_result($conexao);
  
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

  $usuarios = mysqli_query($conexao, $strSQL);
  
  if(!$usuarios){
    echo "<!-- Erro na query: " . mysqli_error($conexao) . " -->";
    echo '<div class="contact-list"><p>Erro ao carregar atendentes.</p></div>';
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

  if(mysqli_num_rows($usuarios) == 0){
    echo '<div class="contact-list"><p>Nenhum atendente disponível</p></div>';
  }
  else{
    echo '<div class="contact-list">';
    $encontrouOnline = false;
    
    while($ln = mysqli_fetch_assoc($usuarios)){
      // Validação se datetime_online não está vazio
      if(!empty($ln["datetime_online"])){
        // Calcula manualmente se está online
        try {
          $start_date = new DateTime($ln["datetime_online"]);
          $now = new DateTime();
          $since_start = $start_date->diff($now);
          
          $minutosTotais = (intval($since_start->days)*(24*60))
                              + (intval($since_start->h)*(60))
                              + (intval($since_start->i));
          
          // Se a última atividade foi dentro do tempo permitido, está online
          if($minutosTotais < $minutos_offline){
            $encontrouOnline = true;
            echo '
            <div class="contact">
              <img src="../../img/ico-contact.svg" alt="' . htmlspecialchars($ln["nome"]) . '">
              <p>' . htmlspecialchars($ln["nome"]) . '</p>
            </div>';
          }
        } catch (Exception $e) {
          // Se houver erro, apenas ignora esse usuário
          error_log("Erro ao processar data do usuário " . $ln["nome"] . ": " . $e->getMessage());
        }
      }
    }
    
    if(!$encontrouOnline){
      echo '<p style="padding: 15px; text-align: center; color: #999;">Nenhum atendente online no momento</p>';
    }
    
    echo '</div>';
  }
?>