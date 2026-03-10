<?php
  require_once("../../includes/padrao.inc.php");

                       mysqli_next_result($conexao);

                      $ano = isset($_POST['ano']) ? intval($_POST['ano']) : intval(date('Y'));
                      $mes = isset($_POST['mes']) ? intval($_POST['mes']) : intval(date('m'));

                      if ($mes > 0) {
                          $dtInicio = sprintf('%04d-%02d-01', $ano, $mes);
                          $stmt = mysqli_prepare($conexao, "SELECT count(ta.id) as atendimentos, tu.nome FROM tbatendimento ta
                              INNER JOIN tbusuario tu ON tu.id = ta.id_atend
                              WHERE ta.id_atend > 0 AND ta.dt_atend BETWEEN ? AND LAST_DAY(?)
                              GROUP BY tu.nome ORDER BY atendimentos DESC");
                          mysqli_stmt_bind_param($stmt, 'ss', $dtInicio, $dtInicio);
                      } else {
                          $dtInicio = sprintf('%04d-01-01', $ano);
                          $dtFim = sprintf('%04d-12-31', $ano);
                          $stmt = mysqli_prepare($conexao, "SELECT count(ta.id) as atendimentos, tu.nome FROM tbatendimento ta
                              INNER JOIN tbusuario tu ON tu.id = ta.id_atend
                              WHERE ta.id_atend > 0 AND ta.dt_atend BETWEEN ? AND ?
                              GROUP BY tu.nome ORDER BY atendimentos DESC");
                          mysqli_stmt_bind_param($stmt, 'ss', $dtInicio, $dtFim);
                      }
                      mysqli_stmt_execute($stmt);
                      $usuarios = mysqli_stmt_get_result($stmt);

                      
       

                           $totalAtendimentos = 0;
                          while($ln = mysqli_fetch_assoc($usuarios)){                            
                             if ($totalAtendimentos==0){
                                $totalAtendimentos = $ln["atendimentos"];
                             }
                             //Altero o estilo da cor de acordo com a quantidade de atendimentos
                             $percentual = ($ln["atendimentos"] / $totalAtendimentos)*100;
                             if ($percentual>80){
                               $estilo = 'bg-success'; 
                             }else if ($percentual>40){
                                $estilo = 'bg-info';  
                             }else{
                                $estilo = 'bg-danger'; 
                             }
                             
                         
                                echo '
                                <h4 class="small font-weight-bold">'.$ln["nome"].' <span class="float-right">'.$ln["atendimentos"].'</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar '.$estilo.'" role="progressbar" style="width: '.$percentual.'%" aria-valuenow="'.$ln["atendimentos"].'" aria-valuemin="0" aria-valuemax="'.$totalAtendimentos.'"></div>
                                    </div>
                                 ';
                            
                          }
               

                    ?>