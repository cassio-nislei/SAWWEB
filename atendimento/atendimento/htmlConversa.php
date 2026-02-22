<?php
    // Definições de Variáveis //
        $idAtendimento = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $numero = isset($_REQUEST["numero"]) ? trim($_REQUEST["numero"]) : "";
        $Nome = isset($_REQUEST["nome"]) ? limpaNome($_REQUEST["nome"]) : "";
        $idCanal = isset($_REQUEST["id_canal"]) ? $_REQUEST["id_canal"] : "";
    // FIM Definições de Variáveis //
  //  echo "<h1>CANAL:  $idCanal</h1>";

    // Seleciona o último ID Canal utilizado pelo cliente //
   //     $qryIDCanal = mysqli_query($conexao, "SELECT canal FROM `tbatendimento` WHERE `numero` = '".$numero."' ORDER BY id DESC LIMIT 1;");
  //      $objIDCanal = mysqli_fetch_object($qryIDCanal);
   //     $idCanal = $objIDCanal->canal;

       // echo "<h1>CANAL:  $idCanal</h1>";
    // FIM Seleciona o último ID Canal utilizado pelo cliente //
?>

<div class="YUoyu" data-asset-chat-background="true"></div>

<script>
    $( document ).ready(function() {	
        $("#s_numero").val("<?php echo $numero; ?>");
        $("#s_nome").val("<?php echo $Nome; ?>");
        $("#s_id_atendimento").val("<?php echo $idAtendimento; ?>");
        $("#s_id_canal").val("<?php echo $idCanal; ?>");
	});
</script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<header class="_3AwwN">
  <i id="btnVoltarResponsivo" class="fas fa-solid fa-chevron-left fa-2x"></i>
    <div class="_18tv-" role="button">    
        <div class="_1WliW" style="height: 40px; width: 40px;">
        
            <img src="#" class="Qgzj8 gqwaM" style="display:none;" id="active-photo">            
            <div class="_3ZW2E">
                <span data-icon="default-user">
                <a href="<?php echo $fotoPerfil; ?>" data-lightbox-title=""><img src="<?php echo $fotoPerfil; ?>" class="rounded-circle user_img"></a>
                </span>
            </div>
        </div>
    </div>
    <div class="_1WBXd" role="button">
        <div class="_2EbF-">
            <div class="_2zCDG">
                <span dir="auto" title="Nome do Contato Ativo - Canal <?php echo $idCanal; ?>" class="_1wjpf" id="active-name"><?php echo  limpaNome($Nome); ?></span>
            </div>
        </div>
        <div class="_3sgkv Gd51Q">
            <?php echo $numero_exibir; ?>
            Total de mensagens: (<span id="TotalMensagens" style="font-size:10px">0</span>)
        </div>
    </div>
    <div class="_1i0-u">
        <ul class="user-options alt">

             <li class="" id="menu-EdtContato">                   
                <i id="btnAlterarContato" class="fas fa-pen" style="font-size: 1.1em; margin-top: 10px; color:#000000;" title="Observações vinculadas ao atendimento"></i>
                         
             </li>
            <li class="" id="menu-obs">
              <a href="javascript:;" onclick="abrirModal('#modalObservacoesAtendimento')">
                <i id="btnObsAtendimento" class="fas fa-comments" style="font-size: 1.1em; margin-top: 10px; margin-left:4px; color:#00CED1;" title="Observações vinculadas ao atendimento"></i>
                </a>                 
             </li>

            <li class="" id="menu-historico">
                <!-- <a href="javascript:;" onclick=""> -->
                <i class="fas fa-history" style="font-size: 1.1em; margin-top: 10px; margin-left:4px" title="Histórico de mensagens"></i>
                <!-- </a> -->                
            </li>

            <li class="btNovaConversa" id="menu-transferir">
                <a href="javascript:;" onclick="abrirModal('#modalTransferirAtendimento')">
                    <i class="fas fa-random" style="font-size: 1.3em; margin-top: 7px" title="Transferir atendimento"></i>
                </a>              
            </li>

            <!-- Enviar Arquivos -->
            <li class="btNovaConversa" id="_arquivo" style="padding-left: 0; padding-right: 0;">
                <div style="cursor:pointer" id="anexo">
                    <i class="fas fa-paperclip" id="btnAnexar" style="font-size: 1.2em; margin-top: 9px" title="Enviar Arquivos"></i>                   
                </div>
            </li>
            <!-- FIM Enviar Arquivos -->

            <!-- Finalizar Atendimento -->
            <li class="" id="menu-finalizar">
                <a href="javascript:;" onclick="abrirModal('#modalFinalizarAtendimento')" class="btNovaConversa ">
                    <i class="far fa-times-circle btnFinalizarChat" style="margin-top: 11px" title="Finalizar atendimento"></i>
                    <!-- Finalizar -->
                </a>           
            </li>
            <!-- FIM Finalizar Atendimento -->
        </ul>
    </div>
