<div class="window menor" id="modalFinalizarAtendimento">
    <div class="box-modal">
        <form method="post" style="margin-top: 15px;">
            <h2 class="title">Finalizar Atendimento</h2>
            <p>
                Deseja realmente finalizar este atendimento?
                <span id="pNaoEnvia" style="display: none;">
                    <input class="uk-checkbox" type="checkbox" id="enviaMensagemFinal" name="enviaMensagemFinal" value="1" checked />
                    Enviar mensagem de Finalização do Atendimento?
                </span>
                <br />
                <span>
                    <input class="uk-checkbox" type="checkbox" id="enviaMsgInatividade" name="enviaMsgInatividade" value="1" />
                    Enviar mensagem de Desconexão por Inatividade?
                </span>
                <span id="spanMsgInatividade" style="display: none; width: 100%;">
                    <textarea id="msgInatividade" name="msgInatividade" style="width: 100%; height: 100px;"></textarea>
                </span>
                <br />
                <span>
                    <input class="uk-checkbox" type="checkbox" id="VinculaObs" name="VinculaObs" value="1" />
                     Adicionar uma Observação, para ficar vinculada aos próximos chamados?
                </span>
                <span id="spanMsgObs" style="display: none; width: 100%;">
                    <textarea id="msgObs" name="msgObs" style="width: 100%; height: 100px;"></textarea>
                </span>
                <br />
                <div class="uk-width-1-1@m">
                    <div class="uk-form-label">Escolha a TAG </div>     
                    <select class="js-example-basic-multiple pesqEtiquetas" name="id_etiqueta[]" multiple="multiple" id="id_etiqueta" style="width:90%">
                        <?php
                    //Crio a lista de etiquetas e defino as cores a exibir
                        $query = mysqli_query($conexao, "SELECT * FROM tbetiquetas");                       
                        while ($ListarEtiquetas = mysqli_fetch_array($query)){       
                        echo  '<option value="'.$ListarEtiquetas["id"].'" data-color="'.$ListarEtiquetas["cor"].'" >'.$ListarEtiquetas["descricao"].'</otpion>';                     
                        }
                        ?>
                    </select>
                    </div>
            </p>
        
            <p class="uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close fechar" type="button"> Cancelar</button>
                <input id="btnFinalizarAtendimento" class="uk-button uk-button-success fechar" type="button" value="Finalizar Atendimento">
            </p>
        </form>
    </div>
</div>

