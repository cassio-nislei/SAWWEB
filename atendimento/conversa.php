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

<script src="js/WebAudioRecorder.min.js"></script>
<script src="js/WebAudioRecorderMp3.min.js"></script>
<script>
    $(document).ready(function() {
        // Cancela o envio de Imagem via Área  de Transferência //
            $(document).keyup(function(event) { 
                if( event.keyCode === 27 ){ //Iniciar Gravação
                    cancelaUploadImageClipboard();
                }
            });
        // FIM Cancela o envio de Imagem via Área  de Transferência //

        var ehaudio = false;
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

            $.post("atendimento/qtdConversas.php", {
                numero: numero,
                id: id,
                id_canal: id_canal
            }, function(retorno) {
                console.log('QTD Conversas'+retorno);
                //Válida se é para Atualizar a conversa, só faz a atualização da tela se existirem novas mensagens
                if (parseInt(retorno) > parseInt(qtdMensagens)) {
                    $.ajax("atendimento/listaConversas.php?id=" + id + "&id_canal=" + id_canal + "&numero=" + numero + "&nome=" + nome).done(function(data) {
                          $('#mensagens').html(data);
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

        // Gravação de Áudio via MP3 //
			//webkitURL is deprecated but nevertheless
            URL = window.URL || window.webkitURL;

            var gumStream; 						//stream from getUserMedia()
            var recorder; 						//WebAudioRecorder object
            var input; 							//MediaStreamAudioSourceNode  we'll be recording
            var encodingType; 					//holds selected encoding for resulting audio (file)
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
                    /*
                        create an audio context after getUserMedia is called
                        sampleRate might change after getUserMedia is called, like it does on macOS when recording through AirPods
                        the sampleRate defaults to the one set in your OS for your playback device

                    */
                    audioContext = new AudioContext();

                    //assign to gumStream for later use
                    gumStream = stream;
                    
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
                        onEncoderLoading: function(recorder, encoding) {},
                        onEncoderLoaded: function(recorder, encoding) {}
                    });

                    recorder.onComplete = function(recorder, blob) {
                        createDownloadLink(blob,recorder.encoding);
                        encodingTypeSelect.disabled = false;
                    }

                    recorder.setOptions({
                        timeLimit:120,
                        encodeAfterRecord:encodeAfterRecord,
                        ogg: {quality: 0.5},
                        mp3: {bitRate: 160}
                    });

                    //start the recording process
                    recorder.startRecording();
                })
                .catch(function(err) {});
            }

            function stopRecording() {
                //stop microphone access
                gumStream.getAudioTracks()[0].stop();

                //tell the recorder to finish the recording (stop recording + encode the recorded audio)
                recorder.finishRecording();
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
                
                if( $('#parametrosEnvioAudioAut').val() === "1" ){
                    $("#btnEnviar").click();
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
                $(".gravando").toggle();
                $(".bt-recorder").toggle();
                $("#msg").prop("disabled", true);
            });
            // Reaparecer microfone ao clicar em Stop
            $(".bt-cancel").click(function(){
                if( $("#myInterval").val() !== "0" ){ clearInterval($("#myInterval").val()); }

                stopRecording()
                $("#gravando").val("9");
                $(".gravando").toggle();
                $(".bt-recorder").toggle();
                $("#time").text("00:00");
                $("#msg").prop( "disabled", false );
            });
            // Reaparecer microfone ao clicar em Send
            $(".bt-send").click(function(){
                if( $("#myInterval").val() !== "0" ){ clearInterval($("#myInterval").val()); }
                
                stopRecording();
                $("#gravando").val("0");
                $(".gravando").toggle();
                $(".bt-recorder").toggle();
                $("#time").text("00:00");
                $("#msg").prop( "disabled", false );
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
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext("2d").drawImage(video, 0, 0);
                img.src = canvas.toDataURL("image/png");

                
    
            //    screenshotsContainer.prepend(img);               
                $(".panel-upImage").addClass("open");
                $("#btnEnviar").attr("style", "display: block");
                $("#divAudio").attr("style", "display: none");

                imageCamera = true;
                 //Converto a Imagem para Blob
                 // Defina o base64
                let base64 = img.src;
                // Remova o cabeçalho do tipo de arquivo do base64
                let byteString = atob(base64.split(",")[1]);
                // Crie um ArrayBuffer
                let buffer = new ArrayBuffer(byteString.length);
                // Crie uma exibição de byte do ArrayBuffer
                let view = new Uint8Array(buffer);
                // Preencha a exibição de byte com os dados do byteString
                for (let i = 0; i < byteString.length; i++) {
                view[i] = byteString.charCodeAt(i);
                }
                let blob = new Blob([buffer], { type: "image/png" });  
                //Fim da Conversão da Imagem para BLOBO               
          
                  
                // Tratamento dos Paineis //
                $("#dragDropImage").attr("style", "display:none");
                $(".panel-upImage").addClass("open");
                form.delete("upload"); //Adicionei esse delete para caso já exista um upload no form deletar                
                form.append("upload", blob ,"imagem_camera.png"); 
                
                $("#dragDropImage").attr("style", "display:none");               
                document.getElementById("panel-upload-image").appendChild(img);				
                stopVideoStream();

                  
                  // FIM Habilitando o Envio da Imagem e Bloqueando as demais Opções //          
           
                 $('#mdlTiraFoto').modal('hide');         
                 $("#msg").focus();                 
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




    });
</script>