</header>
<div class="_3zJZ2 ">
    <div class="copyable-area">
        <!-- Painel de Mensagens -->
        <div class="messages-container" id="panel-messages-container" tabindex="0">
            <div class="_9tCEa" id="mensagens">
                <!-- Listar as Conversas -->
            </div>
        </div>
        <!-- FIM Painel de Mensagens -->
    </div>
</div>
<div class="grGJn" style="height: 0px;"></div>
<footer tabindex="-1" class="_2jVLL" style="display:block">
    <div class="footer-content">

    <div class="_1fkhx panel-down" id="panel-emojis">
            <div>
                <div class="panel-emojis-container">
                    <div tabindex="-1">
                        <div class="_3ZkhL">
                            <div class="Sbkt2">
                                <div class="_2VzPl">
                                    <div class="_1qUCC">
                                        <div class="_1-N6t">Emojis &amp; Pessoas</div>
                                        <div tabindex="-1">
                                        <button type="button" class="emojik btn btn-sm">👍</button>
                                        <button type="button" class="emojik btn btn-sm">😁</button>
                                        <button type="button" class="emojik btn btn-sm">😂</button>
                                        <button type="button" class="emojik btn btn-sm">😃</button>
                                        <button type="button" class="emojik btn btn-sm">😄</button>
                                        <button type="button" class="emojik btn btn-sm">😅</button>
                                        <button type="button" class="emojik btn btn-sm">😆</button>
                                        <button type="button" class="emojik btn btn-sm">😉</button>
                                        <button type="button" class="emojik btn btn-sm">😊</button>
                                        <button type="button" class="emojik btn btn-sm">😋</button>
                                        <button type="button" class="emojik btn btn-sm">😌</button>
                                        <button type="button" class="emojik btn btn-sm">😍</button>
                                        <button type="button" class="emojik btn btn-sm">😏</button>
                                        <button type="button" class="emojik btn btn-sm">😒</button>
                                        <button type="button" class="emojik btn btn-sm">😓</button>
                                        <button type="button" class="emojik btn btn-sm">😔</button>
                                        <button type="button" class="emojik btn btn-sm">😖</button>
                                        <button type="button" class="emojik btn btn-sm">😘</button>
                                        <button type="button" class="emojik btn btn-sm">😚</button>
                                        <button type="button" class="emojik btn btn-sm">😜</button>
                                        <button type="button" class="emojik btn btn-sm">😝</button>
                                        <button type="button" class="emojik btn btn-sm">😞</button>
                                        <button type="button" class="emojik btn btn-sm">😠</button>
                                        <button type="button" class="emojik btn btn-sm">😢</button>
                                        <button type="button" class="emojik btn btn-sm">😤</button>
                                        <button type="button" class="emojik btn btn-sm">😥</button>
                                        <button type="button" class="emojik btn btn-sm">😨</button>
                                        <button type="button" class="emojik btn btn-sm">😩</button>
                                        <button type="button" class="emojik btn btn-sm">😪</button>
                                        <button type="button" class="emojik btn btn-sm">😫</button>
                                        <button type="button" class="emojik btn btn-sm">😭</button>
                                        <button type="button" class="emojik btn btn-sm">😰</button>
                                        <button type="button" class="emojik btn btn-sm">😱</button>
                                        <button type="button" class="emojik btn btn-sm">😲</button>
                                        <button type="button" class="emojik btn btn-sm">😳</button>
                                        <button type="button" class="emojik btn btn-sm">😵</button>
                                        <button type="button" class="emojik btn btn-sm">😷</button>
                                        <button type="button" class="emojik btn btn-sm">😸</button>
                                        <button type="button" class="emojik btn btn-sm">😹</button>
                                        <button type="button" class="emojik btn btn-sm">😺</button>
                                        <button type="button" class="emojik btn btn-sm">😻</button>
                                        <button type="button" class="emojik btn btn-sm">😼</button>
                                        <button type="button" class="emojik btn btn-sm">😽</button>
                                        <button type="button" class="emojik btn btn-sm">😾</button>
                                        <button type="button" class="emojik btn btn-sm">😿</button>
                                        <button type="button" class="emojik btn btn-sm">🙀</button>
                                        <button type="button" class="emojik btn btn-sm">🙅</button>
                                        <button type="button" class="emojik btn btn-sm">🙆</button>
                                        <button type="button" class="emojik btn btn-sm">🙇</button>
                                        <button type="button" class="emojik btn btn-sm">🙈</button>
                                        <button type="button" class="emojik btn btn-sm">🙉</button>
                                        <button type="button" class="emojik btn btn-sm">🙊</button>
                                        <button type="button" class="emojik btn btn-sm">🙋</button>
                                        <button type="button" class="emojik btn btn-sm">🙌</button>
                                        <button type="button" class="emojik btn btn-sm">🙍</button>
                                        <button type="button" class="emojik btn btn-sm">🙎</button>
                                        <!-- INI -->
                                        <button type="button" class="emojik btn btn-sm">🚓</button>
                                        <button type="button" class="emojik btn btn-sm">🐎</button>
                                        <button type="button" class="emojik btn btn-sm">🚀</button>
                                        <button type="button" class="emojik btn btn-sm">❌</button>
                                        <button type="button" class="emojik btn btn-sm">✅</button>
                                        <!-- <button type="button" class="emojik btn btn-sm">nnnn</button>
                                        <button type="button" class="emojik btn btn-sm">nnnn</button> -->
                                        <button type="button" class="emojik btn btn-sm">⚠️</button>
                                        <!-- FIM -->
                                        <button type="button" class="emojik btn btn-sm" onclick="if (!window.__cfRLUnblockHandlers) return false; InsereEmoji('🙏');" data-cf-modified-ef7614bf95d6d907da7ac829-="">🙏</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mostra a Imagem do Upload -->
        <div class="_1fkhx panel-upImage">
            <header class="VMhw8">
                <div class="_2fGIm" data-animate-drawer-title="true">
                    <div class="_215wZ" style="background: #009688;">
                        <button id="cancelaUploadImagem" class="_27F2N" style="margin: 1em; color: #fff">
                            <!-- <span data-testid="x" data-icon="x" class="">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                    <path fill="currentColor" d="M19.1 17.2l-5.3-5.3 5.3-5.3-1.8-1.8-5.3 5.4-5.3-5.3-1.8 1.7 5.3 5.3-5.3 5.3L6.7 19l5.3-5.3 5.3 5.3 1.8-1.8z"></path>
                                </svg>
                            </span> -->
                            <i class="fas fa-times"></i> Pré-visualizar
                        </button>
                    </div>
                </div>
            </header>
            <div id="panel-upload-image">
                <div id="dragDropImage" class="dropzone-wrapper">
                    <div class="dropzone-desc">
                        <p>Arraste a sua imagem e solte nesta área!</p>
                    </div>
                    <input id="imgDragDrop" type="file" name="img_logo" class="dropzone" multiple/>
                </div>
            </div>
        </div>


        <div class="_1fkhx panel-Respostas" style="display:none">
            <div class="_215wZ" style="background: #f5f1ee">
                <div  style="border-left: solid green;border-radius:3px;background-color:#CCC;opacity: 0.2;color:#000">	
                   <input type="hidden" id="chatid_resposta">						
				   <span id="RespostaSelecionada"></span>
                   <a href="javascript:;" id="fecharResposta" style="float:right">
                    <i class="far fa-times-circle"></i>
                </a>
	           </div>	
             </div>
        </div>

    </div>



    <!-- Área da Digitação -->
    <div class="_3oju3" style="display: flex" id="divDigitacao">
        <div tabindex="-1" class="_2uQuX" data-tab="4">
            <!-- Respostas Rápidas -->
            <span style="display: flex; float: left;" class="adjustIconsTalk">
                <a id="lnkRespostaRapida" href="javascript:;" onclick="abrirModal('#modalRespostaRapida')">
                    <i class="far fa-comments adjustFastAns"></i>
                </a>
            </span>

            <!-- Enviar Contatos -->
            <span style="display: flex; float: left;" class="adjustIconsTalk">
                <a id="lnkEnviarContato" href="javascript:;">
                    <i class="far fa-id-card adjustFastAns"></i>
                </a>
            </span>

            <!-- Inserir Emojis -->
            <span style="display: flex; float: left;" class="adjustIconsTalk">
                <a id="btnViewEmojs" href="#">
                    <i class="far fa-grin-alt adjustFastAns"></i>
                </a>
            </span>
        </div>
                    
        <!-- Digitar Mensagem -->
        <div tabindex="-1" class="_2bXVy">
            <div tabindex="-1" class="_3F6QL _2WovP">
                <textarea name="msg" id="msg" class="form-control type_msg" placeholder="Escreva aqui sua mensagem" style="height: 50px;"></textarea>
            </div>
        </div>
        <!-- FIM Digitar Mensagem -->

        <!-- Button Enviar -->
        <button class="_2lkdt" id="btnEnviar" style="display: none;">
            <span data-icon="send" class="adjustIconsTalk">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                    <path fill="#263238" fill-opacity=".45" d="M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z"></path>
                </svg>
            </span>
        </button>
        
        <!-- Gravar Áudio -->
        <div id="divAudio" class="audioIcons">
            <div class="gravando">
                <a href="#" class="bt-cancel" title="Cancelar Gravação"><i class="far fa-times-circle"></i></a>
                <i class="fas fa-circle" title="Gravando"></i> <span id="time" title="Gravando">00:00</span>
                <a href="#" class="bt-send" title="Enviar Gravação"><i class="far fa-check-circle"></i></a>    
            </div>
            <div class="microfone">
                <button id="btnFotografar" class="bt-fotografar" title="Iniciar Camera"><i class="fas fa-camera"></i></button>
                <button id="btnRecorder" class="bt-recorder" title="Iniciar Gravação"  style="margin-left:2px;"><i class="fas fa-microphone"></i></button>
            </div>            
        </div>
    </div>
    <div id="transferirParaMim" class="_3oju3" style="display:flex; display: none;">
        <button id="btnTransferirAtendimentoTriagem" class="btn btn-primary">Transferir Atendimento para mim</button>
    </div>
