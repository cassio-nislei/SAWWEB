<?php
    require_once("../../includes/padrao.inc.php");
    $de   = date("Y-m-d", strtotime(str_replace('/', '-', $_POST["de"])));
    $ate  = date("Y-m-d", strtotime(str_replace('/', '-', $_POST["ate"])));

    // Triagem //
    $sqlTriagem = "SELECT COUNT(situacao) AS qtde FROM tbatendimento WHERE situacao = 'T'";
    $consultaTriagem = mysqli_query($conexao, $sqlTriagem);
    $dadosTriagem = mysqli_fetch_array($consultaTriagem);
    $triagem = $dadosTriagem['qtde'];

    // Aguardando Atendimento no Setor //
    $sqlPendente = "SELECT COUNT(id) AS qtde FROM tbatendimento WHERE situacao = 'P'";
    $consultaPendente = mysqli_query($conexao, $sqlPendente);
    $dadosPendente = mysqli_fetch_array($consultaPendente);
    $aguardando = $dadosPendente['qtde'];

    // Com Atendimento em Aberto //
    $sqlEmAtendimento = "SELECT COUNT(id) AS qtde FROM tbatendimento WHERE situacao = 'A'";
    $consultaEmAtendimento = mysqli_query($conexao, $sqlEmAtendimento);
    $dadosEmAtendimento = mysqli_fetch_array($consultaEmAtendimento);
    $atendimento = $dadosEmAtendimento['qtde'];

    // Total de chamadas Transferidos no Período //
    $sqlTransferencia = "SELECT COUNT(id) AS qtde FROM tbatendimento WHERE finalizado_por = 'Transferencia' AND dt_atend BETWEEN '".$de."' AND '".$ate."' AND COALESCE(id_atend, 0) NOT IN(0)";
    $consultaTransferencia = mysqli_query($conexao, $sqlTransferencia);
    $dadosTransferencia = mysqli_fetch_array($consultaTransferencia);
    $transferidas = $dadosTransferencia['qtde'];

    // Total de chamadas encerradas no Período //
    $sqlFinalizado = "SELECT COUNT(id) AS qtde FROM tbatendimento WHERE situacao = 'F' and finalizado_por != 'Transferencia' AND dt_atend BETWEEN '".$de."' AND '".$ate."' AND COALESCE(id_atend, 0) NOT IN(0)";
    $consultaFinalizado = mysqli_query($conexao, $sqlFinalizado);
    $dadosFinalizado = mysqli_fetch_array($consultaFinalizado);
    $finalizadas = $dadosFinalizado['qtde'];

    // Total de chamadas do Período //
    $sqlTotal = "SELECT COUNT(id) AS qtde FROM tbatendimento WHERE situacao <> 'T' AND dt_atend BETWEEN '".$de."' AND '".$ate."' AND COALESCE(id_atend, 0) NOT IN(0)";
    $consultaTotal = mysqli_query($conexao, $sqlTotal);
    $dadosTotal = mysqli_fetch_array($consultaTotal);
    $total = $dadosTotal['qtde'];
?>

<div class="container-fluid painelTotal">
    <div class="row">
        <div class="col-md-2">
            <div class="card text-white bg-warning mb-3 painel" style="max-width: 18rem;">
                <div class="card-header">TRIAGEM</div>
                <div class="card-body ">
                    <h5 class="card-title painelh5">
                        <?php echo $triagem; ?>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-black bg-secundary mb-3 painel" style="max-width: 18rem;">
                <div class="card-header">AGUARDANDO</div>
                <div class="card-body ">
                    <h5 class="card-title painelh5">
                        <?php echo $aguardando; ?>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card  text-white bg-success mb-3 painel" style="max-width: 18rem;">
                <div class="card-header">EM ANDAMENTO</div>
                <div class="card-body ">
                    <h5 class="card-title painelh5">
                        <?php echo $atendimento; ?>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card  text-white bg-primary mb-3 painel" style="max-width: 18rem;">
                <div class="card-header">TRANSFERIDOS</div>
                <div class="card-body ">
                    <h5 class="card-title painelh5">
                        <?php echo $transferidas; ?>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-white bg-dark mb-3 painel" style="max-width: 18rem;">
                <div class="card-header">FINALIZADOS</div>
                <div class="card-body ">
                    <h5 class="card-title painelh5">
                        <?php echo $finalizadas; ?>
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-white bg-danger mb-3 painel" style="max-width: 18rem;">
                <div class="card-header">TOTAIS</div>
                <div class="card-body ">
                    <h5 class="card-title painelh5">
                        <?php echo  $total; ?>
                    </h5>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'relatorio.php'; ?>