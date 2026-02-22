<?php
  // Requires //
  require_once("../includes/padrao.inc.php");

  // Definições de Variáveis //
    $idAtendimento = $_GET["id"];
    $numero = $_GET["numero"];
    $Nome = $_GET["nome"];
    $idCanal = isset($_REQUEST["id_canal"]) ? $_REQUEST["id_canal"] : "";
  // FIM Definições de Variáveis //

  // Formatando o Número do Celular //
    $numero_exibir = Mask($numero);
  // FIM Formatando o Número do Celular //

  //Busca a foto de perfil
  $fotoPerfil = getFotoPerfil($conexao, $numero);
?>

<!-- Corpo das Mensagens -->
<?php require_once("htmlConversa.php"); ?>
<!-- FIM Corpo das Mensagens -->

<script>
    $(document).ready(function() {
      $('#_arquivo').hide();
      $('#divDigitacao').hide();
      $('#transferirParaMim').show();

      function AtualizaConversas() {
            var numero = $("#s_numero").val();
            var id = $("#s_id_atendimento").val();
            var qtdMensagens = $("#TotalMensagens").text();
            var nome = encodeURIComponent($("#s_nome").val());
            var id_canal = $("#s_id_canal").val();

            $.post("atendimento/qtdConversas.php", {
                numero: numero,
                id: id,
                id_canal: id_canal
            }, function(retorno) {
                //Válida se é para Atualizar a conversa, só faz a atualização da tela se existirem novas mensagens
                if (parseInt(retorno) > parseInt(qtdMensagens)) {
                  $.ajax("atendimento/listaConversas.php?id=" + id + "&id_canal=" + id_canal + "&numero=" + numero + "&nome=" + nome).done(function(data) {
                    $('#mensagens').html(data);
                  });
                }
                $("#TotalMensagens").html(retorno);
            });
        }

        var intervalo = setInterval(function() {
            AtualizaConversas();
        }, 3000);

      //Faz a Transfêrencia do Atendimento para o usuario atual
      $('#btnTransferirAtendimentoTriagem').click(function() {
        var numero = $("#s_numero").val();
        var id_atendimento = $("#s_id_atendimento").val();
        var nome = $("#s_nome").val();
        var id_departamento = $("#departamento option:selected").val();
        var departamento = $("#departamento option:selected").text();
        var id_canal = $("#s_id_canal").val();

       // alert(id_canal);

        //Faz a Inicialização do Atendimento //
        $('#btnTransferirAtendimentoTriagem').html('<i class="fa fa-spinner fa-spin"></i> Transferindo...');
        $('#btnTransferirAtendimentoTriagem').attr('disabled', true);

        $.post("atendimento/transferirAtendimento.php", {
            numero: numero,
            id_atendimento: id_atendimento,
            nome: nome,
            departamento: id_departamento,
            id_canal: id_canal,
            paramim:'S'
        }, function(retorno) {
       //  alert(retorno);
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

          // Tratando o Retorno //
          if (retorno == "transferido" ) { mostraDialogo("O Atendimento já foi transferido!", "danger", 2500); }
          else if (retorno == "-1") { mostraDialogo("Ocorreu algum erro ao tentar Transferir o Atendimento!", "danger", 2500); }
          else {
            // mostraDialogo("Atendimento Transferido", "success", 2500);
            // Mostro a Conversa //
            $.ajax("atendimento/conversa.php?id="+retorno+"&id_canal="+id_canal+"&numero="+numero+"&nome="+encodeURIComponent(nome)).done(
              function(data) {
              $('#AtendimentoAberto').html(data);
              $('.linkDivPendente').removeClass( "carregando" );
            });
            // FIM Faz a Inicialização do atendimento //
          }
        });
      });
    });
</script>