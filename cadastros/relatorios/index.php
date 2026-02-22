<?php
    require_once("../../includes/padrao.inc.php");
    
    $dtIni = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
    $dtFim = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

    $usuarios = mysqli_query(
        $conexao
        , "SELECT id, nome FROM tbusuario WHERE situacao = 'A'"
    );
?>
<script>
    $(document).ready(function() {
        // Formatação do Padrão Data //
        var behaviorDate = function (val) { return '00/00/0000'; }
        , options = {
            onKeyPress: function (val, e, field, options) {
                field.mask(behaviorDate.apply({}, arguments), options);
            }
        };

        $('#hstDtIni').mask(behaviorDate, options);
        $('#hstDtFim').mask(behaviorDate, options);
        // Formatação do Padrão Data //

        // Formatação do Padrão Telefone //
        var behavior = function (val) {
            return val.replace(/\D/g, '').length === 13 ? '+55 (00) 00000-0000' : '+55 (00) 0000-00009';
        },
        options = {
            onKeyPress: function (val, e, field, options) {
                field.mask(behavior.apply({}, arguments), options);
            }
        };

        $('#hstNumero').mask(behavior, options);
        // FIM Formatação do Padrão Telefone //

        // Ação executada no Click do Botão [Lupa] //
        $("#btnPesquisar").on("click", function() {
            // Muda a Ordenação para Crescente //
            $("#hstOrdenacao").val('ASC');

            pesquisa();
        });

        // Ação executada no Click do Botão [Últimos Atendimentos] //
        $("#ultimosAtendimentos").on("click", function() {
            // Muda a Ordenação para Decrescente //
            $("#hstOrdenacao").val('DESC');

            pesquisa();
        });

        function pesquisa(){
            var numer = $("#hstNumero").val();
            var atend = $("#hstAtendente").val();
            var dtIni = $("#hstDtIni").val();
            var dtFim = $("#hstDtFim").val();
            var situa = $("#hstSituacao").val();
            var orden = $("#hstOrdenacao").val();

            // Tratamento do Intervalo de Datas //
            if( dtIni === "" || dtFim === "" ){
                mostraDialogo("O intervalo [Entre Datas] é obrigatório!", "danger", 2500);
            }
            // Efetua a Pesquisa //
            else{
                $.ajax("cadastros/relatorios/listar.php?numero="+numer+"&atendente="+atend+"&dtIni="+dtIni+"&dtFim="+dtFim+"&situacao="+situa+"&ordenacao="+orden).done(function(data) {
                    $('#ListarConversas').html(data);
                });
            }
        }
    });
</script>

<div class="box-modal">
    <h2 class="title" style="width: 50%; float: left;">Histórico de Atendimentos</h2>
    <span style="float: right;">
        <input type="button" id="ultimosAtendimentos" class="uk-button uk-button-primary" value="Buscar Últimos Atendimentos">
    </span>



    <div class="panel-body">
		
        <table class="uk-table uk-table-striped" style="padding:0;margin:0">
            <tbody>
                <tr>
                    <form>
                        <input type="hidden" name="ordenacao" id="hstOrdenacao" value="ASC" />                        
                        <td width="20%" style="padding:2px;">                          
                                <input style="min-width:120px;width:100%;margin-top:5px;width:100%" id="hstNumero" type="text" placeholder="Inf. número com 9 Dígitos">                        
                        </td>
                        
                        <td style="padding:2px;width:120px;">                 
                               <input style="min-width:120px;width:100%;margin-top:5px;" type="text" name="hstDtIni" id="hstDtIni" placeholder="Data inicial" value="<?php echo $dtIni; ?>"> 
                        </td>
                        <td style="padding:2px;width:120px">                        
                            <div class='form-group'>                               
                                <input style="min-width:120px;width:100%;margin-top:5px;width:100%" type="text" name="hstDtFim" id="hstDtFim" placeholder="Data final" value="<?php echo $dtFim; ?>"> 
                             </div>                         
                                               
                        </td>
                        <td style="padding:2px;width:120px">                         
                                <select id="hstAtendente" class="EstiloSelect" style="min-width:120px;margin-top:5px;">
                                    <option value="0">Escolha um Atendente</option>
                                    <?php
                                        while( $results = mysqli_fetch_object($usuarios) ){
                                    ?>
                                    <option value="<?php echo $results->id; ?>"><?php echo $results->nome; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                       
                        </td>
                        <td style="padding:2px;width:120px">                                            
                                <select id="hstSituacao" class="EstiloSelect" style="min-width:120px;margin-top:5px;">
                                    <option value="">Escolha uma Situação</option>
                                    <option value="A">Em Atendimento</option>
                                    <option value="F">Finalizadas</option>
                                </select> 
                                              
                        </td>
                        <td width="20%" style="padding:2px;margin:0;text-align:left">
                            <button style="margin-top:12px;" type="button" id="btnPesquisar" class="uk-button"><i class="fas fa-search"></i></button>
                        </td>
                    </form>
                </tr>
            </tbody>
        </table>

        <table class="uk-table uk-table-striped">
            <thead>
                <tr>
                    <th>Telefone</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Atendente</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="ListarConversas"></tbody>
        </table>
    </div>
</div>