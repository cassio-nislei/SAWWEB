<!-- Transferir Atendimento -->
<div class="window menor comprido" id="modalTransferirAtendimento">
    <div class="" id="tabs">
        <ul>
            <li><a href="#tabs-1">Departamento</a></li>
            <li><a href="#tabs-2">Operador</a></li>
        </ul>
        <div id="tabs-1">
            <div style="height: 55%; overflow: scroll; overflow-x: hidden;">
                <div class="">
                    <div class="uk-padding-small" id="list-setores-encaminhar">
                        <?php
                            $departamentos = mysqli_query($conexao,"select * from tbdepartamentos");
                            
                            while ($listaDepartamento=mysqli_fetch_array($departamentos)){
                                echo '<a id="d'.$listaDepartamento['id'].'" href="javascript:;" onclick="return false;" class="uk-flex-middle uk-grid-small uk-grid lnkDepartamento" style="padding:0">
                                        <input type="hidden" id="dpto'.$listaDepartamento['id'].'" value="'.$listaDepartamento["departamento"].'" />
                                        <div class="uk-width-3-4">
                                            <p class="uk-margin-remove" style="font-size:14px;">'.$listaDepartamento["departamento"].'</p>
                                        </div>
                                        <div class="uk-width-1-5 uk-flex-first uk-first-column">
                                            <img src="img/no-setor.png" alt="Image" class="uk-border-circle" width="40">
                                        </div>
                                    </a>';
                            }
			            ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="tabs-2">
            <div style="height: 55%; overflow: scroll; overflow-x: hidden;">
                <div class="">
                    <div class="uk-padding-small" id="list-usuarios-online-encaminhar">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box-modal">
        <p>
            <b>Departamento:</b> <span id="dptoSelecionado"></span><br/>
            <b>Operador:</b> <span id="operSelecionado"></span><br/>
            <input type="hidden" id="idDepartamentoSelecionado" name="idDepartamentoSelecionado" />
            <input type="hidden" id="idOperadorSelecionado" name="idOperadorSelecionado" />
        </p>
        <p class="uk-text-right" style="margin-top:1rem">
            <button id="btnFecharTransferirAtendimento" class="uk-button uk-button-default uk-modal-close fechar" type="button">Fechar</button>
            <input id="btnTransferirAtendimento" class="uk-button uk-button-primary " type="submit" value="Transferir Atendimento">
        </p>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Seleciona o Departamento para Transferência //
        $('.lnkDepartamento').click(function() {
            var idDepartamento = ($(this).attr('id')).substring(1,$(this).attr('id').length);
            var nomeDepartamento = $("#dpto"+idDepartamento).val();

            $("#dptoSelecionado").html(nomeDepartamento);
            $("#idDepartamentoSelecionado").val(idDepartamento);

            // Busca os Usuários para o Departamento Selecionado //
            $.ajax("includes/listaUsuariosporSetor.php?idDepartamento="+idDepartamento).done(function(data) {
				$('#list-usuarios-online-encaminhar').html(data);
			});
            $("#ui-id-2").click();
        });

        // Transferindo o Atendimento //
        $('#btnTransferirAtendimento').click(function(e) {
            e.preventDefault();
            var numero = $("#s_numero").val();
            var id_atendimento = $("#s_id_atendimento").val();
            var id_canal = $("#s_id_canal").val();
            var nome = $("#s_nome").val();
            var id_departamento = $("#idDepartamentoSelecionado").val();
            var nomeDepartamento = $("#dptoSelecionado").text();
            var usuario = $("#idOperadorSelecionado").val();
            var nomeUsuario = $("#operSelecionado").text(); //Marcelo 07/12/2022
             //Desabilita o Botão
            $(this).prop("disabled", true);
            $(this).attr('value', 'Transferindo ...');  
            
           //Valido se possui id de atendimento
            if ($("#s_id_atendimento").val()==""){
                return false;
            }

            // Valida se escolheu um Departamento //
            if( id_departamento === "" ){
                mostraDialogo("Departamento ainda não Informado!", "danger", 2500);
                $('#btnTransferirAtendimento').attr('value', 'Transferir Atendimento'); 
                $('#btnTransferirAtendimento').prop("disabled", false);
                return false;
            }
            else{
                // Informa se que ainda não foi selecionado um Operador //
                if( usuario === "" ){
                    if( !confirm( "[Operador] não selecionado! Deseja realmente transferir apenas para o departamento [" +nomeDepartamento+ "]?" ) ){
                        $('#btnTransferirAtendimento').attr('value', 'Transferir Atendimento'); 
                        $('#btnTransferirAtendimento').prop("disabled", false);
                        return false;
                    }
                }

            //    alert(id_canal);

                $.post("atendimento/transferirAtendimento.php", {
                    numero: numero,
                    id_atendimento: id_atendimento,
                    id_canal: id_canal,
                    nome: nome,
                    departamento: id_departamento,
                    usuario: usuario,
                    nomeUsuario: nomeUsuario, //Marcelo 16/02/2022
                    paramim:'N'
                }, function(retorno) {
                  //  alert(retorno);
                    if (retorno == 3) {
                        mostraDialogo("O Atendimento já foi transferido!", "success", 2500);

                        $.ajax("atendimento/atendendo.php").done(function(data) {
                            $('#ListaEmAtendimento').html(data);
                        });
                        $.ajax("atendimento/pendentes.php").done(function(data) {
                            $('#ListaPendentes').html(data);
                        });
                    }
                    else if (retorno == 1) {
                        mostraDialogo("Atendimento Transferido", "success", 2500);
                        $('#btnTransferirAtendimento').attr('value', 'Transferir Atendimento'); 
                        $('#btnTransferirAtendimento').prop("disabled", false);
                        $.ajax("atendimento/atendendo.php").done(function(data) {
                            $('#ListaEmAtendimento').html(data);
                        });
                        $.ajax("atendimento/pendentes.php").done(function(data) {
                            $('#ListaPendentes').html(data);
                        });
                        $('#AtendimentoAberto').html('');
                    }
                    else {
                           mostraDialogo("Ocorreu algum erro ao tentar Transferir o Atendimento!", "danger", 2500);
                           $('#btnTransferirAtendimento').attr('value', 'Transferir Atendimento'); 
                           $('#btnTransferirAtendimento').prop("disabled", false);
                        }

                    // Fechando a Modal de Transferência de Atendimento //
                    $('#btnFecharTransferirAtendimento').click();
                });
            }
        });
    });
</script>