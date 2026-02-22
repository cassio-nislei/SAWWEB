<?php 
require_once("includes/padrao.inc.php"); 
if (!isset($_SESSION["usuariosaw"])){
    header("Location: index.php");
  }

// Helper function para acesso seguro a SESSION
function safe_session($key1, $key2 = null, $default = '') {
    if ($key2 === null) {
        return isset($_SESSION[$key1]) ? $_SESSION[$key1] : $default;
    }
    return isset($_SESSION[$key1][$key2]) ? $_SESSION[$key1][$key2] : $default;
}
//echo 'Protocolo '.safe_session("parametros", "usar_protocolo");
?>
<html class="js adownload cssanimations csstransitions webp webp-alpha webp-animation webp-lossless wf-roboto-n4-active wf-opensans-n4-active wf-opensans-n6-active wf-roboto-n3-active wf-roboto-n5-active wf-active" dir="ltr" loc="pt-BR" lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo safe_session("parametros", "title", "SAW"); ?></title>
    <meta name="viewport" content="width=device-width">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/panel-fix.css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/uikit.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.form.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/js_modal.js"></script>
    <script src="js/uikit.min.js"></script>
    <script src="js/uikit-icons.min.js"></script>
    <script src="js/funcionalidade.js"></script>
    <script src="js/profile_foto_upload.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/notification.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Bootstrap 5 para WebChat -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Estilo para os menus de conversas com efeito WhatsApp */
        .RLfQR {
            border-radius: 8px;
            transition: all 0.2s ease;
            padding: 10px 12px;
            margin-bottom: 5px;
            cursor: pointer;
        }
        
        .RLfQR:hover {
            background-color: rgba(0, 0, 0, 0.08);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
            border-radius: 8px;
        }
        
        .RLfQR span {
            padding: 8px 0;
        }

        /* Estilo para o header do atendimento aberto */
        ._3AwwN {
            background-color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            padding: 15px !important;
            border-radius: 0 8px 0 0 !important;
        }

        /* Estilo para os ícones do header */
        ._3AwwN i {
            color: #333333 !important;
            transition: all 0.2s ease;
        }

        ._3AwwN i:hover {
            color: #128c7e !important;
        }

        /* Cores específicas para cada ícone */
        ._3AwwN #btnVoltarResponsivo {
            color: #333333 !important;
        }

        ._3AwwN #btnAlterarContato {
            color: #0066cc !important;
        }

        ._3AwwN #btnObsAtendimento {
            color: #00CED1 !important;
        }

        ._3AwwN .fa-history {
            color: #9370db !important;
        }

        ._3AwwN .fa-random {
            color: #ff9800 !important;
        }

        ._3AwwN #btnAnexar {
            color: #666666 !important;
        }

        ._3AwwN .btnFinalizarChat {
            color: #dc3545 !important;
        }

        /* ===== TOGGLE MENU ANIMAÇÃO ===== */
        html, body {
            width: 100% !important;
            height: 100% !important;
            overflow: visible !important;
        }

        ._1FKgS.app-wrapper-web {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
            position: relative !important;
        }

        #MenuLateral {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            transform: translateX(0) !important;
            opacity: 1 !important;
            flex: 0 0 360px !important;
            min-width: 360px !important;
            width: 360px !important;
            overflow: hidden !important;
            position: relative !important;
        }

        #MenuLateral.menu-hidden {
            transform: translateX(-100%) !important;
            opacity: 0 !important;
            pointer-events: none !important;
            flex: 0 !important;
            min-width: 0 !important;
            width: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Alinhar btnMinimuiConversas2 com margem esquerda */
        #btnMinimuiConversas2 {
            left: 0 !important;
        }

        /* Estilo para o webchatArea quando oculto */
        #webchatArea.webchat-hidden {
            transform: translateX(100%) !important;
            opacity: 0 !important;
            pointer-events: none !important;
            flex: 0 !important;
            min-width: 0 !important;
            width: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Alinhar btManipulaChat2 com margem direita - MOSTRAR CHAT (IDÊNTICO ao menu esquerdo) */
        #btManipulaChat2 {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        #btManipulaChat2:hover {
            background-color: #707577 !important;
            opacity: 1 !important;
        }

        #btManipulaChat {
            display: none !important;
            align-items: center !important;
            justify-content: center !important;
        }

        #btManipulaChat:hover {
            background-color: #707577 !important;
            opacity: 1 !important;
        }

        #btnManipulaChat, #btnManipulaChat2 {
            transition: all 0.3s ease !important;
        }

        /* Área principal expande quando menu está oculto */
        ._3q4NP._1Iexl {
            transition: flex 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            flex: 1 1 auto !important;
            min-width: 1px !important;
            width: 100% !important;
            max-width: none !important;
            overflow: hidden !important;
        }

        /* Garante que o container interno cabe nas dimensões */
        #app {
            width: 100% !important;
            height: 100% !important;
        }

        #app-content {
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: row !important;
        }

        #btnMinimuiConversas, #btnMinimuiConversas2 {
            transition: all 0.3s ease !important;
        }

        /* Webchat Area com Transição Suave - Painel Lateral Direito */
        #webchatArea {
            transition: flex 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease !important;
            flex: 0 0 350px !important;
            min-width: 350px !important;
            width: 350px !important;
            overflow: visible !important;
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            background: white !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Desktop - até 1200px */
        @media (max-width: 1200px) {
            #MenuLateral {
                flex: 0 0 300px !important;
                min-width: 300px !important;
                width: 300px !important;
            }

            #webchatArea {
                flex: 0 0 300px !important;
                min-width: 300px !important;
                width: 300px !important;
            }

            #webchatArea.webchat-hidden {
                flex: 0 0 0 !important;
                width: 0 !important;
                min-width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                border: 0 !important;
            }
        }

        /* Tablet - até 768px */
        @media (max-width: 768px) {
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
            }

            #MenuLateral {
                flex: 0 0 75vw !important;
                width: 75vw !important;
                min-width: 75vw !important;
                overflow: hidden !important;
            }

            #MenuLateral.menu-hidden {
                flex: 0 0 0 !important;
                width: 0 !important;
                min-width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                border: 0 !important;
            }

            /* Webchat em modo FULLSCREEN no tablet */
            #webchatArea {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                flex: 0 0 100vw !important;
                width: 100vw !important;
                min-width: 100vw !important;
                height: 100vh !important;
                overflow: hidden !important;
                z-index: 9999 !important;
            }

            #webchatArea.webchat-hidden {
                display: none !important;
                flex: 0 !important;
                width: 0 !important;
                min-width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                border: 0 !important;
            }

            ._1FKgS.app-wrapper-web {
                overflow-x: hidden !important;
                width: 100% !important;
            }

            #app {
                width: 100% !important;
            }

            #app-content {
                width: 100% !important;
            }

            ._3q4NP._1Iexl {
                flex: 1 1 auto !important;
                width: 100% !important;
                max-width: none !important;
            }
        }

        /* Mobile - até 480px */
        @media (max-width: 480px) {
            html, body {
                width: 100% !important;
                overflow-x: hidden !important;
            }

            #MenuLateral {
                flex: 0 0 85vw !important;
                width: 85vw !important;
                min-width: 85vw !important;
                overflow: hidden !important;
            }

            #MenuLateral.menu-hidden {
                flex: 0 0 0 !important;
                width: 0 !important;
                min-width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                border: 0 !important;
            }

            /* Webchat em modo FULLSCREEN no mobile */
            #webchatArea {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                flex: 0 0 100vw !important;
                width: 100vw !important;
                min-width: 100vw !important;
                height: 100vh !important;
                overflow: hidden !important;
                z-index: 9999 !important;
            }

            #webchatArea.webchat-hidden {
                display: none !important;
                flex: 0 !important;
                width: 0 !important;
                min-width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
                border: 0 !important;
            }

            ._1FKgS.app-wrapper-web {
                width: 100vw !important;
            }

            #app {
                width: 100vw !important;
            }

            #app-content {
                width: 100vw !important;
            }

            ._3q4NP._1Iexl {
                width: 100vw !important;
            }
        }
        ._2bXVy {
            background-color: #f0f0f0 !important;
            border-radius: 20px !important;
            padding: 8px 15px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1) !important;
            flex: 1 !important;
            min-height: 42px !important;
            max-height: 50px !important;
        }

        /* Estilo para o container interno da textarea */
        ._3F6QL {
            flex: 1 !important;
            display: flex !important;
            align-items: center !important;
            width: 100% !important;
        }

        ._3F6QL.type_msg {
            width: 100% !important;
        }

        /* Estilo para a textarea de mensagem */
        .type_msg {
            background-color: transparent !important;
            border: none !important;
            outline: none !important;
            resize: none !important;
            padding: 8px 0 !important;
            font-size: 15px !important;
            color: #111111 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
            width: 100% !important;
            box-shadow: none !important;
            max-height: 25px !important;
            line-height: 1.3 !important;
        }

        .type_msg::placeholder {
            color: #9ba5ab !important;
            opacity: 1 !important;
        }

        .type_msg:focus {
            outline: none !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Ajuste para o botão de envio */
        ._2lkdt {
            background-color: transparent !important;
            border: none !important;
            padding: 8px 10px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            flex: 0 0 auto !important;
        }

        ._2lkdt:hover svg {
            opacity: 0.7 !important;
        }

        ._2lkdt svg {
            width: 24px !important;
            height: 24px !important;
        }

        /* Estilo para os ícones de ações */
        .adjustIconsTalk {
            color: #128c7e !important;
            margin-right: 8px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .adjustIconsTalk:hover {
            transform: scale(1.1) !important;
            color: #075e54 !important;
        }

        .adjustFastAns {
            font-size: 18px !important;
            color: #128c7e !important;
        }

        .adjustFastAns:hover {
            color: #075e54 !important;
        }

        /* Estilo para o container de gravação de áudio */
        .audioIcons {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            justify-content: flex-start !important;
            flex: 0 0 auto !important;
        }

        .gravando {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            padding: 8px 15px !important;
            background-color: #dcf8c6 !important;
            border-radius: 15px !important;
            color: #128c7e !important;
            justify-content: center !important;
            margin: 8px 15px 8px 15px !important;
            width: calc(100% - 30px) !important;
        }

        .gravando i {
            color: #dc3545 !important;
            animation: pulse 1s infinite !important;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .audioIcons .microfone {
            display: flex !important;
            gap: 5px !important;
            justify-content: flex-start !important;
        }

        .audioIcons button {
            background-color: #e0f7fa !important;
            border: none !important;
            border-radius: 50% !important;
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            color: #128c7e !important;
        }

        .audioIcons button:hover {
            background-color: #b2ebf2 !important;
            transform: scale(1.05) !important;
        }

        .gravando .bt-cancel,
        .gravando .bt-send {
            background: none !important;
            width: auto !important;
            height: auto !important;
            padding: 0 5px !important;
            color: #128c7e !important;
        }

        .gravando .bt-cancel:hover,
        .gravando .bt-send:hover {
            background: none !important;
            transform: scale(1.15) !important;
        }

        /* Container de digitação com layout vertical */
        #divDigitacao {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            background-color: #ffffff !important;
            padding: 10px 15px !important;
            gap: 8px !important;
        }

        /* Container da linha de input */
        .containerInputMsg {
            display: flex !important;
            align-items: flex-end !important;
            gap: 10px !important;
            width: 100% !important;
        }

        /* Espaço para ícones de ações rápidas */
        #divDigitacao ._2uQuX {
            display: flex !important;
            gap: 5px !important;
            flex: 0 0 auto !important;
        }
    </style>
    <script>
        // Variáveis Globais para Persistência de Estado //
        var globalForm = new FormData();
        var globalEhaudio = false;
        var globalImageClipboard = false;
        var globalImageCamera = false;
        var globalEhupload = false;
        
        $(document).ready(function() {
            // Ativar carregamento do webchat
            $("#carregaWebChat").val("1");
            
            // Pesquisa de Contatos //
                // Carregando a Lista de Contatos, ao pesquisar um nome //
                $("#pesquisaContato").keyup(function() { 
                    atualizaContatos(); 
                });

                $("#etiqueta, #tipo_pesquisa").change(function(){
                    atualizaContatos(); 
                });

                // Carregando a Lista de Contatos, no click do icon de Contatos //
                $('#contatos-bt-lista').click(function () { atualizaContatos(); });
            // FIM Pesquisa de Contatos //

            function atualizaAtendimentos() {
                var id = $("#id_usuariologado").val();
                var qtdTriagem = $("#qtdTriagem").val();
                var qtdPendentes = $("#qtdPendentes").val();
                var qtdAtendendo = $("#qtdAtendendo").val();
                var qtdNewMsgTriagem = $("#qtdNewMsgTriagem").val();
                var qtdNewMsgPendentes = $("#qtdNewMsgPendentes").val();
                var qtdNewMsgAtendendo = $("#qtdNewMsgAtendendo").val();
                var perfilUsuario = $("#perfilUsuario").val();
                var qtdNovas = parseInt(qtdNewMsgTriagem)+parseInt(qtdNewMsgPendentes)+parseInt(qtdNewMsgAtendendo);

                // Atualiza o Title com a Quantidade de Mensagens //
                if( parseInt(qtdNovas) === 0 ){
                    $(document).attr("title", $("#parametrosTitle").val());
                }
                else{ $(document).attr("title", "("+qtdNovas+") " + $("#parametrosTitle").val()); }
                // FIM Atualiza o Title com a Quantidade de Mensagens //

                // Busco a QTD de Triagem //
                if( $("#qtdTriagem").length ){
                    $.post("atendimento/qtdTriagem.php", {
                        id: id
                    }, function(retorno) {
                        var auxQtdNewMsgTriagem = 0;

                        if( retorno.trim() != "0" ){
                            var qtde = retorno.split("#");
                            var find = /@/g;

                            if( find.test(qtde[1]) ){
                                var atendimentosNovasMsg = (qtde[1]).split("[@]");

                                for(let i = 0; i < (atendimentosNovasMsg.length)-1; i++) {
                                    var dados = (atendimentosNovasMsg[i]).split("[&]");

                                    $("#not" + dados[0]).html('<span class="OUeyt messages-count-new">' + dados[1] + '</span>');
                                    $("#msg" + dados[0]).html(dados[2]);

                                    // Verifica se não é Vazio //
                                    if( dados.length > 3 ){
                                        if( dados[3].trim() != "" ){ $("#hor"+dados[0]).html(dados[3].replace("_",":")); }
                                    }
                                }
                            }

                            retorno = qtde[0];
                            auxQtdNewMsgTriagem = (qtde[2] !== undefined) ? qtde[2] : 0;
                        }

                        if( ( parseInt(retorno) != parseInt(qtdTriagem) )
                            || ( parseInt(auxQtdNewMsgTriagem) != parseInt(qtdNewMsgTriagem) ) ){
                            $.ajax("atendimento/triagem.php").done(function(data) {
                                $('#ListaTriagem').html(data);
                            });

                            $("#qtdTriagem").val(retorno);
                            $("#qtdNewMsgTriagem").val(auxQtdNewMsgTriagem);
                        }
                    });
				}
                // FIM Busco a QTD de Triagem //
                
                // Busco a Qtd de Atendimentos Pendentes //
                if( $("#qtdPendentes").length ){
                    $.post("atendimento/qtdPendentes.php", {
                        id: id
                    }, function(retorno) {
                        var auxQtdNewMsgPendentes = 0;

                        if( retorno.trim() != "0" ){
                            var qtde = retorno.split("#");
                            var find = /@/g;

                            if( find.test(qtde[1]) ){
                                var atendimentosNovasMsg = (qtde[1]).split("[@]");

                                for(let i = 0; i < (atendimentosNovasMsg.length)-1; i++) {
                                    var dados = (atendimentosNovasMsg[i]).split("[&]");
                                    
                                    $("#not"+dados[0]).html('<span class="OUeyt messages-count-new">'+dados[1]+'</span>');
                                    $("#msg"+dados[0]).html(dados[2]);
                                    
                                    // Verifica se não é Vazio //
                                    if( dados[3].trim() != "" ){ $("#hor"+dados[0]).html(dados[3].replace("_",":")); }
                                }
                            }

                            retorno = qtde[0];
                            auxQtdNewMsgPendentes = (qtde[2] !== undefined) ? qtde[2] : 0;
                        }

                        if( ( parseInt(retorno) != parseInt(qtdPendentes) )
                            || ( parseInt(auxQtdNewMsgPendentes) != parseInt(qtdNewMsgPendentes) ) ){
                            $.ajax("atendimento/pendentes.php").done(function(data) {
                                $('#ListaPendentes').html(data);
                                notifyMe("img/uptalk-logo.png", "Notificação", "Nova mensagem aguardando atendimento!", "");
                            });

                            $("#qtdPendentes").val(retorno);
                            $("#qtdNewMsgPendentes").val(auxQtdNewMsgPendentes);
                        }
                    });
                }
                // FIM Busco a Qtd de Atendimentos Pendentes //

                // Conversas em Atendimento //
                if( $("#qtdAtendendo").length ){
                    $.post("atendimento/qtdAtendendo.php", {
                        id: id
                    }, function(retorno) {
                        var auxQtdNewMsgAtendendo = 0;

                        if( retorno.trim() != "0" ){
                            var qtde = retorno.split("#");
                            var find = /@/g;

                            if( find.test(qtde[1]) ){
                                var atendimentosNovasMsg = (qtde[1]).split("[@]");

                                for(let i = 0; i < (atendimentosNovasMsg.length)-1; i++) {
                                    var dados = (atendimentosNovasMsg[i]).split("[&]");
                                    
                                    $("#not"+dados[0]).html('<span class="OUeyt messages-count-new">'+dados[1]+'</span>');
                                    $("#msg"+dados[0]).html(dados[2]);

                                    // Verifica se não é Vazio //
                                    if( dados[3].trim() != "" ){ $("#hor"+dados[0]).html(dados[3].replace("_",":")); }
                                }
                            }

                            retorno = qtde[0];
                            auxQtdNewMsgAtendendo = (qtde[2] !== undefined) ? qtde[2] : 0;
                        }
                        
                        if( ( parseInt(retorno) != parseInt(qtdAtendendo) )
                            || ( parseInt(auxQtdNewMsgAtendendo) != parseInt(qtdNewMsgAtendendo) ) ){
                            $.ajax("atendimento/atendendo.php").done(function(data) {
                                $('#ListaEmAtendimento').html(data);
                            });
                            $("#qtdAtendendo").val(retorno);
                            $("#qtdNewMsgAtendendo").val(auxQtdNewMsgAtendendo);
                        }
                    });
                }
                // FIM Conversas em Atendimento //
            }

            // Atualiza a Lista de Atendimentos //
                var intervalo = setInterval(function() { atualizaAtendimentos(); }, 15000);
                atualizaAtendimentos();
            // FIM Atualiza a Lista de Atendimentos //

            // Atualiza o 'Timestamp' do Usuário para identificar se ele está logado //
                var idTimestampUsuario = setInterval(function() { 
                    updateTimestampUser();
                }, 300000);
                
                function updateTimestampUser(){
                    $.ajax("cadastros/usuarios/gravaTimestamp.php").done(
						function(timestamp) {}
                    );
                }

                // Chama na primeira vez em que a página é Carregada //
                updateTimestampUser();
            // FIM Atualiza o 'Timestamp' do Usuário para identificar se ele está logado //
            
            // ===== TOGGLE MENU LATERAL =====
            console.log("✅✅✅ TOGGLE MENU INICIADO ✅✅✅");
            
            $("#btnMinimuiConversas").on("click", function(e) {    
                e.preventDefault();
                console.log("✅ CLIQUE EM OCULTAR MENU");
                
                var MenuLateral = document.querySelector('#MenuLateral');
                if (MenuLateral) {
                    MenuLateral.classList.add('menu-hidden');
                }
                
                $("#btnMinimuiConversas").fadeOut(200);
                $("#btnMinimuiConversas2").fadeIn(200);
            });
            
            $("#btnMinimuiConversas2").on("click", function(e) {   
                e.preventDefault();
                console.log("✅ CLIQUE EM MOSTRAR MENU");
                
                var MenuLateral = document.querySelector('#MenuLateral');
                if (MenuLateral) {
                    MenuLateral.classList.remove('menu-hidden');
                }
                
                $("#btnMinimuiConversas2").fadeOut(200);
                $("#btnMinimuiConversas").fadeIn(200);
            });
            
            console.log("✅ Toggle Menu ATIVO E FUNCIONANDO!");
            // ===== FIM TOGGLE MENU =====
            
            // ===== TOGGLE WEBCHAT AREA =====
            console.log("✅✅✅ CONFIGURANDO btManipulaChat ✅✅✅");
            
            $("#btManipulaChat").on("click", function(e) {    
                e.preventDefault();
                console.log("✅ CLIQUE EM OCULTAR WEBCHAT");
                
                var webchatArea = document.querySelector('#webchatArea');
                if (webchatArea) {
                    webchatArea.classList.add('webchat-hidden');
                }
                
                $("#btManipulaChat").fadeOut(200);
                $("#btManipulaChat2").fadeIn(200);
            });
            
            $("#btManipulaChat2").on("click", function(e) {   
                e.preventDefault();
                console.log("✅ CLIQUE EM MOSTRAR WEBCHAT");
                
                var webchatArea = document.querySelector('#webchatArea');
                if (webchatArea) {
                    webchatArea.classList.remove('webchat-hidden');
                }
                
                $("#btManipulaChat2").fadeOut(200);
                $("#btManipulaChat").fadeIn(200);
            });
            
            console.log("✅ btManipulaChat ATIVO E FUNCIONANDO!");
            // ===== FIM TOGGLE WEBCHAT AREA =====
        });
    </script>
    <script>
        // Script de Força para garantir que botões apareçam
        document.addEventListener('DOMContentLoaded', function() {
            console.log("🔴🔴🔴 SCRIPT DE FORÇA INICIADO 🔴🔴🔴");
            
            // Criar novo botão do zero
            function criarBotaoFallback() {
                // Verifica se já existe
                if (document.getElementById('btManipulaChat2Fallback')) {
                    return;
                }
                
                // Cria novo elemento
                var novoBotao = document.createElement('div');
                novoBotao.id = 'btManipulaChat2Fallback';
                novoBotao.style.position = 'fixed';
                novoBotao.style.right = '10px';
                novoBotao.style.top = '50%';
                novoBotao.style.marginTop = '-25px';
                novoBotao.style.backgroundColor = '#949a9c';
                novoBotao.style.borderTopLeftRadius = '20px';
                novoBotao.style.borderBottomLeftRadius = '20px';
                novoBotao.style.padding = '5px 8px';
                novoBotao.style.cursor = 'pointer';
                novoBotao.style.height = 'auto';
                novoBotao.style.minWidth = '30px';
                novoBotao.style.zIndex = '50000';
                novoBotao.style.opacity = '1';
                novoBotao.style.visibility = 'visible';
                novoBotao.style.display = 'flex';
                novoBotao.style.alignItems = 'center';
                novoBotao.style.justifyContent = 'center';
                novoBotao.style.pointerEvents = 'auto';
                novoBotao.style.transition = 'all 0.3s ease-in-out';
                novoBotao.style.fontSize = '20px';
                novoBotao.style.color = 'white';
                novoBotao.innerHTML = '<span class="fa fa-chevron-right rotateIconClose"></span>';
                
                // Clique do botão
                novoBotao.onclick = function() {
                    console.log("✅ CLIQUE NO BOTÃO FALLBACK");
                    var webchatArea = document.querySelector('#webchatArea');
                    if (webchatArea) {
                        if (webchatArea.classList.contains('webchat-hidden')) {
                            webchatArea.classList.remove('webchat-hidden');
                        } else {
                            webchatArea.classList.add('webchat-hidden');
                        }
                    }
                };
                
                // Hover effect
                novoBotao.onmouseover = function() {
                    this.style.backgroundColor = '#707577';
                };
                novoBotao.onmouseout = function() {
                    this.style.backgroundColor = '#949a9c';
                };
                
                // Adiciona ao body
                document.body.appendChild(novoBotao);
                console.log("✅ BOTÃO FALLBACK CRIADO E ADICIONADO AO BODY!");
            }
            
            // Executa imediatamente
            setTimeout(criarBotaoFallback, 100);
            setTimeout(criarBotaoFallback, 500);
            setTimeout(criarBotaoFallback, 1500);
        });
    </script>

</head>

<body class="web">
    <!-- Campos Input Hidden --> 
        <input type="hidden" id="qtdTriagem" name="qtdTriagem" value='0' />
        <input type="hidden" id="qtdPendentes" name="qtdPendentes" value="0" />
        <input type="hidden" id="qtdAtendendo" name="qtdAtendendo" value='0' />
        <input type="hidden" id="qtdNewMsgTriagem" name="qtdNewMsgTriagem" value='0' />
        <input type="hidden" id="qtdNewMsgPendentes" name="qtdNewMsgPendentes" value='0' />
        <input type="hidden" id="qtdNewMsgAtendendo" name="qtdNewMsgAtendendo" value='0' />
        <input type="hidden" id="id_usuariologado" name="id_usuariologado" value="<?php echo safe_session("usuariosaw", "id", "0"); ?>" />
        <input type="hidden" id="perfilUsuario" name="perfilUsuario" value="<?php echo safe_session("usuariosaw", "perfil", "3"); ?>" />
        <input type="hidden" id="chatOperadores" name="chatOperadores" value="<?php echo safe_session("parametros", "chat_operadores", "0"); ?>" />
        <input type="hidden" id="atendTriagem" name="atendTriagem" value="<?php echo safe_session("parametros", "atend_triagem", "0"); ?>" />
        <input type="hidden" id="historicoConversas" name="historicoConversas" value="<?php echo safe_session("parametros", "historico_conversas", "0"); ?>" />
        <input type="hidden" id="parametrosTitle" name="parametrosTitle" value="<?php echo safe_session("parametros", "title", "SAW"); ?>" />
        <input type="hidden" id="parametrosIniciarConversa" name="parametrosIniciarConversa" value="<?php echo safe_session("parametros", "iniciar_conversa", "0"); ?>" />
        <input type="hidden" id="parametrosRespRapidaAut" name="parametrosRespRapidaAut" value="<?php echo safe_session("parametros", "enviar_resprapida_aut", "0"); ?>" />
        <input type="hidden" id="parametrosEnvioAudioAut" name="parametrosEnvioAudioAut" value="<?php echo safe_session("parametros", "enviar_audio_aut", "0"); ?>" />
        <input type="hidden" id="parametrosQRCode" name="parametrosQRCode" value="<?php echo safe_session("parametros", "qrcode", "0"); ?>" />
        <input type="hidden" id="parametrosOpNaoEnvUltMensagem" name="parametrosOpNaoEnvUltMensagem" value="<?php echo safe_session("parametros", "op_naoenv_ultmsg", "0"); ?>" />
        <input type="hidden" id="parametrosMostraTodosChats" name="parametrosMostraTodosChats" value="<?php echo safe_session("parametros", "mostra_todos_chats", "0"); ?>" />
        <input type="hidden" id="parametrosTransferOffline" name="parametrosTransferOffline" value="<?php echo safe_session("parametros", "transferencia_offline", "0"); ?>" />
        <input type="hidden" id="parametroshistorico_atendimento" name="parametroshistorico_atendimento" value="<?php echo safe_session("parametros", "historico_atendimento", "0"); ?>" />
        <input type="hidden" id="countViewQRCode" name="countViewQRCode" value="0" />
        <input type="hidden" id="s_id_atendimento" name="s_id_atendimento" />
        <input type="hidden" id="s_id_canal" name="s_id_canal" />
        <input type="hidden" id="s_numero" name="s_numero" />
        <input type="hidden" id="s_nome" name="s_nome" />
        <input type="hidden" id="gravando" name="gravando" value="0" />
        <input type="hidden" id="carregaWebChat" name="carregaWebChat" value="0" />
        <input type="hidden" id="audio64" name="audio64" />
        <input type="hidden" id="myInterval" />
        <input type="file" id="upload" name="upload" class="imginput" style="display: none;" />
    <!-- FIM Campos Input Hidden -->

    

    <div id="app">
        <div class="backgroundLine" style="background: <?php echo safe_session("parametros", "color", "#007bff"); ?>;"></div>
        <div class="_1FKgS app-wrapper-web bFqKf">
            <span>
                <!-- Span Contato -->
                <!-- FIM Span Contato -->
            </span>

            <div tabindex="-1" class="app _3dqpi two" id="app-content">
                <div class="MZIyP">
                    <div class="_3q4NP k1feT">
                        <span>
                            <!-- Dados do Usuário -->
                            <?php require_once("dadosUsuario.php"); ?>
                            <!-- FIM Dados do Usuário -->
                        </span>
                        <span>
                            <!-- Nova Conversa -->
                            <!-- FIM Nova Conversa -->
                        </span>
                    </div>
                </div>
                
                <div id="btnMinimuiConversas2"  title="" aria-expanded="false" class="SetaMostrarAtendimentos">
                        <div class="changebtchat2">
                            <span class="fa fa-chevron-left rotateIconClose"></span>
                        </div>
                </div>
                 <div id="btnMinimuiConversas"  title="" aria-expanded="false" class="SetaOcultarAtendimentos">
                    <div class="changebtchat2">
                        <span class="fa fa-chevron-right rotateIconClose"></span>
                    </div>
                </div>
                <!-- ÁREA DOS CONTATOS -->
                <div class="_3q4NP k1feT" id="MenuLateral">  <!-- Preciso tonar esse trecho responsivo //André Luiz 14/11/2022 -->
                    <div id="side" class="swl8g">
                        <header class="_3auIg">
                            <div class="_2umId">
                                <div class="_1WliW" id="my-photo" style="height: 40px; width: 40px; cursor: pointer;">
                                    <img src="#" class="Qgzj8 gqwaM" style="display:none;">
                                    <div class="_3ZW2E">                                                               
                                        <span data-icon="default-user" style="display: block; width: 100%; height: 100%; overflow: hidden;">
                                            <img src="carregarFotoUsuario.php" class="rounded-circle user_img_msg" style="width: 100%; height: 100%; object-fit: cover; display: block; margin: 0; padding: 0; border: 0;">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="_20NlL">
                                <div class="_3Kxus">
                                    <ul class="user-options" style="padding-left: 15px !important">  
                                                                                                                                      
                                         <!-- Conexão -->
                                         <li class="tooltip btNovaConversa" style="z-index:0">
                                            <a href="javascript:;" id="btnConexao">
                                                <i id="btnConexaoColor" class="fas fa-signal itemIcon"></i>
                                            </a>
                                            <span class="tooltiptext tooltip-bottom" id="spanConectado"></span>
                                        </li>
                   
                                        <!-- Contatos -->
                                        <li class="tooltip btNovaConversa" id="contatos-bt-lista" style="z-index:0">
                                            <i class="far fa-address-book itemIcon" style="color: dodgerblue;"></i>
                                            <span class="tooltiptext tooltip-bottom">Lista de contatos</span>
                                        </li>                              
                                        <!-- Módulos -->
                                        <?php 
                                            // Mostro os itens caso exista algum dos módulos instalados //
                                            if( is_dir("modulos/baseconhecimento") ){
                                        ?>
                                                <li class="tooltip btNovaConversa" style="z-index:0">
                                                    <a id="aModalBaseConhecimento" onclick="abrirModal('#modalBaseConhecimento');">
                                                        <i class="fas fa-database itemIcon" style="color: dodgerblue;"></i>
                                                        <span class="tooltiptext tooltip-bottom">Base de Conhecimento</span>
                                                    </a>                                                   
                                                </li>
                                        <?php } ?>
                                        <!-- Histórico de atendimentos -->                                        
                                        <li id="historico-atendimentos" class="tooltip btNovaConversa" style="display:none;z-index:0 !important;">
                                            <i id="iModalRelatorio" class="fas fa-history itemIcon" style="color: blueviolet;" onclick="abrirModal('#modalRelatorio');"></i>
                                            <span class="tooltiptext tooltip-bottom">Histórico</span>
                                        </li>
                                        <li class="tooltip btNovaConversa" style="z-index:0 !important;">
                                            <i id="iModalRedefinirSenha" class="fas fa-lock itemIcon" onclick="abrirModal('#modalRedefinirSenha');" style="padding-top:4px;"></i>
                                            <span class="tooltiptext tooltip-bottom">Mudar Senha</span>
                                        </li>  
                                        
                                           <!-- Sair -->
                                     <li class="tooltip btNovaConversa" style="z-index:0">
                                            <a href="logOff.php">
                                                <i class="fas fa-sign-out-alt itemIcon" style="color: red;"></i>                                               
                                            </a>
                                            <span class="tooltiptext tooltip-bottom">Logout</span>
                                        </li>   
                                    </ul>
                                        
                                    <!-- Box Operadores -->
                                    <!-- FIM Box Operadores -->

                                    <!-- Lista de contatos-->
                                    <?php require_once("boxContatos.php"); ?>
                                    <!-- FIM Lista de contatos-->
                                </div>
                            </div>
                        </header>
                        <!-- Area de Notificações -->
                        <!-- FIM Area de Notificações -->
                        <div tabindex="-1" class="_3CPl4">
                            <!-- Filtros -->
                            <?php //require_once("filtrosConversas.php"); ?>
                            <!-- FIM Filtros -->
                        </div>
                        <div class="_1NrpZ" id="pane-side" data-list-scroll-container="true">
                            <div tabindex="-1" data-tab="3">
                                <div>
                                    <!-- Conversas -->
                                    <div class="RLfQR">
                                                <span onclick="toggleList('ListaTriagem', 'counterTriagemValue')" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                                <div style="display: flex; align-items: center;">
                                                    <i class="fas fa-inbox" style="margin-right: 10px; font-size: 24px; color: #128c7e;"></i><span>Triagem Sem Departamento</span>
                                                </div>
                                                <div class="_3Bxar">
                                                    <span>
                                                        <div class="_15G96" id="counterTriagem"><span style ="padding: 5px; background-color: #128c7e; border-radius: 50%; color: white;  margin-left: auto; ">
                                                        <div class="counter"id="counterTriagemValue">0</div>
                                                        </span></div>
                                                    </span>
                                                </div>
                                                </span>
                                                <div id="ListaTriagem" class="sub-list">
                                        <!-- Lista os Atendimentos Triagem Sem Departamento -->
                                    </div>
                                    </div>

                                                <div class="RLfQR">
                                                <span onclick="toggleList('ListaPendentes', 'counterPendentesValue')" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                                <div style="display: flex; align-items: center;">
                                                    <i class="fas fa-hourglass-start" style="margin-right: 10px; font-size: 24px; color: #ffa500;"></i><span>Atendimentos em Espera</span>
                                                </div>
                                                <div class="_3Bxar">
                                                    <span>
                                                        <div class="_15G96" id="counterPendentes"><span style ="padding: 5px; background-color: #ffa500; border-radius: 50%; color: white;  margin-left: auto; ">
                                                        <div class="counter" id="counterPendentesValue">0</div>
                                                        </span></div>
                                                    </span>
                                                </div>
                                                </span>
                                                <div id="ListaPendentes" class="sub-list">
                                        <!-- Lista os Atendimentos Em Espera -->
                                    </div>
                                    </div>

                                                <div class="RLfQR">
                                                <span onclick="toggleList('ListaEmAtendimento', 'counterEmAtendimentoValue')" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                                <div style="display: flex; align-items: center;">
                                                    <i class="fas fa-comments" style="margin-right: 10px; font-size: 24px; color: #007bff;"></i><span>Atendimentos em Andamento</span>
                                                </div>
                                                <div class="_3Bxar">
                                                    <span>
                                                        <div class="_15G96" id="counterEmAtendimento"><span style ="padding: 5px; background-color: #007bff; border-radius: 50%; color: white;  margin-left: auto; ">
                                                        <div class="counter" id="counterEmAtendimentoValue">0</div>
                                                        </span></div>
                                                    </span>
                                                </div>
                                                </span>
                                                <div id="ListaEmAtendimento" class="sub-list">
                                        <!-- Lista os Atendimentos Atuais -->
                                    </div>
                                                </div>
                                  
                                    <!-- FIM Conversas -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FECHA ÁREA DOS CONTATOS -->

             

                <!-- ÁREA PRINCIPAL DOS COMENTÁRIOS -->
                <div class="_3q4NP _1Iexl mostrar">
                    <div id="home" class="_3qlW9">
                        <!-- Conecta Celular -->
                        <!-- FIM Conecta Celular -->
                        <div id="AtendimentoAberto" class="_1GX8_">
                            <!-- Conversa Atual -->
                        </div>
                    </div>
                </div>
                <!-- FIM ÁREA PRINCIPAL DOS COMENTÁRIOS -->

                <!-- WEBCHAT LATERAL DIREITO -->
                <div id="webchatArea" class="_3q4NP k1feT webchat-container">
                    <?php require_once("webchat/content.php"); ?>
                </div>
                <!-- FIM WEBCHAT LATERAL DIREITO -->
            </div>
        </div>

        <!-- BOTÕES TOGGLE WEBCHAT (FORA DO app-wrapper-web para evitar overflow hidden) -->
        <div id="btManipulaChat2" title="Mostrar Chat" aria-expanded="false">
            <div class="changebtchat2">
                <span class="fa fa-chevron-left rotateIconClose"></span>
            </div>
        </div>
        <div id="btManipulaChat" title="Ocultar Chat" aria-expanded="true">
            <div class="changebtchat">
                <span class="fa fa-chevron-right rotateIconClose"></span>
            </div>
        </div>
        <!-- FIM BOTÕES TOGGLE WEBCHAT -->

        <?php require_once("modais/modais.php"); ?>
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
            console.log("🚀🚀🚀 SEGUNDO $(document).ready INICIADO 🚀🚀🚀");
            
            // Habilita a opção de Configurações apenas para os Administradores //         
            if( $("#perfilUsuario").val() != 1 ){
                $("#menu-options").attr('style','display: block');
                $("#historico-atendimentos").attr('style','display: block');
           

                // Habilitando o Cadastro de Usuários //
                $("#liUsuarios").attr('style','display: block');
                $("#liTelefones").attr('style','display: block');

                // Libera os demais Menus caso acesso de Administrador //
                if( $("#perfilUsuario").val() == 0 ){
                    $("#liCanais").attr('style','display: block');
                    $("#liMenus").attr('style','display: block');
                    $("#liDepartamentos").attr('style','display: block');
                    $("#liRespostasRapidas").attr('style','display: block');
                    $("#liRespostasAutomaticas").attr('style','display: block');
                    $("#liHorariosAtendimentos").attr('style','display: block');
                    $("#liConfiguracoes").attr('style','display: block');
                    $("#liDashboards").attr('style','display: block');
                    $("#liEtiquetas").attr('style','display: block'); 
                }
            }

            // Verifica a Conexão do Bot //
                function verificaConexao() {
                    $.post("includes/conectado.php", {}, function(retorno) {
                        retorno = JSON.parse(retorno);

                        // Definição de Variável //
                        var color, label;

                        // Verifica se está ou não Conectado //
                        if( retorno.status === 9 ){
                            color = 'red';
                            label = 'Desconectado';

                            // Verifica Parâmetro para Habilitar a Tela de Leitura do QRCode //
                            if( $("#parametrosQRCode").val() === "1" ){
                                $("#btnConexao").attr( "onclick", "abrirModal('#modalQRCode')" );
                                setTimeout(function() { $('#btnConexao').click(); }, 500);
                            }
                        }
                        else {
                            color = 'green';
                            label = 'Conectado';
                            $("#btnConexao").removeAttr("onclick");

                            // Verifica o valor de 'countViewQRCode' antes de fechar a Modal //
                            if( parseInt($("#countViewQRCode").val()) !== 0 ){
                                // Fecha a Janela //
                                fecharModal();

                                // Zera o Contador //
                                $("#countViewQRCode").val(0);
                            }
                        }

                        // Envia Dados para o HTML //
                        $("#btnConexaoColor").attr('style', 'color: ' + color);
                        $("#spanConectado").text(label);
                    });
                }
            // FIM Verifica a Conexão do Bot //

            // Chamada da Verificação de Conexão //
                var intervalConnection = setInterval(function() { verificaConexao(); }, 15000);
                verificaConexao();
            // FIM Chamada da Verificação de Conexão //



            // Atualiza o QRCode //
                function getQRCode() {
                    var countViewQRCode = $("#countViewQRCode").val();

                    // Verifica Parâmetro para Habilitar a Tela de Leitura do QRCode //
                    if( $("#parametrosQRCode").val() === "1" 
                        // Só inicia a Leitura do QRCode se 'qrcode' for inicializada. Possue stop no valor '60' - Só executa durante 60 segundos //    
                        && countViewQRCode > 0 ){
                        $.post("includes/qrcode.php", {count: countViewQRCode}, function(retorno) {
                            retorno = JSON.parse(retorno);

                            // Verifica se está ou não Conectado //
                            if( retorno.status === 9 ){
                                if( retorno.qrcode !== 9 ){
                                    // Atualiza a Variável 'qrcode' //
                                    $("#btnConexao").removeAttr( "src");
                                    $("#imgQRCode").attr( "src", "data:image/png;base64," + retorno.qrcode);

                                    // Fechar a Janela //
                                    if( retorno.count === 0 ){ fecharModal(); }
                                }
                                // Server Bot Offline //
                                else{ $("#serverBotOffline").attr('style', 'display: block'); }

                                // Atualizando o Contador //
                                $("#countViewQRCode").val(retorno.count);
                            }
                            else{
                                // Envia Dados para o HTML //
                                $("#btnConexaoColor").attr('style', 'color: green');
                                $("#spanConectado").text("Conectado");

                                // Fecha a Janela //
                                fecharModal();
                                
                                // Zera o Contador //
                                $("#countViewQRCode").val("0");
                            }
                        });
                    }
                }
            // FIM Atualiza o QRCode //

            // Chamada da Atualização o QRCode //
                var intervalQRCode = setInterval(function() { getQRCode(); }, 1000);
            // FIM Chamada da Atualização o QRCode //

            // Carregamento das Modais //              

                // Modal Menus //
                $("#aModalMenus").on("click", function() {
                    $.ajax("cadastros/menu/index.php").done(function(data) {
                        $('#modalMenu').html(data);
                    });
                });

                // Modal Departamentos //
                $("#aModalDepartamentos").on("click", function() {
                    $.ajax("cadastros/departamentos/index.php").done(function(data) {
                        $('#modalDepartamento').html(data);
                    });
                });

                // Modal Respostas Rapidas //
               // $("#aModalRespostasRapidas").on("click", function() {});

                // Modal Respostas Automaticas //
                $("#aModalRespostasAutomaticas").on("click", function() {
                    $.ajax("cadastros/respostasautomaticas/index.php").done(function(data) {
                        $('#modalRespostaAutomatica').html(data);
                    });
                });

                // Modal Horarios Atendimentos //
                $("#aModalHorariosAtendimentos").on("click", function() {
                    $.ajax("cadastros/horarios/index.php").done(function(data) {
                        $('#modalHorarioAtendimento').html(data);
                    });
                });

                // Modal Usuarios //
                $("#aModalUsuarios").on("click", function() {
                    $.ajax("cadastros/usuarios/index.php").done(function(data) {
                        $('#modalUsuario').html(data);
                    });
                });

                // Modal Configurações //
                $("#aModalConfiguracoes").on("click", function() {
                    $.ajax("cadastros/configuracoes/index.php").done(function(data) {
                        $('#modalConfiguracao').html(data);
                    });
                });
                
                // Modal Dashboards //
                $("#aModalDashboards").on("click", function() {});

                // Modal Relatorio //
                $("#iModalRelatorio").on("click", function() {
                    $.ajax("cadastros/relatorios/index.php").done(function(data) {
                        $('#modalRelatorio').html(data);
                    });
                });

                // Modal QRCode //
                $("#btnConexao").on("click", function() {
                    $("#countViewQRCode").val("1");
                });

                // Modal Base de Conhecimento //
                $("#aModalBaseConhecimento").on("click", function() {
                    $.ajax("modulos/baseconhecimento/index.php").done(function(data) {
                        $('#modalBaseConhecimento').html(data);
                    });
                });

                

                // Modal Telefones //
                $("#aModalTelefones").on("click", function() {
                    $.ajax("cadastros/telefoneaviso/index.php").done(function(data) {
                        $('#modalTelefone').html(data);
                    });
                });

               // Modal Redefinir Senha //
               $("#iModalRedefinirSenha").on("click", function() {
                    $.ajax("cadastros/usuarios/redefinirSenha.php").done(function(data) {
                        $('#modalRedefinirSenha').html(data);
                    });
                });

                // Modal Nova Conversa //
               $("#iModalNovaConversa").on("click", function() {
                    $.ajax("atendimento/novaConversa.php").done(function(data) {                     
;                        $('#modalNovaConversa').html(data);
                    });
                });

                 // Modal Canais //
                 $("#aModalEtiquetas").on("click", function() {
                    $.ajax("cadastros/etiquetas/index.php").done(function(data) {
                        $('#modalEtiqueta').html(data);
                    });
                });
            // FIM Carregamento das Modais //

            // Habilita o sistema de Abas //
                $("#tabs").tabs();
                $("#tabs2").tabs();
                $("#tabs3").tabs();
                $("#tabs4").tabs();
            // FIM Habilita o sistema de Abas //

            console.log("�🔴🔴 INICIANDO CONFIGURAÇÃO DO TOGGLE 🔴🔴🔴");
            console.log("�🔍 TESTE: jQuery está disponível?", typeof $);
            console.log("🔍 TESTE: Elemento #btnMinimuiConversas existe?", document.getElementById('btnMinimuiConversas'));
            console.log("🔍 TESTE: jQuery encontrou #btnMinimuiConversas?", $("#btnMinimuiConversas").length);

            //Ocultar Conversas (TOGGLE MENU)
            
            $("#btnMinimuiConversas").on("click", function(e) {    
                e.preventDefault();
                console.log("✅ CLIQUE EM OCULTAR MENU DETECTADO");
                
                var MenuLateral = document.querySelector('#MenuLateral');
                if (MenuLateral) {
                    MenuLateral.style.display = 'none';
                    console.log("✅ Menu oculto");
                }
                     
                if ($("._1FKgS").length) $("._1FKgS").css("overflow","hidden");
                if ($("#btnVoltarResponsivo").length) $("#btnVoltarResponsivo").css("display","block");
   
                
                $("#btnMinimuiConversas2").css("display","flex");       
                if ($('.._1Iexl').length) $('.._1Iexl').css("-webkit-flex","100%");                 

                
            });

            //Mostrar Conversas (TOGGLE MENU)
            $("#btnMinimuiConversas2").on("click", function(e) {   
                e.preventDefault();
                console.log("✅ CLIQUE EM MOSTRAR MENU DETECTADO");
                
                var MenuLateral = document.querySelector('#MenuLateral');
                if (MenuLateral) {
                    MenuLateral.style.display = 'block';
                    console.log("✅ Menu visível");
                }                     
                 
                if ($("._1FKgS").length) $("._1FKgS").css("overflow","visible");

                 

                $("#btnMinimuiConversas2").css("display","none");  
                if ($('.._1Iexl').length) $('.._1Iexl').css("-webkit-flex","100%");   
                    
                if ($("#btnVoltarResponsivo").length) $("#btnVoltarResponsivo").css("display","none");
             
               

            });

            
        });
    </script>
  <script>
  function toggleList(listId, counterId) {
    var list = document.getElementById(listId);
    var counterValue = document.getElementById(counterId);

    if (list.style.display === 'none' || list.style.display === '') {
      list.style.display = 'block';
    } else {
      list.style.display = 'none';
    }

    // Atualize o contador com o número correto de itens na lista
    setTimeout(function () {
      updateItemCount(list, counterValue);
    }, 0);
  }

  function updateItemCount(list, counterValue) {
    var itemCount = list.querySelectorAll('div').length;
    var itemCountDividedByThirteen = Math.floor(itemCount / 13); // Divida o resultado por 13 e arredonde para baixo

    counterValue.innerText = itemCountDividedByThirteen;
  }

  function observeListChanges(listId, counterId) {
    var list = document.getElementById(listId);
    var counterValue = document.getElementById(counterId);

    // Função para atualizar o contador
    function updateCounter() {
      updateItemCount(list, counterValue);
    }

    // Configurar o observador para observar alterações no conteúdo da lista
    var observer = new MutationObserver(updateCounter);
    var observerConfig = { childList: true, subtree: true };
    observer.observe(list, observerConfig);

    // Chamar a função de atualização quando a página é carregada
    updateCounter();
  }

  // Chame a função observeListChanges para cada lista quando a página for carregada
  window.onload = function () {
    observeListChanges('ListaTriagem', 'counterTriagemValue');
    observeListChanges('ListaPendentes', 'counterPendentesValue');
    observeListChanges('ListaEmAtendimento', 'counterEmAtendimentoValue');
  };
  
  // ===== Carregar foto do usuário =====
  $(document).ready(function() {
    // Ensegurar que a foto do usuário está carregada
    $('.user_img_msg').on('error', function() {
      // Se houver erro ao carregar, mostra foto padrão
      $(this).attr('src', 'images/user-default.png');
    });
    
    // Recarregar foto a cada 30 segundos (em caso de alteração)
    setInterval(function() {
      const $img = $('.user_img_msg');
      const currentSrc = $img.attr('src');
      if (currentSrc && currentSrc.includes('carregarFotoUsuario.php')) {
        $img.attr('src', currentSrc + '?t=' + new Date().getTime());
      }
    }, 30000);
    
    console.log("✓ Sistema de foto do usuário carregado");
  });
  // ===== FIM Carregar foto do usuário =====
</script>


</body>
</html>