<script>
    var opNaoEnvia = $("#parametrosOpNaoEnvUltMensagem").val();
    if( opNaoEnvia === "1" ){ $("#pNaoEnvia").attr("style", "display:block"); }

  

    // Tratamento da Mensagem de Inatividade //
    $("#msgInatividade").val("");
    $("#enviaMsgInatividade").prop("checked", false);

    // Tratamento da Mensagem de Obs //
    $("#msgObs").val("");
    $("#VinculaObs").prop("checked", false);

    // Finalizar o Atendimento //
    $('#btnFinalizarAtendimento').click(function() {
        var numero = $("#s_numero").val();
        var idAtendimento = $("#s_id_atendimento").val();
        var nome = $("#s_nome").val();
        var idCanal = $("#s_id_canal").val();
        var msg = $("#msg").val();
        var msgInatividade = $("#msgInatividade").val();
        var msgObs = $("#msgObs").val();
        var enviaMensagemFinal = $("#enviaMensagemFinal").is(':checked');
        var enviaMsgInatividade = $("#enviaMsgInatividade").is(':checked');

      //  alert("Canal:"+idCanal + "ID:"+idAtendimento+ " NUMERO:"+numero);

        // Zera a Mensagem para não enviar //
        if( $("#enviaMsgInatividade").prop("checked") === false ){ msgDscnx = ""; }
    
        // Faz a Inicialização do atendimento //
        $.post("atendimento/finalizarAtendimento.php", {
            numero: numero,
            id_atendimento: idAtendimento,
            nome: nome,
            id_canal: idCanal,
            msg: msg,
            enviaMensagemFinal: enviaMensagemFinal,
            enviaMsgInatividade: enviaMsgInatividade,
            msgInatividade: msgInatividade,
            msgObs: msgObs,
            id_etiqueta : $("#id_etiqueta").val()
        }, function(retorno) {
            if( retorno == 1 ){
                mostraDialogo("Atendimento Finalizado!", "success", 2500);

                // Limpando os Hiddens de Controle //
                    $("#s_numero").val("");
                    $("#s_id_atendimento").val("");
                    $("#s_nome").val("");
                    $("#s_id_canal").val("");
                    $("#msgInatividade").val("");
                    $("#msgObs").val("");
                    $("#enviaMsgInatividade").prop("checked", false);
                    $("#VinculaObs").prop("checked", false);
                    $("#spanMsgObs").attr("style", "display:none");
                // FIM Limpando os Hiddens de Controle //

                // Atualiza a Lista de Atendimentos 'Sem Setor' //
                $.ajax("atendimento/triagem.php").done(function(data) {
                    $('#ListaTriagem').html(data);
                });
                // Atualiza a Lista de Atendimentos 'Pendentes' //
                $.ajax("atendimento/pendentes.php").done(function(data) {
                    $('#ListaPendentes').html(data);
                });
                // Atualiza a Lista de Atendimentos 'Em Atendimento' //
                $.ajax("atendimento/atendendo.php").done(function(data) {
                    $('#ListaEmAtendimento').html(data);
                });

                // Limpando a Área de Conversa //
                $('#AtendimentoAberto').html("");
            }
            else{ mostraDialogo("Ocorreu algum erro ao tentar finalizar o Atendimento!", "danger", 2500);}
        });
    });

    // Mostra/Esconde o TextArea com a Mensagem de 'Desconexão por Inatividade' //
    $('#enviaMsgInatividade').click(function() {
        // Verifica se o campo está 'checado' //
        if( $("#enviaMsgInatividade").prop("checked") ){
            // Recuperando a Mensagem de Desconexão por Inatividade //
                $.getJSON("cadastros/configuracoes/carregardados.php"
                    , function(registro){
                    $("#msgInatividade").val(registro.msg_desc_inatividade);
                });
            // FIM Recuperando a Mensagem de Desconexão por Inatividade //

            $("#spanMsgInatividade").attr("style", "display:block");
        }
        else{ $("#spanMsgInatividade").attr("style", "display:none"); }
    });


       //Mostra/Esconde o TextArea Vincular observação aos atendimentos futuros desse número
    
     $('#VinculaObs').click(function() {
        // Verifica se o campo está 'checado' //
        if( $("#VinculaObs").prop("checked") ){

            $("#spanMsgObs").attr("style", "display:block");
        }
        else{ $("#spanMsgObs").attr("style", "display:none"); }
    });



// Aqui são os tratamentos para a escolha da TAG
    $('.pesqEtiquetas').select2({
    placeholder: 'TAGS',
    maximumSelectionLength: 10,
    "language": "pt-BR"
  });

  function ajustarCoresSelect2() {
    
  var selectedColors = {};

    $('.pesqEtiquetas').on('select2:select', function(e) {
    var selectedOption = e.params.data.element;
    var selectedColor = $(selectedOption).attr('data-color');
    var selectedId = $(selectedOption).val();
    var selectedTag = $(this).next().find('.select2-selection__choice[title="' + e.params.data.text + '"]');
    selectedColors[selectedId] = selectedColor;
    for (var id in selectedColors) {
      var selectedOption = $(this).find('option[value="' + id + '"]');
      selectedOption.css('background-color', selectedColors[id]);
      var selectedTag = $(this).next().find('.select2-selection__choice[title="' + selectedOption.text() + '"]');
      selectedTag.css('background-color', selectedColors[id]);
    }
    selectedTag.css('background-color', selectedColor);
  });
}

$('#id_etiqueta').on('select2:open', function() {
  ajustarCoresSelect2();
});

</script>