</footer>



<script>
    $( document ).ready(function() {	
        var numero = $("#s_numero").val();
        var nome = encodeURIComponent($("#s_nome").val());
        var idCanal = $("#s_id_canal").val();

        // Valida se Habilita a opção de Histórico de Conversas para os Operadores //
        $("#menu-historico").click(function () {
            if( $("#perfilUsuario").val() == 1
                && $("#historicoConversas").val() == 0 ){
                mostraDialogo("Atenção: Apenas Administradores pode visualizar o Histórico de Conversas!", "danger", 3000);
            }
            else{
                abrirModal('#modalHistorico');

                $.ajax("atendimento/historico.php?numero="+numero+"&nome="+nome+"&id_canal="+idCanal).done(function(data) {
                    $('#HistoricoAberto').html(data);
                });
            }
        });

        //CArrega as Observações vinculadas ao atendimento
        $("#btnObsAtendimento").click(function(e){
          $("#ObservacoesAtendimentos").html("Carregando Observações vinculadas a este número...");
            var numero = $("#s_numero").val();
          //  alert(numero);
            $.post("atendimento/listaObservacoes.php", {
                            numero: numero,
                        }, function(retorno) {
                        // alert(retorno);
                            $("#ObservacoesAtendimentos").html(retorno);

                });
            })

        // Habilita Aba de Contatos para seleção e Envio //
        $("#lnkEnviarContato").click( function(){
            $('#box-contatos').css("left","0");
            atualizaContatos();
        });

        $("#btnVoltarResponsivo").click( function(e){
           e.preventDefault();
           // alert("clicou");
          
             $("#btnMinimuiConversas2").click();           
             $("#btnVoltarResponsivo").css("display","none");  
       
        });
        
 

        $('#btnAlterarContato').on('click', function (){
        // Abrindo a Modal //
        abrirModal('#modalContato');


		  // Recupera o 'Número' do Contato Selecionado //
          var id= $("#s_numero").val();
        if (id.length ==0){
			alert("Erro ao selecionar contato, tente novamente");
			return false;
		}

		$('#id_etiqueta2').val(null);

        // Carregando a Tela para Edição //
        $.ajax("cadastros/contatos/index.php").done(function(data) {
            $('#modalContato').html(data);
      
          
      
         
        // Alterando o Título do Cadastro //
        $("#titleCadastroContato").html("Alteração de Contato");

        $.getJSON('cadastros/contatos/ContatoController.php?id='+id
            , function(registro){
            // Carregando os Dados no Form //
            //CArrego as TAGS selecionadas
			$.post('cadastros/contatos/listaEtiquetas.php',{numero_contato:registro.numero}, function(retorno){
				if (retorno!=0){        
					$('#id_etiqueta2').val(retorno.split(',')).trigger("change");			
  		
				}		          
               
            });

            $("#idContato").val(registro.numero);
            $("#numero_contato").val(registro.numero);
            $("#nome_contato").val(registro.nome);
			$("#cpf_cnpj").val(registro.cpf_cnpj);
			$("#razao_social").val(registro.razao_social);
			  
            // Mudo a Ação para Alterar    
            $("#acaoContato").val("2");
            $("#numero_contato").focus();
        });
	  }); //Fim do Carregamento da tela
    });
// FIM Abrindo a tela para Alteração de Contato //
   



	});
</script>