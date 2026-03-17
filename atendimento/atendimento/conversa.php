<?php
    // Requires //
    require_once("../includes/padrao.inc.php");

    // Definições de Variáveis //
        $idAtendimento = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
        $numero = isset($_REQUEST["numero"]) ? trim($_REQUEST["numero"]) : "";
        $Nome = isset($_REQUEST["nome"]) ? limpaNome($_REQUEST["nome"]) : "";
        $idCanal = isset($_REQUEST["id_canal"]) ? $_REQUEST["id_canal"] : "";
    // FIM Definições de Variáveis //

    // Formatando o Número do Celular //
        $numero_exibir = Mask($numero);
    // FIM Formatando o Número do Celular //
    
    // Busca a foto de perfil
    if( $_SESSION["parametros"]["exibe_foto_perfil"] ){
        $fotoPerfil = getFotoPerfil($conexao, $numero);
    }
    else{ $fotoPerfil = fotoPerfil; }
?>

<!-- Corpo das Mensagens -->
<?php require_once("htmlConversa.php"); ?>
<select id="encodingTypeSelect" style="display:none">
    <option value="mp3" style="display:none">MP3</option>
</select>
<!-- FIM Corpo das Mensagens -->

<style>
    .hidden-audio .microfone,
    .hidden-audio .gravando {
        display: none !important;
    }
    .hidden-audio #btnEnviar {
        display: block !important;
        margin-top: 5px !important;
    }
    #btnEnviar .adjustIconsTalk {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        margin-top: 15px !important;
    }
    #btnEnviar svg {
        width: 100% !important;
        height: 100% !important;
    }
</style>

