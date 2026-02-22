<?php
    // Requires //
    require_once("../includes/padrao.inc.php");

    // Definições de Variáveis //
        $strNumero = isset($_GET["numero"]) ? $_GET["numero"] : "";
        $strNome = isset($_GET["nome"]) ? limpaNome($_GET["nome"]) : "";
        $idCanal = isset($_GET["id_canal"]) ? $_GET["id_canal"] : "";
    // FIM Definições de Variáveis //
  
    // Formatando o Número do Celular //
        $labelNumero = Mask($strNumero);
    // FIM Formatando o Número do Celular //
    
    // Busca a foto de perfil
    $fotoPerfil = getFotoPerfil($conexao, $strNumero);
  ?>

<!-- Corpo das Mensagens -->
<div class="YUoyu" data-asset-chat-background="true"></div>
<header class="_3AwwN">
    <div class="_18tv-" role="button">
        <div class="_1WliW" style="height: 40px; width: 40px;">
            <img src="#" class="Qgzj8 gqwaM" style="display:none;" id="active-photo">
            <div class="_3ZW2E">
                <span data-icon="default-user">
                    <img src="<?php echo $fotoPerfil; ?>" class="rounded-circle user_img">
                </span>
            </div>
        </div>
    </div>
    <div class="_1WBXd" role="button">
        <div class="_2EbF-">
            <div class="_2zCDG">
                <span dir="auto" title="Nome do Contato Ativo" class="_1wjpf" id="active-name"><?php echo $strNome; ?></span>
            </div>
        </div>
        <div class="_3sgkv Gd51Q">
            <?php echo $labelNumero; ?>
            Total de mensagens:(<span id="TotalMensagensHistorico" style="font-size:10px">0</span>)
        </div>
    </div>
    <div class="_1i0-u">
        <ul class="user-options alt">
            <li class="tooltip">
                <a href="javascript:;" id="fecharHistorico">
                    <i class="far fa-times-circle" style="font-size:24px" alt="Fechar Histórico" title="Fechar Histórico"></i>                    
                </a>
                
            </li>
        </ul>
    </div>
</header>
<div class="_3zJZ2 ">
    <div class="copyable-area">
        <!-- Painel de Mensagens -->
        <div class="messages-container" id="panel-messages-container2" tabindex="0">
            <div class="_9tCEa" id="mensagensHistorico"></div>
        </div>
        <!-- FIM Painel de Mensagens -->
    </div>
 </div>
<div class="grGJn" style="height: 0px;"></div>

<!-- FIM Corpo das Mensagens -->

<script>
    $(document).ready(function() {
        var numero = '<?php echo $strNumero; ?>';
        var nome = encodeURIComponent('<?php echo $strNome; ?>');
        var idCanal = '<?php echo $idCanal; ?>';
        var  parametroshistorico_atendimento = $("#parametroshistorico_atendimento").val(); 
        var filtro = '';
        if (parametroshistorico_atendimento){
           filtro = 'all'; //All vai filtrar a conversa inteira com o Número selecionado
        }else{
            filtro = 'att'; //ATT vai filtrar a conversa por atendimento
        }

        $.post("atendimento/qtdConversas.php", {
            numero: numero,
            id: filtro,
            id_canal: idCanal
        }, function(retorno) {
            $.ajax("atendimento/listaConversas.php?id=all&numero=" + numero + "&nome=" + nome + "&id_canal=" + idCanal).done(function(data) {
                $('#mensagensHistorico').html(data);
                //Rola o Scroll            
                 scrollToBottom();       

             
            });
            $("#TotalMensagensHistorico").html(retorno);
        });
        
        // Selecionar o Imput File //
        $("#fecharHistorico").click(function() {
            $.post("includes/deletarArquivos.php", {numero: numero}, function() {
                // Fechar a Janela Modal do Histórico //
                 fecharModal();                 
            });
        });



       


    });

   

    function scrollToBottom() {
             $("#panel-messages-container2").animate({ scrollTop: $("#panel-messages-container2")[0].scrollHeight }, 1000);
                                	// Scroll Automático //
                    if( $("#mensagensHistorico").length ){
                        var rolagem = document.getElementById('mensagensHistorico');
                        var target = $('#mensagensHistorico');
                
                        target.animate({ scrollTop: rolagem.scrollHeight }, 200);
                    }
                // FIM Scroll Automático //
        }
</script>