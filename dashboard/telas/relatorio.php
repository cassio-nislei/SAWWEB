<?php
    require_once("../../includes/padrao.inc.php");
    
    if( !isset($_POST["mostrar"]) ){
        $mostrar = "";
        $mostra = "display: none;";
    }
    else{
        $mostrar = $_POST["mostrar"];

        if( $_POST["mostrar"]=='true' ){ $mostra = ""; }
        else{ $mostra = "display: none;"; }  
    }
?>

<!-- Inputs:hidden da Tela -->
<input type="hidden" id="dateDe" name="dateDe" value="<?php echo $de; ?>" />
<input type="hidden" id="dateAte" name="dateAte" value="<?php echo $ate; ?>" />
<!-- FIM Inputs:hidden da Tela -->

<div class=" container-fluid" id="divAtendimentoAberto" style="width: 74%; float: left; <?php echo $mostra; ?>">
    <div class="row">
        <div class="col-md-8">
            <table class='table table-hover table-sm'>
                <thead>
                    <tr>
                        <th scope='col' copspan='2'>
                            <b class='text-success'>ATENDIMENTOS [EM ANDAMENTO] POR OPERADOR</b>
                        </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th scope='col'>
                            <b>OPERADOR</b>
                        </th>
                        <th scope='col'>
                            <b>ATENDIMENTOS</b>
                        </th>
                    </tr>
                </thead>

                <?php
                    $emAndamentoTotais = 0;

                    $sql = "SELECT p.NOME as nome, count(1) AS atendimentos
                                        FROM tbatendimento c
                                            INNER JOIN tbusuario p ON c.id_atend = p.id WHERE c.situacao = 'A' GROUP BY p.nome";
                    $query = mysqli_query($conexao, $sql);

                    while ($dados = mysqli_fetch_array($query)) {
                        $emAndamentoTotais += intval($dados['atendimentos']);
                ?>

                <tbody>
                    <tr>
                        <th><?php echo $dados['nome']; ?></th>
                        <td><?php echo $dados['atendimentos']; ?></td>
                    </tr>
                </tbody>

                <?php } ?>

                <tr>
                    <th scope='col'></th>
                    <th scope='col'><b class='text-success'><?php echo $emAndamentoTotais; ?></b></th>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="card-header" id="divExportar" style="width: 25%; float: left; <?php echo $mostra; ?>">
    <div class="card-body" style="float: both;"><b>Exportar como:</b></div>
    <div class="card-body" style="float: left;">
        <a target="_blank" href="telas/export.php?de=<?php echo $de; ?>&ate=<?php echo $ate; ?>&type=csv"><button class="exportFile">CSV</button></a>
    </div>
    <div class="card-body" style="float: left;">
        <a target="_blank" href="telas/export.php?de=<?php echo $de; ?>&ate=<?php echo $ate; ?>&type=xlsx"><button class="exportFile">XLSX</button></a>
    </div>
    <div class="card-body" style="float: left;">
        <a target="_blank" href="telas/export.php?de=<?php echo $de; ?>&ate=<?php echo $ate; ?>&type=pdf"><button class="exportFile">PDF</button></a>
    </div>
</div>

<div class=" container-fluid" id="divAtendimentoFinalizado" style="width: 74%; float: left; <?php echo $mostra; ?>">
    <div class="row">
        <div class="col-md-8">
            <table class='table table-hover table-sm'>
                <thead>
                    <tr>
                        <th scope='col' copspan='4'>
                            <b class='text-success'>ATENDIMENTOS [FINALIZADOS] POR OPERADOR</b>
                        </th>
                    </tr>
                </thead>

                <thead>
                    <tr>
                        <th scope='col'><b>OPERADOR</b></th>
                        <th scope='col'><b>ATENDIDOS</b></th>
                        <th scope='col'><b>TRANSFERIDOS</b></th>
                        <th scope='col'><b>TOTAIS</b></th>
                    </tr>
                </thead>

                <?php
                    $atendidosTotais = 0;
                    $transferidosTotais = 0;
                    $totaisTotais = 0;

                    $sql = "SELECT p.NOME as nome , count(1) AS atendimentos
                                , (SELECT count(1) FROM tbatendimento tba WHERE tba.id_atend = c.id_atend AND tba.dt_atend BETWEEN '" . $de . "' AND '" . $ate . "' AND tba.finalizado_por = 'Transferencia') AS transferencias 
                                FROM tbatendimento c
                                    INNER JOIN tbusuario p ON c.id_atend = p.id 
                                        WHERE c.situacao = 'F' AND c.dt_atend BETWEEN '" . $de . "' AND '" . $ate . "'
                                            GROUP BY p.id";
                    $query = mysqli_query($conexao, $sql);

                    while ($dados = mysqli_fetch_array($query)) {
                        $atendidosTotais += (intval($dados['atendimentos'])-intval($dados['transferencias']));
                        $transferidosTotais += intval($dados['transferencias']);
                        $totaisTotais += intval($dados['atendimentos']);
                ?>

                <tbody>
                    <tr>
                        <th><?php echo $dados['nome']; ?></th>
                        <td><?php echo intval($dados['atendimentos'])-intval($dados['transferencias']); ?></td>
                        <td><?php echo $dados['transferencias']; ?></td>
                        <td><?php echo $dados['atendimentos']; ?></td>
                    </tr>
                </tbody>

                <?php } ?>

                <tr>
                    <th scope='col'></th>
                    <th scope='col'><b class='text-success'><?php echo $atendidosTotais; ?></b></th>
                    <th scope='col'><b class='text-success'><?php echo $transferidosTotais; ?></b></th>
                    <th scope='col'><b class='text-success'><?php echo $totaisTotais; ?></b></th>
                </tr>
            </table>
        </div>
    </div>
</div>