<script src="js/WebAudioRecorder.min.js"></script>
<script src="js/WebAudioRecorderMp3.min.js"></script>
<script>
    $(document).ready(function() {
        // Observar mudanças no painel de visualização de imagem
        var observer = new MutationObserver(function(mutations) {
            if ($(".panel-upImage").hasClass("open")) {
                // Quando painel está aberto, mostrar botão enviar
                $("#divAudio").removeClass("hidden-audio").css("display", "flex");
                $("#btnEnviar").addClass("enviar-visible");
                $("#btnEnviar")[0].style.setProperty("display", "block", "important");
                $("#btnEnviar")[0].style.setProperty("visibility", "visible", "important");
                $("#btnEnviar")[0].style.setProperty("pointer-events", "auto", "important");
            } else {
                // Quando painel fecha, ocultar botão enviar
                $("#divAudio").removeClass("hidden-audio").css("display", "flex");
                $("#btnEnviar").removeClass("enviar-visible");
                $("#btnEnviar")[0].style.setProperty("display", "none", "important");
                $("#btnEnviar")[0].style.setProperty("visibility", "hidden", "important");
            }
        });

        observer.observe($(".panel-upImage")[0], {
            attributes: true,
            attributeFilter: ["class"]
        });
        
        // Botão enviar - ocultar quando clicado e fechar preview
        $("#btnEnviar").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            // O botão de envio será ocultado quando o preview fecha
            setTimeout(function() {
                $("#btnEnviar").removeClass("enviar-visible");
                $("#btnEnviar")[0].style.setProperty("display", "none", "important");
                $("#btnEnviar")[0].style.setProperty("visibility", "hidden", "important");
                $(".panel-upImage").removeClass("open");
            }, 100);
        });

        // Cancela o envio de Imagem via Área  de Transferência //
            $(document).keyup(function(event) { 
                if( event.keyCode === 27 ){ //Iniciar Gravação
                    cancelaUploadImageClipboard();
                }
            });
        // FIM Cancela o envio de Imagem via Área  de Transferência //

        var ehaudio = false;
        var audioConfirmado = false;  // FLAG: áudio foi confirmado e está pronto para enviar
        var audioProcessado = false;  // FLAG: áudio foi completamente processado e convertido para Base64
        var enviandoMensagem = false;  // FLAG: protege contra múltiplos envios simultâneos
        var imageClipboard = false;
        var imageCamera = false;
        var ehupload = false;
        var form;
        form = new FormData();

        function ajustaScroll(){	
            $('#panel-messages-container').animate({
                scrollTop: $(this).height()*100 // aqui introduz o numero de px que quer no scroll, neste caso é a altura da propria div, o que faz com que venha para o fim
            }, 100);
        }

        function carregaAtendimento() {
            var numero = $("#s_numero").val();
            var id = $("#s_id_atendimento").val();
            var qtdMensagens = $("#TotalMensagens").text();
            var nome = encodeURIComponent($("#s_nome").val());
            var id_canal = $("#s_id_canal").val();

            console.log("🔄 carregaAtendimento: numero=" + numero + ", id=" + id + ", qtdMensagens=" + qtdMensagens + ", nome=" + $("#s_nome").val());

            $.post("atendimento/qtdConversas.php", {
                numero: numero,
                id: id,
                id_canal: id_canal
            }, function(retorno) {
                console.log('📊 QTD Conversas:' + retorno + ' (anterior: ' + qtdMensagens + ')');
                //Válida se é para Atualizar a conversa, só faz a atualização da tela se existirem novas mensagens
                if (parseInt(retorno) > parseInt(qtdMensagens)) {
                    console.log('✅ Novas mensagens detectadas, carregando...');
                    $.ajax({
                        url: "atendimento/listaConversas.php?id=" + id + "&id_canal=" + id_canal + "&numero=" + numero + "&nome=" + nome,
                        timeout: 30000,  // 30 segundos para processar áudio grande
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("❌ Erro ao carregar mensagens: " + textStatus + " - " + errorThrown);
                            if (textStatus === 'timeout') {
                                console.warn("⏱ Timeout ao carregar mensagens. Servidor pode estar processando áudio grande. Tentando novamente...");
                                setTimeout(function() { carregaAtendimento(); }, 2000);
                            }
                        }
                    }).done(function(data) {
                          try {
                              console.log('✅ Mensagens carregadas (' + (data ? data.length : 0) +' chars)');
                              $('#mensagens').html(data);
                          } catch(e) {
                              console.error("❌ Erro ao carregar mensagens HTML: " + e.message);
                              console.error("Resposta do servidor tinha " + (data ? data.length : 0) + " caracteres");
                          }
                    });

                    ajustaScroll(); //desço a barra de rolagem da conversa
                }
                $("#TotalMensagens").html(retorno);
            });
        }

        // Atualiza a Lista de Atendimentos //
            var intervalo = setInterval(function() { carregaAtendimento(); }, 5000);
            carregaAtendimento();
        // FIM Atualiza a Lista de Atendimentos //

        // Selecionar o Imput File //
        $("#btnAnexar").click(function() {
            $(".panel-upImage").addClass("open");
            $("#dragDropImage").attr("style", "display:block");
        });

        // Tratamento dos dados digitados no campo de Mensagem //
            $("#msg").keydown(function(event) { processaMensagem(event); });
         
            // Processa e Submita os Dados digitados no campo de Mensagem //
                function processaMensagem(event){
                    var strMensagem = $.trim($("#msg").val());
                    var eventCodes = ",8,9,13,16,17,18,20,27,32,33,34,35,36,37,38,39,40,45,46,91,93,112,113,114,115,116,117,118,119,120,121,122,123,144,173,174,175,";
                    var padrao = ","+event.keyCode+",";
                    var regex = new RegExp(padrao);

                    if( strMensagem.length === 0 && !regex.test(eventCodes) ){
                        $("#btnEnviar").attr("style", "display: block");
                        $("#divAudio").attr("style", "display: none");
                    }
                    else if( ( strMensagem.length === 1 && event.key === "Backspace" )
                        || strMensagem === "" ){
                        $("#btnEnviar").attr("style", "display: none");
                        $("#divAudio").attr("style", "display: block");
                    }
                    
                    // Permitir quando pressionar <Shift> e <Enter>	//
                        if( event.keyCode == 13 && event.shiftKey ){
                            var content = $("#msg").val();
                            var caret = getCaret(this);
                            this.value = content.substring(0,caret) 
                                            + "\n" 
                                            + content.substring(caret,content.length-1);
                            event.stopPropagation();
                        }
                        else if( event.keyCode == 13 ){
                            // Se há áudio sendo processado, aguardar
                            if (audioConfirmado && !audioProcessado) {
                                console.warn("⏳ Áudio ainda está sendo processado. Aguarde...");
                                alert("Aguarde... o áudio ainda está sendo processado (encoding em progresso)");
                                event.preventDefault();
                                return false;
                            }
                            
                            // Submita os Dados //	 
                            event.preventDefault();
                            $("#msg").focus();
                            $("#btnEnviar").click();
                            $("#btnEnviar").attr("style", "display: none");
                            $("#divAudio").attr("style", "display: block");
                            return false;
                        }
                    // FIM Permitir quando pressionar <Shift> e <Enter>	//
                }

                function getCaret(el) {
                    if (el.selectionStart) {
                        return el.selectionStart;
                    }
                    else if (document.selection) {
                        el.focus();

                        var r = document.selection.createRange();

                        if (r == null) { return 0; }

                        var re = el.createTextRange(),
                            rc = re.duplicate();
                        re.moveToBookmark(r.getBookmark());
                        rc.setEndPoint('EndToStart', re);

                        return rc.text.length;
                    }

                    return 0;
                }
            // FIM Processa e Submita os Dados digitados no campo de Mensagem //
        // FIM Tratamento dos dados digitados no campo de Mensagem //

        // Clique no Botão Enviar //
        $("#btnEnviar").click(function() {
            // PROTEÇÃO: Evita múltiplos envios simultâneos
            if (enviandoMensagem) {
                console.warn("Múltiplo click detectado! Ignorando...");
                return false;
            }
            
            // Se há áudio sendo processado, não permitir envio
            if (audioConfirmado && !audioProcessado) {
                console.error("Tentativa de envio enquanto áudio está sendo processado!");
                alert("Aguarde... o áudio ainda está sendo processado (encoding em progresso)");
                return false;
            }
            
            enviandoMensagem = true;  // Marca como enviando
            
            // Desabilitando as opções de Envio //
                $("#lnkRespostaRapida").removeAttr( "onclick");
                $("#btnViewEmojs").prop( "disabled", true );
                $("#msg").prop( "disabled", true );
                $("#btnRecorder").prop( "disabled", true );
                $("#btnEnviar").prop( "disabled", true );
            // FIM Desabilitando as opções de Envio //

            // Fecha painel de Visualização da Imagem e Limpa a Div //
            $(".panel-upImage").removeClass('open');

            // Declaração de Variáveis //
            var numero = $("#s_numero").val();
            var id_atendimento = $("#s_id_atendimento").val();
            var nome = $("#s_nome").val();
            var id_canal = $("#s_id_canal").val();
            var msg = $("#msg").val();
            var msg_resposta = $("#RespostaSelecionada").html();
            var idResposta = $("#chatid_resposta").val();
			var upload = document.getElementById("upload").files.length;
            var anexomsgRapida = $("#anexomsgRapida").val();
            var nomeanexomsgRapida = $("#nomeanexomsgRapida").val();
              
            // Montando os Dados [Form] para Envio //
            form.append('numero', numero);
            form.append('id_atendimento', id_atendimento);
            form.append('nome', nome);
            form.append('id_canal', id_canal);
            form.append('msg', msg);
            form.append('Resposta', msg_resposta); //Adicionei a mensagem de Resposta
            form.append('idResposta', idResposta); //Adicionei a mensagem de Resposta
            form.append('anexomsgRapida', anexomsgRapida); //Adicionei a mensagem de Resposta
            form.append('nomeanexomsgRapida', nomeanexomsgRapida); //Adicionei a mensagem de Resposta

            // Se não for 'Áudio' e não for 'Imagem da Área de Transferência' //
			if( (!ehaudio) && (!imageClipboard) && (!ehupload) && (!imageCamera) ) {
                if ( ($.trim(msg) == '') ) {
					 $("#msg").prop( "disabled", false );
					 $("#msg").attr("value", "");
                     $("#btnRecorder").prop( "disabled", false );
                     $("#btnEnviar").prop( "disabled", false );
					 $("#msg").focus();
                     return false;
                }
            }

            //Faz a Inicialização do atendimento
            $.ajax({
                url: 'atendimento/gravarMensagem.php', // Url do lado server que vai receber o arquivo
                data: form,
                processData: false,
                contentType: false,
                type: 'POST',
                resetForm: true,
                success: function(retorno) {
                    enviandoMensagem = false;  // Libera para próximo envio
                    carregaAtendimento();	
                    form = new FormData();
 //alert(retorno);
                    // Limpando Campos //
                    $("#msg").val("");
                    $("#anexomsgRapida").val("0");
                    $("#RespostaSelecionada").html("");
                    $("#chatid_resposta").val(""); 
                    $("#upload").val("");
                    $("#imgDragDrop").val("");                   
					ehupload = false;
                    imageClipboard = false;
                    //Limpo as imagens tiradas pela Camera
                    if (imageCamera){
                        var lista = document.getElementsByClassName("imgView");
                        for(var i = lista.length - 1; i >= 0; i--)
                        {
                            lista[i].remove()
                        }
                                
                    }
                    imageCamera = false

                    // Removendo as Tags <img> e <audio> //
                    removeFile();
                    cancelaUploadImageClipboard();

                    // Habilitando as opções de Envio //
                        $("#lnkRespostaRapida").attr( "onclick", "abrirModal('#modalRespostaRapida')" );
                        $("#btnViewEmojs").prop( "disabled", false );
                        $("#msg").prop( "disabled", false );
                        $("#btnRecorder").prop( "disabled", false );
                        $("#btnEnviar").prop( "disabled", false );
                    // FIM Habilitando as opções de Envio //

                    $("#btnEnviar").attr("style", "display: none");
                    $("#divAudio").attr("style", "display: block");
                    $("#msg").focus();
                    audioConfirmado = false;  // Reset flag após envio bem-sucedido
                    audioProcessado = false;  // Reset audio processado flag
                    ehaudio = false;  // Zerar flag de áudio para evitar duplo envio
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    enviandoMensagem = false;  // Libera para próximo envio mesmo em caso de erro
                    console.error("Erro ao enviar mensagem:", textStatus, errorThrown);
                    // Resetar flags em caso de erro
                    form = new FormData();
                    ehaudio = false;
                    audioConfirmado = false;
                    audioProcessado = false;
                    // Re-habilitar botões
                    $("#lnkRespostaRapida").attr( "onclick", "abrirModal('#modalRespostaRapida')" );
                    $("#btnViewEmojs").prop( "disabled", false );
                    $("#msg").prop( "disabled", false );
                    $("#btnRecorder").prop( "disabled", false );
                    $("#btnEnviar").prop( "disabled", false );
                }
            });
        });
        // FIM Clique no Botão Enviar //

        //Limpo a notificação no menu
        $('#not' + $("#s_numero").val()).text("");

        // Insere o Emoj //
        $("#btnViewEmojs").click(function() {
            var _class = $(".panel-down").attr("class").split(" ");
            var indice = (_class.length - 1);

            if( _class[indice] !== "open" ){
                $(".panel-down").addClass("open");
            }
            else{ $(".panel-down").removeClass('open'); }
        });


        

        // Insere o Emoji Textarea //
        $(".emojik").click(function() {
            var emoji = $(this).text();
            $('#msg').val($('#msg').val() + emoji);
            $(".panel-down").removeClass('open');
            $('#msg').focus();
        });

        // Event Delegation para botões de mensagens carregadas dinamicamente //
        // Auto-Scroll das mensagens //
        $(document).on('DOMNodeInserted', '#mensagens', function() {
            var rolagem = document.getElementById('mensagens');
            if (rolagem) {
                $('#mensagens').animate({ scrollTop: rolagem.scrollHeight }, 200);
            }
        });

        // Responder mensagem //
        $(document).on("click", ".btnResponderMSG", function() {
            var msgRecuperada = $(this).parent().find("#msg_original").val();
            var idResposta = $(this).parent().find("#chatID").val();
            $(".panel-Respostas").fadeIn(500);
            $("#chatid_resposta").val(idResposta);
            $("#RespostaSelecionada").html(msgRecuperada);
            $('#msg').focus();
        });

        // Fechar resposta //
        $(document).on("click", "#fecharResposta", function() {
            $(".panel-Respostas").fadeOut(500);
            $("#RespostaSelecionada").html('');
        });

        // Reagir à mensagem //
        var elementoreacao = "";
        var idResposta = "";
        $(document).on("click", ".btnReagirMSG", function() {
            var msgRecuperada = $(this).parent().find("#msg_original").val();
            idResposta = $(this).parent().find("#chatID").val();
            elementoreacao = $(this).parents(".message").find(".ReacaoManifestada");
            $('#ModalReacoes').modal('show');
        });

        // Reação emoji //
        $(document).on("click", ".emojreacao", function(e) {
            e.stopPropagation();
            e.preventDefault();
            $(elementoreacao).fadeIn();
            $(elementoreacao).html($(this).html());
            var iconereact = $(this).attr('value');
            $.post("atendimento/reacaoMensagem.php", {id: idResposta, reacao: iconereact}, function(resultado) {});
            $('#ModalReacoes').modal('hide');
        });

        // Fechar modal de reações //
        $(document).on("click", "#ModalReacoes", function() {
            $('#ModalReacoes').modal('hide');
        });

        // Apagar mensagem //
        $(document).on("click", ".btnApagarMSG", function() {
            var idUnico = $(this).parent().find("#chatID").val();
            var elementoMensagem = $(this).parent().parent().parent().parent().find(".Tkt2p");
            var numero = $("#s_numero").val();
            var id_atendimento = $("#s_id_atendimento").val();
            var id_canal = $("#s_id_canal").val();
            var sequencia = $(this).parent().find("#seq_msg").val();

            $.post("atendimento/apagarMensagem.php", {
                id: idUnico,
                numero: numero,
                id_atendimento: id_atendimento,
                seq: sequencia
            }, function(resultado) {
                elementoMensagem.html("🚫Mensagem Apagada!!!");
            });
        });

        // Botão enviar mensagem rápida //
        $(document).on("click", ".btn-message-send", function() {
            var numero = $(this).data('numero');
            var nome = $(this).data('nome');

            $.post("cadastros/contatos/ContatoController.php", {
                id: 0,
                acao: 1,
                numero_contato: numero,
                nome_contato: nome
            }, function(resultado) {});

            $.post("atendimento/gerarAtendimento.php", {numero: numero, nome: nome}, function(idAtendimento) {
                if (idAtendimento != "erro") {
                    $('#not' + idAtendimento).text("");
                    $('#AtendimentoAberto').html("<div class='spinner-border text-primary' role='status'><span class='sr-only'>Carregando ...</span></div>");
                    $.ajax("atendimento/conversa.php?id=" + idAtendimento + "&id_canal=1&numero=" + encodeURIComponent(numero) + "&nome=" + encodeURIComponent(nome)).done(function(data) {
                        $('#AtendimentoAberto').html(data);
                        $.ajax("atendimento/atendendo.php").done(function(data) {
                            $('#ListaEmAtendimento').html(data);
                        });
                    });
                } else {
                    mostraDialogo("Erro ao tentar Iniciar o Atendimento!", "danger", 2500);
                }
            });
        });
        // FIM Event Delegation //

        // Gravação de Áudio via MP3 //
			//webkitURL is deprecated but nevertheless
            URL = window.URL || window.webkitURL;

            var gumStream = null; 						//stream from getUserMedia()
            var recorder = null; 						//WebAudioRecorder object
            var input = null; 						//MediaStreamAudioSourceNode  we'll be recording
            var encodingType = null; 						//holds selected encoding for resulting audio (file)
            var encodeAfterRecord = true;       // when to encode

            // shim for AudioContext when it's not avb. 
            var AudioContext = window.AudioContext || window.webkitAudioContext;
            var audioContext; //new audio context to help us record

            var encodingTypeSelect = document.getElementById("encodingTypeSelect");

            function startRecording() {
                /*
                    Simple constraints object, for more advanced features see
                    https://addpipe.com/blog/audio-constraints-getusermedia/
                */
                var constraints = { audio: true, video:false }

                /*
                    We're using the standard promise based getUserMedia() 
                    https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
                */
                navigator.mediaDevices.getUserMedia(constraints).then
                (function(stream) {
                    console.log("✓ Microfone acessado com sucesso");
                    console.log("🎤 Stream recebido:", stream);
                    
                    /*
                        create an audio context after getUserMedia is called
                        sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
                        the sampleRate defaults to the one set in your OS for your playback device

                    */
                    audioContext = new AudioContext();

                    //assign to gumStream for later use
                    gumStream = stream;
                    console.log("✓ gumStream atribuído:", gumStream);
                    
                    /* use the stream */
                    input = audioContext.createMediaStreamSource(stream);

                    //get the encoding 
                    encodingType = encodingTypeSelect.options[encodingTypeSelect.selectedIndex].value;
                    
                    //disable the encoding selector
                    encodingTypeSelect.disabled = true;

                    recorder = new WebAudioRecorder(input, {
                        workerDir: "js/", // must end with slash
                        encoding: encodingType,
                        numChannels:2, //2 is the default, mp3 encoding supports only 2
                        onEncoderLoading: function(recorder, encoding) {
                            console.log("Carregando encoder...");
                        },
                        onEncoderLoaded: function(recorder, encoding) {
                            console.log("✓ Encoder carregado: " + encoding);
                        }
                    });
                    
                    console.log("✓ recorder criado:", recorder);

                    recorder.onComplete = function(recorder, blob) {
                        console.log("✓ Gravação completa, tamanho: " + blob.size + " bytes");
                        createDownloadLink(blob,recorder.encoding);
                        audioProcessado = true;  // Marca que o áudio foi completamente processado
                        console.log("✓ audioProcessado = true (pronto para envio)");
                        encodingTypeSelect.disabled = false;
                    }
                    
                    recorder.onError = function(recorder, message) {
                        // Suppress benign "no recording is running" error that occurs during normal finishRecording
                        if (message.indexOf("no recording is running") !== -1) {
                            return;
                        }
                        console.error("Erro no gravador:", message);
                        alert("Erro durante a gravação: " + message);
                    }

                    recorder.setOptions({
                        timeLimit:120,
                        encodeAfterRecord:encodeAfterRecord,
                        ogg: {quality: 0.5},
                        mp3: {bitRate: 32}  // Otimizado para 32 kbps (voz clara, arquivo pequeno)
                    });

                    //start the recording process
                    console.log("Iniciando gravação...");
                    recorder.startRecording();
                    console.log("✓ Gravação iniciada");
                })
                .catch(function(err) {
                    console.error("✗ Erro ao acessar microfone:", err);
                    console.error("Detalhes:", err.name, err.message);
                    gumStream = null;
                    recorder = null;
                    alert("Erro ao acessar o microfone. Verifique as permissões e tente novamente.\n\nDetalhes: " + err.name + " - " + err.message);
                });
            }

            function stopRecording() {
                console.log("Parando gravacao...");
                
                // Verificar se gumStream foi inicializado
                if (!gumStream) {
                    console.error("ERRO: gumStream nao foi inicializado. Verifique se o microfone foi acessado corretamente.");
                    alert("Erro: o microfone nao foi inicializado corretamente. Tente novamente.");
                    return;
                }
                
                // Verificar se recorder foi inicializado
                if (!recorder) {
                    console.error("ERRO: recorder nao foi inicializado. Verifique se o gravador foi criado corretamente.");
                    alert("Erro: o gravador nao foi inicializado corretamente. Tente novamente.");
                    return;
                }
                
                // CRITICO: Verificar se a gravacao realmente esta em andamento
                if (!recorder.isRecording || !recorder.isRecording()) {
                    console.warn("AVISO: A gravacao nao esta em andamento! Pulando finishRecording()");
                    return;
                }
                
                //stop microphone access
                try {
                    if (gumStream && gumStream.getAudioTracks) {
                        gumStream.getAudioTracks()[0].stop();
                        console.log("OK: Microfone desligado");
                    }
                } catch(e) {
                    console.error("Erro ao desligar microfone:", e);
                }

                //tell the recorder to finish the recording (stop recording + encode the recorded audio)
                try {
                    if (recorder && recorder.finishRecording) {
                        recorder.finishRecording();
                        console.log("OK: Finalizando gravacao...");
                    }
                } catch(e) {
                    console.error("Erro ao finalizar gravacao:", e);
                }
            }

            function createDownloadLink(blob,encoding) {
                var url = URL.createObjectURL(blob);
                var audio = document.createElement('audio');
                var li = document.createElement('li');
                var link = document.createElement('a');

                //add controls to the <audio> element
                audio.controls = true;
                audio.src = url;
                audio.setAttribute("style", "margin-top: 300");
                audio.setAttribute("id", "audioView");

                //link the a element to the blob
                link.href = url;
                link.download = new Date().toISOString() + '.'+encoding;
                link.innerHTML = link.download;

                //add the new audio and a elements to the li element
                li.appendChild(audio);
                li.appendChild(link);

                //add the li element to the ordered list
                //coloco o Audio no formulário	
                ehaudio = true;
               // form.append("upload", blob, link.download);
               form.append("upload", blob, 'audio_gravado.mp3');
               console.log("✓ Áudio blob adicionado ao formulário (modo atendimento/atendimento)");
                
                if( $('#parametrosEnvioAudioAut').val() === "1" ){
                    console.log("Enviando audio automaticamente...");
                    
                    // Converter Blob para Base64 antes de enviar
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var audioBase64 = e.target.result;
                        console.log("✓ Áudio convertido para Base64: " + audioBase64.length + " caracteres");
                        
                        // Criar novo FormData para auto-send (não reutilizar form)
                        var formAutoSend = new FormData();
                        
                        // Adicionar campos de controle
                        formAutoSend.append('numero', $("#s_numero").val());
                        formAutoSend.append('id_atendimento', $("#s_id_atendimento").val());
                        formAutoSend.append('nome', $("#s_nome").val());
                        formAutoSend.append('id_canal', $("#s_id_canal").val());
                        formAutoSend.append('msg', $("#msg").val());
                        formAutoSend.append('Resposta', $("#RespostaSelecionada").html());
                        formAutoSend.append('idResposta', $("#chatid_resposta").val());
                        formAutoSend.append('anexomsgRapida', $("#anexomsgRapida").val());
                        formAutoSend.append('nomeanexomsgRapida', $("#nomeanexomsgRapida").val());
                        
                        // Adicionar audioBase64 DIRETAMENTE
                        formAutoSend.append('audioBase64', audioBase64);
                        
                        console.log("Enviando FormData com audioBase64...");
                        
                        // Submit AJAX
                        $.ajax({
                            url: 'atendimento/gravarMensagem.php',
                            data: formAutoSend,
                            processData: false,
                            contentType: false,
                            type: 'POST',
                            success: function(retorno) {
                                console.log("Audio enviado com sucesso. Resposta:", retorno);
                                
                                // CRÍTICO: Resetar flags IMEDIATAMENTE
                                form = new FormData();
                                ehaudio = false;
                                audioConfirmado = false;
                                audioProcessado = false;
                                enviandoMensagem = false;
                                imageClipboard = false;
                                imageCamera = false;
                                
                                // Limpar campos
                                $("#msg").val("");
                                $("#anexomsgRapida").val("0");
                                $("#RespostaSelecionada").html("");
                                $("#chatid_resposta").val("");
                                $("#upload").val("");
                                $("#imgDragDrop").val("");
                                
                                removeFile();
                                cancelaUploadImageClipboard();
                                
                                console.log("Flags resetados apos auto-send");
                                
                                $("#divAudio").attr("style", "display: block");
                                $("#msg").focus();
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Erro ao enviar audio:", textStatus);
                                enviandoMensagem = false;
                                alert("Erro ao enviar audio.");
                            }
                        });
                    };
                    // Iniciar leitura do blob como Base64
                    reader.readAsDataURL(blob);
                }
                else{
                    // Habilitando o Envio da Imagem e Bloqueando as demais Opções //
                        $("#btnEnviar").attr("style", "display: block");
                        $("#divAudio").attr("style", "display: none");
                        $("#lnkRespostaRapida").removeAttr( "onclick");
                        $("#btnViewEmojs").prop( "disabled", true );
                        $("#msg").prop( "disabled", true );
                    // FIM Habilitando o Envio da Imagem e Bloqueando as demais Opções //

                    // Abrindo o Pré-visualizar //
                    $(".panel-upImage").addClass("open");
                    $("#dragDropImage").attr("style", "display:none");
                    $("#panel-upload-image").append(audio);
                    
                    // Marcar como confirmado e pronto para enviar
                    audioConfirmado = true;
                    audioProcessado = true;  // Áudio já está processado e pronto para enviar
                }
            }

            function __log(e, data) {
                log.innerHTML += "\n" + e + " " + (data || '');
            }
        // FIM Gravação de Áudio via MP3 //

        // Scripts referentes à Gravação de Áudio //
            // Ocultar microfone e mosrar ítens gravando
            $(".bt-recorder").click(function(){
                if( $("#myInterval").val() !== "0" ){ clearInterval($("#myInterval").val()); }

                startRecording();
                var myInterval = startTimer(0, $("#time"));
                $("#myInterval").val(myInterval);
                $("#gravando").val("1");
                $(".gravando").slideDown(200);
                $(".microfone").slideUp(200);
                $("#msg").prop("disabled", true);
            });
            // Reaparecer microfone ao clicar em Stop
            $(".bt-cancel").click(function(){
                if( $("#myInterval").val() !== "0" ){ clearInterval($("#myInterval").val()); }

                stopRecording()
                $("#gravando").val("9");
                $(".gravando").slideUp(200);
                $(".microfone").slideDown(200);
                $("#time").text("00:00");
                $("#msg").prop( "disabled", false );
                audioConfirmado = false;  // Áudio foi cancelado
                audioProcessado = false;  // Reset audio processado
                ehaudio = false;  // Resetar flag de áudio ao cancelar
                form = new FormData();  // Limpar form ao cancelar para evitar reutilização
            });
            // Reaparecer microfone ao clicar em Send
            $(".bt-send").click(function(){
                if( $("#myInterval").val() !== "0" ){ clearInterval($("#myInterval").val()); }
                
                stopRecording();
                $("#gravando").val("0");
                $(".gravando").slideUp(200);
                $(".microfone").slideDown(200);
                $("#time").text("00:00");
                $("#msg").prop( "disabled", false );
                $("#btnEnviar").attr("style", "display: block");
                audioConfirmado = true;  // Áudio foi confirmado - pronto para enviar ao pressionar Enter
            });
        // FIM Scripts referentes à Gravação de Áudio //

        // Calcula o Tempo de Gravação do Áudio //
            function startTimer(duration, display) {
                var timer = duration, minutes, seconds;
                
                var privateInterval = setInterval(function () {
                    minutes = parseInt(timer / 60, 10)
                    seconds = parseInt(timer % 60, 10);

                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    display.text(minutes + ":" + seconds);

                    if (++timer < 0) { timer = duration; }
                }, 1000);

                return privateInterval;
            }
        // Calcula o Tempo de Gravação do Áudio //

        // Copiando uma Imagem da Área de Transferência //
            var reader = new FileReader();

            reader.onload = function(result) {
                // Removendo as Tags <img> e <audio> //
                removeFile();

                // Tratamento dos Paineis //
                $("#dragDropImage").attr("style", "display:none");
                $(".panel-upImage").addClass("open");

                // Criando a Visualização da Imagem //
                let img = document.createElement("img");
                img.setAttribute("id", "imgView");
                img.src = result.target.result;
                document.getElementById("panel-upload-image").appendChild(img);
            }

            document.querySelector("body").onpaste = function(event){
                let items = event.clipboardData.items;

                for (itemIndex in items) {
                    let item = items[itemIndex];

                    if (item.kind == 'file') {
                        var file = item.getAsFile();
                        reader.readAsDataURL(file);

                        // Coloco a Imagem no formulário //
                        imageClipboard = true;
                        form.append("upload[]", file, "clipboard_image.png");
                       // form.append("upload", file, "clipboard_image.png");

                        // Habilitando o Envio da Imagem e Bloqueando as demais Opções //
                            $("#btnEnviar").attr("style", "display: block");
                            $("#divAudio").attr("style", "display: none");
                            $("#lnkRespostaRapida").removeAttr( "onclick");
                            $("#btnViewEmojs").prop( "disabled", true );
                        // FIM Habilitando o Envio da Imagem e Bloqueando as demais Opções //
                    }
                }
            }
        // FIM Copiando uma Imagem da Área de Transferência //

        // Cancelando o Envio da Imagem //
            $('#cancelaUploadImagem').click(function() {
                cancelaUploadImageClipboard();
            });

            function cancelaUploadImageClipboard() {
                $(".panel-upImage").removeClass('open');
                $("#btnEnviar").attr("style", "display: none");
                $("#divAudio").attr("style", "display: block");
                $("#lnkRespostaRapida").attr( "onclick", "abrirModal('#modalRespostaRapida')" );
                $("#btnViewEmojs").prop( "disabled", false );
                $("#msg").prop( "disabled", false );
                $("#upload").val("");
                $("#imgDragDrop").val("");
                ehupload = false;

                // Removendo as Tags <img> e <audio> //
                removeFile();
            }
        // FIM Cancelando o Envio da Imagem //

        // Drag & Drop Image //
            function readFile(input) {   
                //Tento pegar Multiplos Anexos aqui
                for (var i = 0; i < input.files.length; i++) {
                
                
                 if (input.files && input.files[i]) {
                    var arquivo = input.files[i];
                    var fileName = arquivo.name;
                    var fileExtension = fileName.slice((fileName.lastIndexOf(".") - 1 >>> 0) + 2); // obtém a extensão do arquivo
                    


                    $(".panel-upImage").addClass("open");
                    $("#btnEnviar").attr("style", "display: block");
                    $("#divAudio").attr("style", "display: none");

                    // Criando o Elemento <img> //
                        var imageClipboard = true;
                        var reader = new FileReader();
                        var img = document.createElement("img");
                        img.setAttribute("id", "imgView");
                            
                        reader.onload = function(file) {
                            if( fileExtension === "jpeg"
                                || fileExtension === "jpg"
                                || fileExtension === "png"
                                || fileExtension === "gif" ){
                                img.src = file.target.result;
                            }
                            else if( fileExtension === "pdf" ){
                                img.src = "images/abrir_pdf.png";
                            }
                            else if( fileExtension === "doc"
                                || fileExtension === "docx" ){
                                img.src = "images/abrir_doc.png";
                            }
                            else if( fileExtension === "xls"
                                || fileExtension === "xlsx"
                                || fileExtension === "csv" ){
                                img.src = "images/abrir_xls.png";
                            }
                            else{ img.src = "images/abrir_outros.png"; }                            
                        };                    
                        
                        reader.readAsDataURL(arquivo);
                      //   alert(fileName);      
                        // Coloco a Imagem no formulário //
                        ehupload = true;
                        form.append("upload[]", arquivo, fileName);
                        $("#dragDropImage").attr("style", "display:none");
                        document.getElementById("panel-upload-image").appendChild(img);
					    $("#msg").focus();
                    // FIM Criando o Elemento <img> //
                }



             //   console.log(input.files[i]);
                }   

                if (input.files.length>1){
                   //FAço o envio direto se possuir mais de 1 arquivo
                   $("#btnEnviar").click(); //FAço o envio dos anexos sem exibir na tela
                }
               
                

             /*   
                
                if (input.files && input.files[0]) {
                    var fileExtension = ($("#imgDragDrop").val()).split(".");
                    fileExtension = fileExtension[fileExtension.length-1];
                    var fileName = ($("#imgDragDrop").val()).split("\\");
                    fileName = fileName[fileName.length-1];
                    $(".panel-upImage").addClass("open");
                    $("#btnEnviar").attr("style", "display: block");
                    $("#divAudio").attr("style", "display: none");

                    // Criando o Elemento <img> //
                        var imageClipboard = true;
                        var reader = new FileReader();
                        var img = document.createElement("img");
                        img.setAttribute("id", "imgView");
                            
                        reader.onload = function(file) {
                            if( fileExtension === "jpeg"
                                || fileExtension === "jpg"
                                || fileExtension === "png"
                                || fileExtension === "gif" ){
                                img.src = file.target.result;
                            }
                            else if( fileExtension === "pdf" ){
                                img.src = "images/abrir_pdf.png";
                            }
                            else if( fileExtension === "doc"
                                || fileExtension === "docx" ){
                                img.src = "images/abrir_doc.png";
                            }
                            else if( fileExtension === "xls"
                                || fileExtension === "xlsx"
                                || fileExtension === "csv" ){
                                img.src = "images/abrir_xls.png";
                            }
                            else{ img.src = "images/abrir_outros.png"; }                            
                        };                    
                        
                        reader.readAsDataURL(input.files[0]);

                        // Coloco a Imagem no formulário //
                        ehupload = true;
                        form.append("upload", input.files[0], fileName);
                        $("#dragDropImage").attr("style", "display:none");
                        document.getElementById("panel-upload-image").appendChild(img);
					    $("#msg").focus();
                    // FIM Criando o Elemento <img> //
                }
                */
            }
            
            // Instancia a Leitura do Arquivo /
            $(".dropzone").change(function() {
                readFile(this);
            });

            //Testando pra abrir pra selecionar arquivo
            $("#dragDropImage").click(function() {
                //Se for mobile eu forço a abertura para selecionar arquivos
                var larguradatela = $(window).width();
                if (larguradatela < 801){ //Se a tela for menor qu 800pixels minimizo os atendimentos para ficar mais responsivo
                   var input = document.getElementById('imgDragDrop');                 
                    input.click();   
                }                                 
            });
            

            // Ativa a Área de Drag & Drop //
                var counter = 0;

                $('#AtendimentoAberto').bind({
                    // Abre o Painel //
                    dragenter: function() {
                        counter++;

                        // Removendo as Tags <img> e <audio> //
                        removeFile();

                        // Tratamento dos Paineis //
                        $(".panel-upImage").addClass("open");
                        $("#dragDropImage").attr("style", "display:block");
                    },
                    // Fecha o Painel //
                    dragleave: function() {
                        counter--;

                        if( counter === 0 ){
                            // Removendo as Tags <img> e <audio> //
                            removeFile();

                            // Abrindo o Painel de Visualização do Arquivo //
                            $(".panel-upImage").removeClass('open');
                        }
                    }
                });
            // Ativa a Área de Drag & Drop //
        // FIM Drag & Drop Image //

        // Removendo o Arquivo antes de Enviar //
            function removeFile() {
                var imgView = document.getElementById("imgView");
                var audView = document.getElementById("audioView");
                if( imgView !== null ){ imgView.remove(); }
                if( audView !== null ){ audView.remove(); }
            }
        // FIM Removendo o Arquivo antes de Enviar //

        // Carregando a Modal de Respostas Rápidas //
        $("#lnkRespostaRapida").on("click", function() {});  
                
        removeFile();

        $(".panel-upImage").removeClass('open');
        $("#btnEnviar").attr("style", "display: none");
        $("#divAudio").attr("style", "display: block");
        $("#lnkRespostaRapida").attr( "onclick", "abrirModal('#modalRespostaRapida')" );
        $("#btnViewEmojs").prop( "disabled", false );
        $("#msg").prop( "disabled", false );
        $("#upload").val("");
        $("#imgDragDrop").val("");

         // Habilita Aba de Contatos para seleção e Envio //
         $("#btnFotografar").click( function(e){
            e.preventDefault();
           
               

            (function () {
          //      if (
          //          !"mediaDevices" in navigator ||
          //          !"getUserMedia" in navigator.mediaDevices
          //      ) {
          //          alert("Camera API is not available in your browser");
          //          return;
          //      }
       
           
            var video = document.querySelector("#video");
            var btnChangeCamera = document.querySelector("#btnChangeCamera");
            var canvas = document.querySelector("#canvas");
            var context = canvas.getContext('2d');
            var btnScreenshot = document.querySelector("#btnScreenshot");

            function clearCanvas(context, canvas) {
                context.clearRect(0, 0, canvas.width, canvas.height);
                var w = canvas.width;
                canvas.width = 1;
                canvas.width = w;
                }
            clearCanvas(context, canvas) ;

          
                
            
            // video constraints
            var constraints = {
                video: {
                width: {
                    min: 1280,
                    ideal: 1920,
                    max: 2560,
                },
                height: {
                    min: 720,
                    ideal: 1080,
                    max: 1440,
                },
                },
            };
           
            
            // use front face camera
            let useFrontCamera = false;

            // current video stream
            let videoStream;     

           

              // take screenshot
            btnScreenshot.addEventListener("click", function () {
                let img = document.createElement("img");  
                img.setAttribute("class", "imgView"); 
                
                // Redimensiona canvas para melhor compressão
                canvas.width = Math.min(video.videoWidth, 1280);
                canvas.height = Math.min(video.videoHeight, 720);
                
                canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
                // Converte para JPEG com compressão 0.8 (80% qualidade - reduz tamanho mantendo qualidade)
                img.src = canvas.toDataURL("image/jpeg", 0.8);

                
    
            //    screenshotsContainer.prepend(img);               
                $(".panel-upImage").addClass("open");
                $("#btnEnviar").attr("style", "display: block");
                $("#divAudio").attr("style", "display: none");

                imageCamera = true;
                
                // Obtém a base64 diretamente
                let base64String = img.src;
                
                // Limpa o form anterior
                form.delete("upload");
                form.delete("imageBase64");
                
                // Adiciona a imagem em base64 como string
                form.append("imageBase64", base64String);
                
                $(".panel-upImage").addClass("open");               
                document.getElementById("panel-upload-image").appendChild(img);				
                stopVideoStream();

                  
                  // FIM Habilitando o Envio da Imagem e Bloqueando as demais Opções //          
           
                 $('#mdlTiraFoto').modal('hide');
                 
                 // Auto-enviar foto se configurado
                 if( $('#parametrosEnvioFotoAuto').val() === "1" ){
                    console.log("Enviando foto automaticamente...");
                    
                    var formAutoSendFoto = new FormData();
                    formAutoSendFoto.append('numero', $("#s_numero").val());
                    formAutoSendFoto.append('id_atendimento', $("#s_id_atendimento").val());
                    formAutoSendFoto.append('nome', $("#s_nome").val());
                    formAutoSendFoto.append('id_canal', $("#s_id_canal").val());
                    formAutoSendFoto.append('msg', $("#msg").val());
                    formAutoSendFoto.append('Resposta', $("#RespostaSelecionada").html());
                    formAutoSendFoto.append('idResposta', $("#chatid_resposta").val());
                    formAutoSendFoto.append('anexomsgRapida', $("#anexomsgRapida").val());
                    formAutoSendFoto.append('nomeanexomsgRapida', $("#nomeanexomsgRapida").val());
                    formAutoSendFoto.append('imageBase64', base64String);
                    
                    $.ajax({
                        url: 'atendimento/gravarMensagem.php',
                        data: formAutoSendFoto,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        success: function(retorno) {
                            console.log("Foto enviada com sucesso. Resposta:", retorno);
                            
                            form = new FormData();
                            imageCamera = false;
                            imageClipboard = false;
                            
                            $("#msg").val("");
                            $("#anexomsgRapida").val("0");
                            $("#RespostaSelecionada").html("");
                            $("#chatid_resposta").val("");
                            $("#upload").val("");
                            $("#imgDragDrop").val("");
                            
                            removeFile();
                            cancelaUploadImageClipboard();
                            
                            console.log("Flags resetados apos auto-send de foto");
                            
                            $("#divAudio").attr("style", "display: block");
                            $("#msg").focus();
                            
                            setTimeout(function() {
                                carregaAtendimento();
                            }, 500);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Erro ao enviar foto:", textStatus);
                            alert("Erro ao enviar foto.");
                        }
                    });
                 } else {
                    $("#msg").focus();
                 }
                 return false;
                 
            });
                                

            // switch camera
            btnChangeCamera.addEventListener("click", function () {
                useFrontCamera = !useFrontCamera;

                initializeCamera();
            });

              // stop video stream
                function stopVideoStream() {
                    if (videoStream) {
                    videoStream.getTracks().forEach((track) => {
                        track.stop();
                    });
                    }
                }

             


            
            // initialize
                async function initializeCamera() {
                    stopVideoStream();
                    constraints.video.facingMode = useFrontCamera ? "user" : "environment";

                    try {
                    videoStream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = videoStream;

                    $('#mdlTiraFoto').modal('show');
             
                    } catch (err) {
                      alert("Não é possivel acessar a camera!");
                      return false;
                    }
                }
                
               
                initializeCamera();
                 
            })();           

            

   

        });

        // FIX: Atualizar timestamp do usuário a cada 2 minutos para manter online
        // Isso mantém o atendente marcado como online enquanto está atendendo
        setInterval(function() {
            $.ajax({
                url: "../../cadastros/usuarios/gravaTimestamp.php",
                type: "GET",
                timeout: 5000,
                error: function(jqXHR, textStatus, errorThrown) {
                    // Silenciosamente falha se houver erro - não interfere na aplicação
                    console.debug("Timestamp update failed (this is normal): " + textStatus);
                }
            });
        }, 120000); // 120 segundos = 2 minutos

    });
</script>
