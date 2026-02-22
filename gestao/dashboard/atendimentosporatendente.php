<?php
  require_once("../../includes/padrao.inc.php");

                       mysqli_next_result($conexao);

                      $usuarios = mysqli_query(
                          $conexao
                          , "select count(ta.id) as atendimentos, tu.nome from tbatendimento ta
                          inner join tbusuario tu on tu.id = ta.id_atend
                          where ta.id_atend > 0 and ta.dt_atend between DATE_FORMAT(NOW(), '%Y-%m-01') and  LAST_DAY(NOW())
                          group by tu.nome order by atendimentos desc "
                                    
                      );

                      
       

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