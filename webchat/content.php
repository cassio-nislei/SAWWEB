<?php
// Arquivo para carregar apenas o conteúdo do webchat (sem as tags HTML/HEAD/BODY)
// Usado quando o webchat é carregado como componente dentro de outra página

require_once(__DIR__ . "/../includes/padrao.inc.php");

// A tentativa extra de incluir o arquivo de conexão caso não tenha carregado
if (!isset($conexao)) {
    require_once(__DIR__ . "/../includes/conexao.php");
}

// Garantir que a sessão está ativa - se não estiver, continuar mesmo assim para renderizar a interface
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["usuariosaw"])) {
    $_SESSION["usuariosaw"]["id"] = 0;  // Usuário anônimo
}

// Incluir o CSS do webchat
?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .webchat-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* Header */
    .webchat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .webchat-header-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 18px;
        font-weight: 600;
    }

    .webchat-header-title i {
        font-size: 24px;
    }

    .department-selector {
        min-width: 280px;
    }

    .department-selector select {
        border-radius: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.1);
        color: white;
        padding: 8px 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: auto;
        max-width: 100%;
        white-space: nowrap;
    }

    .department-selector select:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .department-selector select option {
        background: #667eea;
        color: white;
    }

    /* Private Message Controls */
    .private-message-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        flex-wrap: wrap;
    }

    .private-checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        user-select: none;
    }

    .private-checkbox-wrapper input[type="checkbox"] {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    .private-checkbox-wrapper label {
        cursor: pointer;
        font-size: 13px;
        margin: 0;
        color: rgba(255, 255, 255, 0.9);
    }

    .operator-selector {
        display: none;
        min-width: 220px;
    }

    .operator-selector.active {
        display: block;
    }

    .operator-selector select {
        border-radius: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.9) !important;
        color: #333 !important;
        padding: 8px 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        white-space: nowrap;
    }

    .operator-selector select:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .operator-selector select option {
        background: #f0f2f5 !important;
        color: #333 !important;
    }

    .operator-selector select option:checked {
        background: linear-gradient(#667eea, #667eea) !important;
        background-color: #667eea !important;
        color: white !important;
    }

    .private-tag {
        background: rgba(255, 76, 76, 0.15) !important;
        color: #ff4c4c !important;
    }

    .message-item.own .private-tag {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }

    /* Messages Area */
    .webchat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        background: #f8f9fa;
    }

    .webchat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .webchat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .webchat-messages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }

    .webchat-messages::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    /* Message Styles */
    .message-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .message-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        animation: slideIn 0.3s ease;
    }

    .message-item.own {
        justify-content: flex-end;
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

    .message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 12px;
        flex-shrink: 0;
    }

    .avatar-actions-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }

    .message-item.own .message-avatar {
        order: 2;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .message-content {
        flex: 1;
        max-width: 70%;
    }

    .message-item.own .message-content {
        text-align: right;
    }

    .message-bubble {
        background: white;
        border-radius: 12px;
        padding: 10px 14px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        word-wrap: break-word;
    }

    .message-item.own .message-bubble {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-item:not(.own) .message-bubble {
        border-bottom-left-radius: 4px;
    }

    .message-header {
        font-weight: 600;
        font-size: 13px;
        color: #333;
        margin-bottom: 4px;
    }

    .message-item.own .message-header {
        color: rgba(255, 255, 255, 0.9);
    }

    .message-text {
        font-size: 14px;
        line-height: 1.4;
        color: #333;
        word-wrap: break-word;
    }

    .message-item.own .message-text {
        color: white;
    }

    .message-time {
        font-size: 11px;
        color: #999;
        margin-top: 4px;
    }

    .message-item.own .message-time {
        color: rgba(255, 255, 255, 0.7);
    }

    .message-tag {
        display: inline-block;
        background: #e7f3ff;
        color: #0066cc;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 4px;
    }

    .message-item.own .message-tag {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .message-tag.private-tag {
        background: rgba(255, 76, 76, 0.15) !important;
        color: #ff4c4c !important;
        border: 1px solid rgba(255, 76, 76, 0.3);
    }

    .message-item.own .message-tag.private-tag {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Empty State */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    /* Input Area */
    .webchat-input-area {
        background: white;
        border-top: 1px solid #e0e0e0;
        padding: 15px 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }

    .input-wrapper {
        flex: 1;
        display: flex;
        gap: 8px;
        align-items: flex-end;
    }

    #msgChat {
        flex: 1;
        border: 2px solid #e0e0e0;
        border-radius: 20px;
        padding: 10px 15px;
        resize: none;
        font-family: inherit;
        font-size: 14px;
        transition: all 0.3s ease;
        max-height: 100px;
        min-height: 40px;
    }

    #msgChat:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-send {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .btn-send:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .btn-send:active {
        transform: scale(0.95);
    }

    .btn-send i {
        font-size: 18px;
    }

    .loading-spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #666;
        margin-top: 4px;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #4caf50;
    }

    .message-actions {
        display: flex;
        gap: 2px;
        flex-direction: column;
        align-items: center;
    }

    .message-item:not(.own) .message-actions {
        display: none;
    }

    .action-btn {
        background: none;
        border: none;
        color: #667eea;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 16px;
        padding: 0;
        width: auto;
        height: auto;
    }

    .action-btn.btn-edit {
        color: #667eea;
    }

    .action-btn.btn-delete {
        color: #dc3545;
    }

    .action-btn:hover {
        transform: scale(1.2);
        opacity: 0.8;
    }

    .action-btn:active {
        transform: scale(0.95);
    }

    @media (max-width: 768px) {
        .webchat-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .department-selector {
            width: 100%;
        }

        .department-selector select {
            width: 100%;
        }

        .private-message-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .operator-selector {
            width: 100%;
        }

        .operator-selector select {
            width: 100%;
        }

        .message-content {
            max-width: 85%;
        }
    }
</style>

<!-- Inputs necessários -->
<input type="hidden" id="idDepartamentoChat" value="0" />
<input type="hidden" id="carregaWebChat" value="1" />

<!-- HTML do WebChat -->
<div class="webchat-container">
    <!-- Header -->
    <div class="webchat-header">
        <div class="webchat-header-title">
            <i class="bi bi-chat-dots"></i>
            <span>Chat Operadores</span>
        </div>
        <div class="department-selector">
            <select id="mudaDepartamento">
                <option value="0">📌 Todos os setores</option>
                <option value="1">🏢 Geral</option><option value="2">🏢 Loja</option><option value="3">🏢 Pagamentos</option>            </select>
        </div>
    </div>

    <!-- Messages -->
    <div class="webchat-messages" id="conversas_chat"><div class="empty-state" style="margin: 40px 20px;">
            <i class="bi bi-chat-left"></i>
            <p>Nenhuma mensagem ainda</p>
            <small>Comece a conversa digitando sua primeira mensagem</small>
        </div></div>

    <!-- Input Area -->
    <div class="webchat-input-area">
        <!-- Private Message Controls (First Row) -->
        <div class="private-message-controls">
            <div class="private-checkbox-wrapper">
                <input type="checkbox" id="ehPrivada" />
                <label for="ehPrivada">
                    <i class="bi bi-lock"></i> Privado
                </label>
            </div>
            <div class="operator-selector" id="operadorSelectorDiv">
                <select id="operadorDestino">
                    <option value="">📌 Selecione um operador...</option>
                </select>
            </div>
        </div>

        <!-- Message Input (Second Row) -->
        <div class="input-wrapper">
            <textarea id="msgChat" placeholder="Escreva sua mensagem..." data-lpignore="true"></textarea>
            <button class="btn-send" id="btnSendMsg" title="Enviar (Enter)" disabled="">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>

<!-- JavaScript do WebChat (reduzido, apenas funcionalidade essencial) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        console.log("✅ WebChat Content Carregado");
        
        var isLoading = false;
        var chatContainer = $('#conversas_chat');
        var departamentoInput = $('#idDepartamentoChat');
        var carregaWebChatInput = $('#carregaWebChat');
        var msgInput = $('#msgChat');
        var btnSend = $('#btnSendMsg');
        var checkboxPrivado = $('#ehPrivada');
        var operadorSelectorDiv = $('#operadorSelectorDiv');
        var operadorSelect = $('#operadorDestino');

        // Carregar lista de operadores
        function carregaOperadores(idDepto = 0) {
            $.ajax({
                url: '/webchat/getOperadores.php?idDepto=' + idDepto,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.operadores) {
                        operadorSelect.empty();
                        operadorSelect.append('<option value="">📌 Selecione um operador...</option>');
                        
                        response.operadores.forEach(function(op) {
                            operadorSelect.append(
                                '<option value="' + op.id + '">' + op.nome + ' (' + op.departamento + ')</option>'
                            );
                        });
                    }
                },
                error: function(err) {
                    console.error("Erro ao carregar operadores:", err);
                }
            });
        }

        function carregaChat(idDepartamento) {
            if (isLoading || carregaWebChatInput.val() !== "1") return;
            
            isLoading = true;

            $.ajax({
                url: '/webchat/listaMensagens.php?idDepto=' + idDepartamento,
                type: 'GET',
                dataType: 'html',
                timeout: 10000,
                success: function(data) {
                    if (!data.trim()) {
                        isLoading = false;
                        return;
                    }

                    // Parse novo conteúdo em um container temporário
                    const $newContent = $('<div>').html(data);
                    const newMessageIds = [];
                    
                    // Coletar IDs das novas mensagens
                    $newContent.find('[data-msg-id]').each(function() {
                        newMessageIds.push($(this).data('msg-id'));
                    });

                    // Se é estado vazio, apenas mostrar
                    if ($newContent.find('.empty-state').length > 0) {
                        chatContainer.html(data);
                        isLoading = false;
                        return;
                    }

                    // Se não há mensagens atual, preencher
                    if (chatContainer.find('[data-msg-id]').length === 0) {
                        chatContainer.html(data);
                        scrollToBottom();
                        isLoading = false;
                        return;
                    }

                    // Comparar e adicionar apenas mensagens novas
                    let temNovas = false;
                    $newContent.find('[data-msg-id]').each(function() {
                        const msgId = $(this).data('msg-id');
                        if (chatContainer.find('[data-msg-id="' + msgId + '"]').length === 0) {
                            const novaMsg = $(this);
                            novaMsg.css('opacity', '0').appendTo(chatContainer);
                            novaMsg.animate({ opacity: 1 }, 300);
                            temNovas = true;
                        }
                    });

                    if (temNovas) {
                        scrollToBottom();
                    }

                    isLoading = false;
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao carregar mensagens:", error);
                    isLoading = false;
                }
            });
        }

        function scrollToBottom() {
            setTimeout(function() {
                chatContainer.scrollTop(chatContainer[0].scrollHeight);
            }, 100);
        }

        function enviarMensagem() {
            const mensagem = msgInput.val().trim();
            const idDepto = departamentoInput.val();
            const ehPrivada = checkboxPrivado.is(':checked') ? 1 : 0;
            const idDestinatario = ehPrivada ? parseInt(operadorSelect.val()) : 0;

            if (!mensagem) return;

            // Se é privada e não selecionou operador
            if (ehPrivada && !idDestinatario) {
                alert('Por favor, selecione um operador para enviar mensagem privada');
                return;
            }

            btnSend.prop('disabled', true).html('<div class="loading-spinner"></div>');

            $.ajax({
                url: '/webchat/gravarMensagemChat.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    idDepto: idDepto,
                    strMensagem: mensagem,
                    ehPrivada: ehPrivada,
                    idDestinatario: idDestinatario
                },
                timeout: 10000,
                success: function(response) {
                    if (response.success) {
                        msgInput.val('').trigger('input');
                        carregaChat(idDepto);
                        btnSend.prop('disabled', true).html('<i class="bi bi-send-fill"></i>');
                    } else {
                        console.error('Erro:', response.error);
                        alert('Erro ao enviar mensagem: ' + (response.error || 'Tente novamente'));
                        btnSend.prop('disabled', false).html('<i class="bi bi-send-fill"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro:', error, status);
                    let mensagemErro = 'Erro ao enviar mensagem.';
                    
                    if (status === 'timeout') {
                        mensagemErro += ' Tempo limite excedido.';
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        mensagemErro += ' ' + xhr.responseJSON.error;
                    }
                    
                    alert(mensagemErro);
                    btnSend.prop('disabled', false).html('<i class="bi bi-send-fill"></i>');
                }
            });
        }

        // Mudança de departamento
        $('#mudaDepartamento').change(function() {
            var idDepto = $(this).val();
            departamentoInput.val(idDepto);
            isLoading = false;
            carregaOperadores(idDepto);  // Recarrega operadores do departamento
            carregaChat(idDepto);
        });

        // Toggle do checkbox privado
        checkboxPrivado.change(function() {
            if ($(this).is(':checked')) {
                operadorSelectorDiv.addClass('active');
                operadorSelect.focus();
            } else {
                operadorSelectorDiv.removeClass('active');
                operadorSelect.val('');
            }
        });

        // Envio de mensagens
        $('#btnSendMsg').click(function() {
            enviarMensagem();
        });

        msgInput.on('input', function() {
            const temTexto = $(this).val().trim().length > 0;
            btnSend.prop('disabled', !temTexto);
        });

        msgInput.keypress(function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                enviarMensagem();
            }
        });

        // Inicializa carregamento
        carregaOperadores(0);  // Carrega operadores inicialmente
        carregaChat(0);
        
        // Recarrega a cada 3 segundos
        setInterval(function() {
            carregaChat(departamentoInput.val());
        }, 3000);
    });
</script>
