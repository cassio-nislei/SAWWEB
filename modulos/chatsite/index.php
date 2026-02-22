<?php
// Session already started in padrao.inc.php when accessed via gestao/index.php
// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

// Se é um operador/atendente (tem usuário logado em gestao), mostra painel de conversas
if (isset($_SESSION["usuariosaw"])) {
    include('conversas_sala.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SAW Chat - Atendimento</title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
    
    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0b5ed7;
            --secondary-color: #6f42c1;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding: 1rem;
            margin: 0;
        }

        .chat-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem 0;
        }

        .chat-wrapper {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 80vh;
            max-height: 600px;
        }

        /* ===== HEADER ===== */
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .chat-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .chat-header h2 i {
            font-size: 1.75rem;
        }

        .chat-status {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        /* ===== INICIO ATENDIMENTO ===== */
        .inicio-atendimento {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(180deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            text-align: center;
        }

        .inicio-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .inicio-title {
            font-size: 1.5rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .inicio-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        .form-inicio {
            width: 100%;
        }

        .form-control-moderno {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control-moderno:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .btn-iniciar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-iniciar:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-iniciar:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ===== CHAT MESSAGES ===== */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: white;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: var(--light-bg);
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #bbb;
        }

        .message {
            display: flex;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.own {
            justify-content: flex-end;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            flex-shrink: 0;
            font-weight: 600;
        }

        .message.own .message-avatar {
            background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);
        }

        .message-content {
            display: flex;
            flex-direction: column;
            max-width: 75%;
        }

        .message-info {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
            padding: 0 0.5rem;
        }

        .message-text {
            background: var(--light-bg);
            color: var(--text-dark);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            word-wrap: break-word;
            line-height: 1.5;
        }

        .message.own .message-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 2px 12px 12px;
        }

        .message:not(.own) .message-text {
            border-radius: 2px 12px 12px 12px;
        }

        .system-message {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 1rem;
            background: var(--light-bg);
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        /* ===== CHAT FOOTER ===== */
        .chat-footer {
            background: white;
            border-top: 1px solid var(--border-color);
            padding: 1rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }

        .input-mensagem {
            flex: 1;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-family: inherit;
            resize: none;
            max-height: 100px;
            transition: all 0.3s ease;
        }

        .input-mensagem:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .btn-enviar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .btn-enviar:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-enviar:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ===== TOOLS ===== */
        .chat-tools {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            padding: 0 0.5rem;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--border-color);
            background: white;
        }

        .badge-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.45rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-tool {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-tool:hover {
            color: #667eea;
        }

        .btn-sair {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-sair:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220, 53, 69, 0.3);
            color: white;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            .chat-wrapper {
                max-height: 100vh;
                height: 100vh;
                border-radius: 0;
            }

            body {
                padding: 0;
            }

            .chat-container {
                min-height: auto;
                height: 100vh;
                padding: 0;
            }

            .chat-header h2 {
                font-size: 1.25rem;
            }

            .message-content {
                max-width: 85%;
            }

            .message-text {
                font-size: 0.95rem;
            }

            .chat-footer {
                padding: 0.75rem;
                gap: 0.5rem;
            }

            .input-mensagem {
                font-size: 1rem;
            }

            .btn-enviar {
                width: 36px;
                height: 36px;
            }
        }

        /* ===== ANIMATIONS ===== */
        .spin {
            animation: spin 0.6s linear;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .typing-indicator {
            display: flex;
            gap: 4px;
            align-items: center;
            padding: 0.75rem 1rem;
            background: var(--light-bg);
            border-radius: 12px;
            width: fit-content;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #999;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% { opacity: 0.5; }
            30% { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-wrapper">
            <!-- HEADER -->
            <div class="chat-header">
                <h2><i class="bi bi-chat-dots-fill"></i> Chat SAW</h2>
                <div class="chat-status">
                    <?php echo isset($_SESSION["chat"]["nome"]) ? "Bem-vindo, " . $_SESSION["chat"]["nome"] . "!" : "Novo atendimento"; ?>
                </div>
            </div>

            <!-- CONTEÚDO PRINCIPAL -->
            <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
                <?php if (!isset($_SESSION["chat"]["nome"])): ?>
                    <!-- FORMULÁRIO DE INÍCIO -->
                    <div class="inicio-atendimento">
                        <div class="inicio-icon"><i class="bi bi-chat-heart"></i></div>
                        <h3 class="inicio-title">Bem-vindo!</h3>
                        <p class="inicio-subtitle">Informe seu nome para iniciar um atendimento</p>
                        <form class="form-inicio">
                            <input type="text" id="nome" name="nome" placeholder="Seu nome..." class="form-control-moderno" required>
                            <button type="button" id="btnIniciarAtendimeto" class="btn-iniciar">
                                <i class="bi bi-play-circle" style="margin-right: 0.5rem;"></i> Iniciar Atendimento
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <!-- FERRAMENTAS DO CHAT -->
                    <div class="chat-tools">
                        <span class="badge-custom"><i class="bi bi-chat-dots"></i> <span id="TotalMensagens">0</span> mensagens</span>
                        <div style="margin-left: auto; display: flex; gap: 0.5rem; align-items: center;">
                            <button type="button" class="btn-tool" title="Minimizar" data-bs-toggle="tooltip">
                                <i class="bi bi-dash-lg"></i>
                            </button>
                            <button type="button" class="btn-tool" title="Fechar" data-bs-toggle="tooltip">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <a href="../modulos/chatsite/sair.php" class="btn-sair">
                                <i class="bi bi-box-arrow-right" style="margin-right: 0.5rem;"></i> Sair
                            </a>
                        </div>
                    </div>

                    <!-- MENSAGENS -->
                    <div class="chat-messages" id="ListarMensagens">
                        <div class="system-message">
                            <i class="bi bi-info-circle" style="margin-right: 0.5rem;"></i>
                            Em breve um de nossos atendentes o atenderá. Aguarde por favor!
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="chat-footer">
                        <textarea id="mensagem" name="mensagem" placeholder="Digite sua mensagem..." class="input-mensagem" rows="1"></textarea>
                        <button type="button" id="btnGravarMensagem" class="btn-enviar" title="Enviar">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>

                    <!-- Hidden inputs for JavaScript polling -->
                    <input type="hidden" id="s_numero" value="<?php echo isset($_SESSION["chat"]["numero"]) ? $_SESSION["chat"]["numero"] : ''; ?>">
                    <input type="hidden" id="s_id_atendimento" value="<?php echo isset($_SESSION["chat"]["id_atendimento"]) ? $_SESSION["chat"]["id_atendimento"] : ''; ?>">
                    <input type="hidden" id="s_nome" value="<?php echo isset($_SESSION["chat"]["nome"]) ? $_SESSION["chat"]["nome"] : ''; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-resize textarea
        const textarea = document.getElementById('mensagem');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            });
        }

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Iniciar Atendimento
        $("#btnIniciarAtendimeto").click(function(e){
            e.preventDefault();
            var nome = $("#nome").val();
            if ($("#nome").val()==''){               
                return false;
            }
            $(this).html('<i class="bi bi-hourglass-split spin"></i> Iniciando...').prop('disabled', true);
            $.post("../modulos/chatsite/iniciaAtendimento.php", {nome:nome},function(retorno){
                if (retorno==1){
                    location.reload(true);
                } 
                $("#btnIniciarAtendimeto").html('<i class="bi bi-play-circle"></i> Iniciar Atendimento').prop('disabled', false);
            });
        });

        // Processar Mensagem
        function processaMensagem(event){
            var strMensagem = $.trim($("#mensagem").val());
            
            // Permitir quando pressionar <Shift> e <Enter>
            if( event.keyCode == 13 && event.shiftKey ){
                var content = $("#mensagem").val();
                var caret = getCaret(this);
                this.value = content.substring(0,caret) + "\n" + content.substring(caret,content.length-1);
                event.stopPropagation();
            }
            else if( event.keyCode == 13 ){
                event.preventDefault();
                $("#mensagem").focus();
                $("#btnGravarMensagem").click();
                return false;
            }
        }

        $("#mensagem").keydown(function(event) { processaMensagem(event); });

        // Gravar Mensagem
        $("#btnGravarMensagem").click(function(e){
            e.preventDefault();
            var mensagem = $("#mensagem").val();
            if ($("#mensagem").val()==''){               
                return false;
            }
            $(this).html('<i class="bi bi-hourglass-split spin"></i>').prop('disabled', true);
            $.post("../modulos/chatsite/gravaMensagem.php", {mensagem:mensagem},function(retorno){
                if (retorno==1){
                    $("#mensagem").val("").css('height', 'auto');
                    if ( $( "#ListarMensagens" ).length ) { 
                        $( "#ListarMensagens" ).load( "../modulos/chatsite/listaMensagens.php" );
                    }
                } 
                $("#btnGravarMensagem").html('<i class="bi bi-send-fill"></i>').prop('disabled', false);
            });
        });

        // Carregar Mensagens
        if ( $( "#ListarMensagens" ).length ) { 
            $( "#ListarMensagens" ).load( "../modulos/chatsite/listaMensagens.php" );
        }

        // Scroll para o final
        function ajustaScroll(){	
            var el = document.getElementById('ListarMensagens');
            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        }

        ajustaScroll();

        // Atualizar quantidade de mensagens
        function atualizaQtdMensagens() {
            var numero       = $("#s_numero").val();
            var id           = $("#s_id_atendimento").val();
            var qtdMensagens = $("#TotalMensagens").text();

            $.post("../modulos/chatsite/qtdnovasmensagens.php", {
                numero: numero,
                id: id
            }, function(retorno) {
                if (parseInt(retorno) > parseInt(qtdMensagens)) {
                    if ( $( "#ListarMensagens" ).length ) { 
                        $( "#ListarMensagens" ).load( "../modulos/chatsite/listaMensagens.php" );
                    }
                    ajustaScroll();
                }
                $("#TotalMensagens").html(retorno);
            });
        }

        // Polling de atualização
        var intervalo = setInterval(function() { atualizaQtdMensagens(); }, 5000);
        atualizaQtdMensagens();
    </script>
</body>
</html>
