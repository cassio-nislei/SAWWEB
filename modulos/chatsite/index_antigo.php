<?php
// Session already started in padrao.inc.php when accessed via gestao/index.php
// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
    ?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.form.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <title>SAW CHAT</title>
    <style>

      .stretch-card>.card {
    width: 100%;
    min-width: 100%
}

body {
    background-color: #f9f9fa;
    padding:0;
    margin:0;
}

.flex {
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto
}

@media (max-width:991.98px) {
    .padding {
        padding: 1.5rem
    }
}

@media (max-width:767.98px) {
    .padding {
        padding: 1rem
    }
}

.padding {
    padding: 3rem
}

.box.box-warning {
    border-top-color: #f39c12;
}

.box {
    position: relative;
    border-radius: 3px;
    background: #ffffff;
    border-top: 3px solid #d2d6de;
    margin-bottom: 20px;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}
.box-header.with-border {
    border-bottom: 1px solid #f4f4f4
}

.box-header.with-border {
    border-bottom: 1px solid #f4f4f4;
}

.box-header {
    color: #444;
    display: block;
    padding: 10px;
    position: relative;
}

.box-header:before, .box-body:before, .box-footer:before, .box-header:after, .box-body:after, .box-footer:after {
    content: " ";
    display: table;
}

.box-header {
    color: #444;
    display: block;
    padding: 10px;
    position: relative
}

.box-header>.fa, .box-header>.glyphicon, .box-header>.ion, .box-header .box-title {
    display: inline-block;
    font-size: 18px;
    margin: 0;
    line-height: 1;
}

.box-header>.box-tools {
    position: absolute;
    right: 10px;
    top: 5px;
}

.box-header>.box-tools [data-toggle="tooltip"] {
    position: relative;
}

.bg-yellow, .callout.callout-warning, .alert-warning, .label-warning, .modal-warning .modal-body {
    background-color: #f39c12 !important;
}

.bg-yellow{
        color: #fff !important;
}

.btn {
    border-radius: 3px;
    -webkit-box-shadow: none;
    box-shadow: none;
    border: 1px solid transparent;
}

.btn-box-tool {
    padding: 5px;
    font-size: 12px;
    background: transparent;
    color: #97a0b3;
}

.direct-chat .box-body {
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
    position: relative;
    overflow-x: hidden;
    padding: 0;
}

.box-body {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    padding: 10px;
}
.box-header:before, .box-body:before, .box-footer:before, .box-header:after, .box-body:after, .box-footer:after {
    content: " ";
    display: table;
}

.direct-chat-messages {
    -webkit-transform: translate(0, 0);
    -ms-transform: translate(0, 0);
    -o-transform: translate(0, 0);
    transform: translate(0, 0);
    padding: 10px;
    height: 250px;
    overflow: auto;
}

.direct-chat-messages, .direct-chat-contacts {
    -webkit-transition: -webkit-transform .5s ease-in-out;
    -moz-transition: -moz-transform .5s ease-in-out;
    -o-transition: -o-transform .5s ease-in-out;
    transition: transform .5s ease-in-out;
}



.direct-chat-msg {
    margin-bottom: 10px;
}

.direct-chat-msg, .direct-chat-text {
    display: block;
}

.direct-chat-info {
    display: block;
    margin-bottom: 2px;
    font-size: 12px;
}

.direct-chat-timestamp {
    color: #999;
}

.btn-group-vertical>.btn-group:after, .btn-group-vertical>.btn-group:before, .btn-toolbar:after, .btn-toolbar:before, .clearfix:after, .clearfix:before, .container-fluid:after, .container-fluid:before, .container:after, .container:before, .dl-horizontal dd:after, .dl-horizontal dd:before, .form-horizontal .form-group:after, .form-horizontal .form-group:before, .modal-footer:after, .modal-footer:before, .modal-header:after, .modal-header:before, .nav:after, .nav:before, .navbar-collapse:after, .navbar-collapse:before, .navbar-header:after, .navbar-header:before, .navbar:after, .navbar:before, .pager:after, .pager:before, .panel-body:after, .panel-body:before, .row:after, .row:before {
    display: table;
    content: " ";
}

.direct-chat-img {
    border-radius: 50%;
    float: left;
    width: 40px;
    height: 40px;
}

.direct-chat-text {
    border-radius: 5px;
    position: relative;
    padding: 5px 10px;
    background: #d2d6de;
    border: 1px solid #d2d6de;
    margin: 5px 0 0 50px;
    color: #444;
}

.direct-chat-msg, .direct-chat-text {
    display: block;
}

.direct-chat-text:before {
    border-width: 6px;
    margin-top: -6px;
}

