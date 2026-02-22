
  <style>
    .contact-list {
          display: flex;
        /* flex-direction: column; */
          flex-direction: row;
          flex-wrap: wrap; /* Permite a quebra de linha quando necessário */
          align-items: flex-start; /*center;*/
          padding: 20px;
      }

      .contact {
          display: flex;
          align-items: flex-start; /*center;*/
          background-color: #ffffff;
          color:#000066;
          width: 300px; /* Largura máxima dos elementos .contact */
        /* width: 300px; */
          border-radius: 5px;
          margin-right: 10px; /* Espaçamento entre os elementos */
          margin-bottom: 10px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .contact img {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          margin: 10px;
      }

      .contact p {
          font-size: 16px;
          margin: 0;
          padding: 10px;
      }
  </style>
        <!-- Begin Page Content -->
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Situação dos Atendimentos
            <select name="ano" id="ano" style="padding: 5px 10px; margin: 0 10px; font-size: 14px;">
                            <?php
                           
                              $ano = mysqli_query($conexao,"select distinct YEAR(dt_atend) as ano from tbatendimento order by dt_atend DESC");
                              while ($anos = mysqli_fetch_assoc($ano)){
                                if ($anos['ano'] == date("Y")){
                                  $seleciona = 'selected';
                                }else{
                                  $seleciona = '';
                                }
                                if (!empty($anos['ano'])){
                                  echo '<option value="'.$anos['ano'].'" '.$seleciona.' >'.$anos['ano'].'</option>';
                                }
                               
                              }
                            
                            ?>
                </select>   
                <select name="mes" id="mes" style="padding: 5px 10px; margin: 0 10px; font-size: 14px;">
                   <option value="0" selected>Todos os Meses</option>
                   <option value="1" >Janeiro</option>
                   <option value="2" >Fevereiro</option>
                   <option value="3" >Março</option>
                   <option value="4" >Abril</option>
                   <option value="5" >Maio</option>
                   <option value="6" >Junho</option>
                   <option value="7" >Julho</option>
                   <option value="8" >Agosto</option>
                   <option value="9" >Setembro</option>
                   <option value="10" >Outubro</option>
                   <option value="11" >Novembro</option>
                   <option value="12" >Dezembro</option>
                </select>    
          <?php

            $cards = mysqli_query($conexao,"CALL sprDashBoardAnoAtual();");
            $card = mysqli_fetch_assoc($cards);

          ?>
        </h1>
      
  </div>

  <!-- Content Row -->
  <div class="row">

      <!-- Earnings (Monthly) Card Example -->
      <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100 py-2">
              <div class="card-body">
                  <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                              SEM SETOR</div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $card["triagem"]; ?></div>
                      </div>
                      <div class="col-auto">
                          <i class="fas fa-calendar fa-2x text-gray-300"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Earnings (Monthly) Card Example -->
      <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-success shadow h-100 py-2">
              <div class="card-body">
                  <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            AGUARDANDO ATENDENTE</div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $card["pendentes"]; ?></div>
                      </div>
                      <div class="col-auto">
                          <i class="fas fa-clock fa-2x text-gray-300"></i>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Earnings (Monthly) Card Example -->
      <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-info shadow h-100 py-2">
              <div class="card-body">
                  <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-info text-uppercase mb-1">EM ATENDIMENTO
                          </div>
                          <div class="row no-gutters align-items-center">
                              <div class="col-auto">
                                  <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $card["atendendo"]; ?></div>
                              </div>
                              <div class="col">
                                  <div class="progress progress-sm mr-2">
                                      <div class="progress-bar bg-info" role="progressbar"
                                          style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                          aria-valuemax="100"></div>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="col-auto">
                         <i class="fas fa-comments fa-2x text-gray-300"></i>                          
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- Pending Requests Card Example -->
      <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100 py-2">
              <div class="card-body">
                  <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                          <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                              ENCERRADOS</div>
                          <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $card["finalizados"]; ?></div>
                      </div>
                      <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i> 
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>





<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Dashboard de Atendimentos</h1>

<div class="row">

<div class="col-xl-12 col-lg-12">
    <!-- Area Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Atendentes Online</h6>
        </div>
          <div id="AtendentesOnline">
          </div>
      </div>
   </div>
</div>

<div class="row">
<div class="col-xl-12 col-lg-12">
    <!-- Area Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Atendentes Inativos</h6>
        </div>
          <div id="AtendentesInativos">
          </div>
      </div>
   </div>
</div>

<!-- Content Row -->
<div class="row">

    <div class="col-xl-8 col-lg-7">
        <!-- Area Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Atendimentos realizados este ano</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>  
            </div>
        </div>

    </div>

          <!-- Donut Chart -->
        <div class="col-xl-4 col-lg-5">         

               <!-- Card Body -->
                <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Chamados</h6>
                                </div>
                                <div class="card-body" id="graficoAtendimentosPorAtendente">
                                    
                                    <h4 class="small font-weight-bold">Weslen <span class="float-right">10</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Wanderson <span class="float-right">8</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Natan <span class="float-right">5</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Mateus <span class="float-right">0</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>         

        </div>
   </div>


   <!-- Content Row -->
<div class="row">
  <div class="col-xl-12 col-lg-12">
        <!-- Bar Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Tempo Médio de atendimento por dia</h6>
            </div>
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="myBarChart"></canvas>
                </div>              
            </div>
        </div>
      </div>
</div>
                      

<!-- /.container-fluid -->


  <!-- Page level plugins -->
  <script src="vendor/chart.js/Chart.min.js"></script>

  <script>
    //carrego e atualizo os dados dos Atendentes online
    function atualizarRelatorio() {
      // Faça uma requisição POST para o arquivo atualizarelatorio.php
      $.post('dashboard/atendentesonline.php', function(data) {
        console.log('Função atualizarRelatorio chamada.');
        console.log('Dados retornados:', data);
        $("#AtendentesOnline").html(data);
        
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Erro ao atualizar o relatório:', textStatus, errorThrown);
        console.error('Status:', jqXHR.status);
        console.error('Response:', jqXHR.responseText);
      });
    }

    //carrego e atualizo os dados dos Atendentes inativos
    function atualizarRelatorioInativos() {
      $.post('dashboard/atendentesinativos.php', function(data) {
        console.log('Função atualizarRelatorioInativos chamada.');
        console.log('Dados retornados:', data);
        $("#AtendentesInativos").html(data);
        
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Erro ao atualizar o relatório de inativos:', textStatus, errorThrown);
        console.error('Status:', jqXHR.status);
        console.error('Response:', jqXHR.responseText);
      });
    }

    // Chame a função a cada 30 segundos (30 * 1000 milissegundos)
    setInterval(atualizarRelatorio, 30000);
    setInterval(atualizarRelatorioInativos, 30000);
    
    // Chama a primeira vez quando a página carrega
    $(document).ready(function() {
      // Define o mês atual como selecionado
      var mesAtual = new Date().getMonth() + 1; // getMonth retorna 0-11, então +1
      $("#mes").val(mesAtual);
      
      atualizarRelatorio();
      atualizarRelatorioInativos();
      updateTimestampUser(); // Atualiza o timestamp do usuário logado
    });
  
    </script>

    <script>
    // Atualiza o timestamp do usuário a cada 5 minutos para manter online
    var idTimestampUsuario = setInterval(function() { 
        updateTimestampUser();
    }, 300000); // 5 minutos
    
    function updateTimestampUser(){
        $.ajax("../../cadastros/usuarios/gravaTimestamp.php").done(
            function(response) {
                console.log('Timestamp do usuário atualizado');
            }
        ).fail(function() {
            console.log('Erro ao atualizar timestamp');
        });
    }
    </script>

    <script>
    //carrego e atualizo os dados dos Atendentes online
    function atualizarAtendimentosPorAtendente() {
      // Faça uma requisição POST para o arquivo atualizarelatorio.php
      $.post('dashboard/atendimentosporatendente.php', function(data) {
        $("#graficoAtendimentosPorAtendente").html(data);
        
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        console.error('Erro ao atualizar o relatório:', textStatus, errorThrown);
      });
    }

    // Chame a função a cada 30 segundos (30 * 1000 milissegundos)
    setInterval(atualizarAtendimentosPorAtendente, 30000);
    
    atualizarAtendimentosPorAtendente();
  
    </script>



  <script> 
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

function atualizarGraficoAnual() {
var listaDadosMeses;
  
    $.post( "dashboard/atualizadados.php", function( data ) {
     // alert(data);
      listaDadosMeses = data;
      let array = [];
      array = listaDadosMeses.split(','); 
  

// Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
    datasets: [{
      label: "Total de Atendimentos",
      lineTension: 0.3,
      backgroundColor: "rgba(78, 115, 223, 0.05)",
      borderColor: "rgba(78, 115, 223, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: array,
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: true,
          drawBorder: true
        },
        ticks: {
          maxTicksLimit: 10,
          autoSkip: false,

        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 10,
          padding: 10,
          autoSkip: false,
          // Include a dollar sign in the ticks
          callback: function(value, index, values) {
            return  number_format(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
        }
      }
    }
  }
});
});
}

 // Chame a função a cada 30 segundos (30 * 1000 milissegundos)
 setInterval(atualizarGraficoAnual, 420000); //Seta um timer para atualizar o Gráfico de tempos em tempos
    
 atualizarGraficoAnual(); //Carrega o Gráfico na Inicialização

 </script>

 <script>
   //Gráfico de Barras Tempo Médio de atendimentos

   function atualizarGraficoMensal() {
   $.post( "dashboard/atualizagraficotempomedio.php", function( data ) {
   // alert(data);
      listaDadosMeses = data;
      let array = [];
      array = listaDadosMeses.split(';'); 
      let descricao = [];
      descricao = array[0].split('|');
      let quantidades = [];
      quantidades = array[1].split('|');


      console.log(data);


// Restante do seu código Chart.js
Chart.defaults.global.defaultFontFamily = 'Nunito, -apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';



var ctx = document.getElementById("myBarChart");
var myBarChart = new Chart(ctx, {
    type: 'bar', // Mantido como gráfico de barras
    data: {
        labels: descricao,
        datasets: [{
            label: "Tempo Médio de Atendimento",
            backgroundColor: "#4e73df",
            borderColor: "#4e73df",
            data: quantidades.map(function(time) {
                // Converta o tempo para minutos
                var parts = time.split(':');
                return parseInt(parts[0]) * 60 + parseInt(parts[1]);
            }),
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                type: 'category', // Configurado para escala de categoria
                gridLines: {
                    display: false,
                },
                ticks: {
                    maxRotation: 0, // Gire os rótulos do eixo X
                    autoSkip: true,
                    maxTicksLimit: 31, // Ajuste conforme necessário
                },
            }],
            yAxes: [{
                ticks: {
                    callback: function(value) {
                        // Converta minutos de volta para o formato de tempo
                        var hours = Math.floor(value / 60);
                        var minutes = value % 60;
                        return hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
                    },
                },
                gridLines: {
                    // ... outras configurações ...
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, chart) {
                    // Converta minutos para o formato de tempo
                    var hours = Math.floor(tooltipItem.yLabel / 60);
                    var minutes = tooltipItem.yLabel % 60;
                    return 'Tempo Médio: ' + hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0');
                }
            }
        },
    }
});

});

   }

     // Chame a função a cada 30 segundos (30 * 1000 milissegundos)
     setInterval(atualizarGraficoMensal, 60000);
    
     atualizarGraficoMensal();



 </script>