.direct-chat-text:after, .direct-chat-text:before {
    position: absolute;
    right: 100%;
    top: 15px;
    border: solid transparent;
    border-right-color: #d2d6de;
    content: ' ';
    height: 0;
    width: 0;
    pointer-events: none;
}

.direct-chat-text:after {
    border-width: 5px;
    margin-top: -5px;
}

.direct-chat-text:after, .direct-chat-text:before {
    position: absolute;
    right: 100%;
    top: 15px;
    border: solid transparent;
    border-right-color: #d2d6de;
    content: ' ';
    height: 0;
    width: 0;
    pointer-events: none;
}

:after, :before {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.direct-chat-msg:after {
    clear: both;
}

.direct-chat-msg:after {
    content: " ";
    display: table;
}

.direct-chat-info {
    display: block;
    margin-bottom: 2px;
    font-size: 12px;
}

.right .direct-chat-img {
    float: right;
}

.direct-chat-warning .right>.direct-chat-text {
    background: #f39c12;
    border-color: #f39c12;
    color: #fff;
}

.right .direct-chat-text {
    margin-right: 50px;
    margin-left: 0;
}

.box-footer {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 3px;
    border-bottom-left-radius: 3px;
    border-top: 1px solid #f4f4f4;
    padding: 10px;
    background-color: #fff;
}

.box-header:before, .box-body:before, .box-footer:before, .box-header:after, .box-body:after, .box-footer:after {
    content: " ";
    display: table;
}


.input-group-btn {
    position: relative;
    font-size: 0;
    white-space: nowrap;
}

.input-group-btn:last-child>.btn, .input-group-btn:last-child>.btn-group {
    z-index: 2;
    margin-left: -1px;
}

.btn-warning {
    color: #fff;
    background-color: #f0ad4e;
    border-color: #eea236;
} 






    </style>
  </head>
  <body>
  <div class="page-content page-container" id="page-content">
    <div class="row container d-flex justify-content-center">
      <div class="col-md-4">
             
              <div class="box box-warning direct-chat direct-chat-warning">
                <div class="box-header with-border">
    <?php
    if (!isset($_SESSION["chat"]["nome"])){
      echo '
      <h3 class="box-title">Novo Atendimento</h3>
      <form action="#" method="post">
      <div class="input-group">
        <input type="text" id="nome" name="nome" placeholder="Informe seu nome ..." class="form-control">
        <span class="input-group-btn">
              <button id="btnIniciarAtendimeto" type="button" class="btn btn-warning btn-flat">INICIAR</button>
            </span>
      </div>
    </form> ';

    

    }else{
    ?>

                  <h3 class="box-title">Mensagens</h3>

                  <div class="box-tools pull-right">
                    <span data-toggle="tooltip" title="" class="badge bg-yellow"  id="TotalMensagens" data-original-title="3 New Messages">0</span>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Contacts">
                      <i class="fa fa-comments"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
              
                <div class="box-body">
                  
                  <div class="direct-chat-messages" id="ListarMensagens">             
                 
                  </div>
                  <!-- Hidden inputs for JavaScript polling -->
                  <input type="hidden" id="s_numero" value="<?php echo isset($_SESSION["chat"]["numero"]) ? $_SESSION["chat"]["numero"] : ''; ?>">
                  <input type="hidden" id="s_id_atendimento" value="<?php echo isset($_SESSION["chat"]["id_atendimento"]) ? $_SESSION["chat"]["id_atendimento"] : ''; ?>">
                  <input type="hidden" id="s_nome" value="<?php echo isset($_SESSION["chat"]["nome"]) ? $_SESSION["chat"]["nome"] : ''; ?>">
                </div>
               
                <div class="box-footer">
                  <form action="#" method="post">
                    <div class="input-group">
                    <textarea name="mensagem" id="mensagem" placeholder="Escreva aqui sua mensagem ..." class="form-control" style="height: 40px;"></textarea>
                      <span class="input-group-btn">
                        <input type="hidden" name="numero" id="numero" value="<?php echo $_SESSION["chat"]["numero"]; ?>">
                            <button type="button" id="btnGravarMensagem" class="btn btn-warning btn-flat">ENVIAR</button>
                          </span>
                    </div>
                  </form>
                </div>
                <div class="row container d-flex justify-content-center">
                <a href="sair.php"><span data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="3 New Messages">Sair</span></a>
                
              </div>
             
              </div>
              
           
         
      <?php
     } //Fim da verificação se Tem usuario na Sessão
    ?>
       </div>
     </div>
  </div>

 <!-- Bootstrap -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {
    
          $("#btnIniciarAtendimeto").click(function(e){
             e.preventDefault();
             var nome = $("#nome").val();
             if ($("#nome").val()==''){               
               return false;
             }
             $("#btnIniciarAtendimeto").html("Iniciando...");
             $("#btnIniciarAtendimeto").attr("disabled",true); 
             $.post("../modulos/chatsite/iniciaAtendimento.php", {nome:nome},function(retorno){
          //    alert(retorno);
               if (retorno==1){
                location.reload(true);
               } 
               $("#btnIniciarAtendimeto").html("INICIAR");
               $("#btnIniciarAtendimeto").attr("disabled",false)
             })
          })


          // Processa e Submita os Dados digitados no campo de Mensagem //
          function processaMensagem(event){
                    var strMensagem = $.trim($("#mensagem").val());
                    var eventCodes = ",8,9,13,16,17,18,20,27,32,33,34,35,36,37,38,39,40,45,46,91,93,112,113,114,115,116,117,118,119,120,121,122,123,144,173,174,175,";
                    var padrao = ","+event.keyCode+",";
                    var regex = new RegExp(padrao);

                    if( strMensagem.length === 0 && !regex.test(eventCodes) ){
                     //   $("#btnGravarMensagem").attr("style", "display: block");
                     //   $("#divAudio").attr("style", "display: none");
                    }
                    else if( ( strMensagem.length === 1 && event.key === "Backspace" )
                        || strMensagem === "" ){
                   //     $("#btnGravarMensagem").attr("style", "display: none");
                      //  $("#divAudio").attr("style", "display: block");
                    }
                    
                    // Permitir quando pressionar <Shift> e <Enter>	//
                        if( event.keyCode == 13 && event.shiftKey ){
                            var content = $("#mensagem").val();
                            var caret = getCaret(this);
                            this.value = content.substring(0,caret) 
                                            + "\n" 
                                            + content.substring(caret,content.length-1);
                            event.stopPropagation();
                        }
                        else if( event.keyCode == 13 ){
                            // Submita os Dados //	 
                            event.preventDefault();
                            $("#mensagem").focus();
                            $("#btnGravarMensagem").click();
                          //  $("#btnGravarMensagem").attr("style", "display: none");
                          //  $("#divAudio").attr("style", "display: block");
                            return false;
                        }
                    // FIM Permitir quando pressionar <Shift> e <Enter>	//
                }

                $("#mensagem").keydown(function(event) { processaMensagem(event); });

          //GRava a nova mensagem
          $("#btnGravarMensagem").click(function(e){
             e.preventDefault();
             var mensagem = $("#mensagem").val();
             if ($("#mensagem").val()==''){               
               return false;
             }
             $("#btnGravarMensagem").html("Enviando...");
             $("#btnGravarMensagem").attr("disabled",true); 
             $.post("../modulos/chatsite/gravaMensagem.php", {mensagem:mensagem},function(retorno){
             // alert(retorno);
               if (retorno==1){
                $("#mensagem").val("");
                if ( $( "#ListarMensagens" ).length ) { 
                     $( "#ListarMensagens" ).load( "../modulos/chatsite/listaMensagens.php" );
                 }
               } 
               $("#btnGravarMensagem").html("ENVIAR");
               $("#btnGravarMensagem").attr("disabled",false)
             })
          })
          


          if ( $( "#ListarMensagens" ).length ) { 
            $( "#ListarMensagens" ).load( "../modulos/chatsite/listaMensagens.php" );
          }

          function ajustaScroll(){	
            $('#ListarMensagens').animate({
                scrollTop: $(this).height()*100 // aqui introduz o numero de px que quer no scroll, neste caso é a altura da propria div, o que faz com que venha para o fim
            }, 100);
        }

        ajustaScroll();

          function atualizaQtdMensagens() {
            var numero       = $("#s_numero").val();
            var id           = $("#s_id_atendimento").val();
            var qtdMensagens = $("#TotalMensagens").text();
            var nome         = encodeURIComponent($("#s_nome").val());

            $.post("../modulos/chatsite/qtdnovasmensagens.php", {
                numero: numero,
                id: id
            }, function(retorno) {
                //Válida se é para Atualizar a conversa, só faz a atualização da tela se existirem novas mensagens
                if (parseInt(retorno) > parseInt(qtdMensagens)) {
                    if ( $( "#ListarMensagens" ).length ) { 
                         $( "#ListarMensagens" ).load( "../modulos/chatsite/listaMensagens.php" );
                     
                   }

                   ajustaScroll(); //desço a barra de rolagem da conversa
                }
                $("#TotalMensagens").html(retorno);
            });
        }

        // Atualiza a Lista de Atendimentos //
            var intervalo = setInterval(function() { atualizaQtdMensagens(); }, 5000);
            atualizaQtdMensagens();
        // FIM Atualiza a Lista de Atendimentos //


        });
    </script> 
  </body>
</html>