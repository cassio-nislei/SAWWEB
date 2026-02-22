<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Operadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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
            min-width: 200px;
        }

        .department-selector select {
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s ease;
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
            background: rgba(255, 255, 255, 0.95) !important;
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

        /* Loading indicator */
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

        /* Status indicator */
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

        /* Message Actions */
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

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            border-top: 1px solid #e0e0e0;
            padding: 15px 20px;
        }

        .modal-body textarea {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            font-family: inherit;
            resize: vertical;
            min-height: 100px;
        }

        .modal-body textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3d8f 100%);
        }

        /* Responsive */
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
</head>
<body style="padding: 10px;">
    <input type="hidden" id="idDepartamentoChat" value="0" />
    <input type="hidden" id="carregaWebChat" value="1" />

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
                    <?php 
                    require_once("../includes/padrao.inc.php");
                    $qryDeptos = mysqli_query(
                        $conexao,
                        "SELECT id, departamento FROM tbdepartamentos ORDER BY departamento"
                    );
                
                    while( $arrDeptos = mysqli_fetch_assoc($qryDeptos) ){
                        echo '<option value="'.$arrDeptos["id"].'">🏢 '.$arrDeptos["departamento"].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Messages -->
        <div class="webchat-messages" id="conversas_chat">
            <div class="empty-state">
                <i class="bi bi-chat-left"></i>
                <p>Nenhuma mensagem ainda</p>
                <small>Comece digitando sua primeira mensagem abaixo</small>
            </div>
        </div>

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
                <textarea 
                    id="msgChat" 
                    placeholder="Escreva sua mensagem..." 
                    data-lpignore="true"
                ></textarea>
                <button class="btn-send" id="btnSendMsg" title="Enviar (Enter)" disabled>
                    <i class="bi bi-send-fill"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Editar Mensagem -->
    <div class="modal fade" id="editMessageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Mensagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="editMessageText" class="form-control" rows="4" placeholder="Edite sua mensagem..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSaveEdit">
                        <i class="bi bi-check-circle"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja <strong>deletar</strong> esta mensagem?</p>
                    <p style="color: #999; font-size: 12px;">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                        <i class="bi bi-trash"></i> Deletar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const chatContainer = $('#conversas_chat');
            const departamentoInput = $('#idDepartamentoChat');
            const msgInput = $('#msgChat');
            const btnSend = $('#btnSendMsg');
            const checkboxPrivado = $('#ehPrivada');
            const operadorSelectorDiv = $('#operadorSelectorDiv');
            const operadorSelect = $('#operadorDestino');
            let lastMessageId = 0;
            let isLoading = false;

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
                                    '<option value="' + op.id + '">' + op.nome + '</option>'
                                );
                            });
                        }
                    },
                    error: function(err) {
                        console.error("Erro ao carregar operadores:", err);
                    }
                });
            }

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

            // Auto-resize textarea
            msgInput.on('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                btnSend.prop('disabled', !$(this).val().trim());
            });

            // Enable/disable send button
            msgInput.on('keyup', function() {
                btnSend.prop('disabled', !$(this).val().trim());
            });

            // Helper function to escape HTML
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Load messages with smart update (no flashing)
            function carregaChat(idDepartamento) {
                if (isLoading || $('#carregaWebChat').val() !== "1") return;
                
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

                        // Create temp container to parse new HTML
                        const $newContent = $('<div>').html(data);
                        const newMessageIds = [];
                        
                        // Collect new message IDs
                        $newContent.find('[data-msg-id]').each(function() {
                            newMessageIds.push($(this).data('msg-id'));
                        });

                        // Get current message IDs
                        const currentMessageIds = [];
                        chatContainer.find('[data-msg-id]').each(function() {
                            currentMessageIds.push($(this).data('msg-id'));
                        });

                        // If content is empty state, clear and show
                        if ($newContent.find('.empty-state').length > 0) {
                            chatContainer.html(data);
                            isLoading = false;
                            return;
                        }

                        // If no messages currently, just fill
                        if (currentMessageIds.length === 0) {
                            chatContainer.html(data);
                            scrollToBottom();
                            isLoading = false;
                            return;
                        }

                        let hasChanges = false;

                        // Remove messages that don't exist anymore (fade out)
                        chatContainer.find('[data-msg-id]').each(function() {
                            const msgId = $(this).data('msg-id');
                            if (newMessageIds.indexOf(msgId) === -1) {
                                $(this).fadeOut(200, function() {
                                    $(this).remove();
                                });
                                hasChanges = true;
                            }
                        });

                        // Update existing messages and add new ones
                        $newContent.find('[data-msg-id]').each(function() {
                            const msgId = $(this).data('msg-id');
                            const $existing = chatContainer.find('[data-msg-id="' + msgId + '"]');
                            const $newMsg = $(this);

                            if ($existing.length > 0) {
                                // Message exists, check if it changed
                                const oldText = $existing.find('.message-text').data('original');
                                const newText = $newMsg.find('.message-text').data('original');

                                if (oldText !== newText) {
                                    // Message was edited, update smoothly
                                    $existing.find('.message-text').fadeOut(100, function() {
                                        $(this).data('original', newText).html($newMsg.find('.message-text').html()).fadeIn(100);
                                    });
                                    hasChanges = true;
                                }
                            } else {
                                // New message, append with fade-in
                                $newMsg.hide().appendTo(chatContainer).fadeIn(300);
                                hasChanges = true;
                            }
                        });

                        // Scroll to bottom if new messages were added
                        if (hasChanges) {
                            scrollToBottom();
                        }

                        isLoading = false;
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro ao carregar mensagens:', error);
                        isLoading = false;
                    }
                });
            }

            // Scroll to bottom
            function scrollToBottom() {
                chatContainer.animate({
                    scrollTop: chatContainer.prop('scrollHeight')
                }, 300);
            }

            // Send message
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

            // Send button click
            btnSend.on('click', enviarMensagem);

            // Send with Enter (Shift+Enter for new line)
            msgInput.on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!btnSend.prop('disabled')) {
                        enviarMensagem();
                    }
                }
            });

            // Department change
            $('#mudaDepartamento').on('change', function() {
                departamentoInput.val($(this).val());
                carregaOperadores($(this).val());  // Recarrega operadores do departamento
                carregaChat($(this).val());
            });

            // Edit message handler
            let currentEditMsgId = null;
            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                currentEditMsgId = $(this).data('msg-id');
                const messageText = $(this).closest('.message-item').find('.message-text').data('original');
                
                $('#editMessageText').val(messageText).focus();
                const editModal = new bootstrap.Modal(document.getElementById('editMessageModal'));
                editModal.show();
            });

            // Save edited message
            $('#btnSaveEdit').on('click', function() {
                const novaMensagem = $('#editMessageText').val().trim();
                
                if (!novaMensagem) {
                    alert('Mensagem não pode estar vazia!');
                    return;
                }

                if (!currentEditMsgId) {
                    alert('Erro: ID da mensagem não encontrado.');
                    return;
                }

                $(this).prop('disabled', true).html('<span class="loading-spinner"></span> Salvando...');

                $.ajax({
                    url: '/webchat/editarMensagem.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: currentEditMsgId,
                        mensagem: novaMensagem
                    },
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            // Atualizar a mensagem no DOM sem recarregar
                            const $messageItem = chatContainer.find('[data-msg-id="' + currentEditMsgId + '"]');
                            if ($messageItem.length > 0) {
                                const $messageText = $messageItem.find('.message-text');
                                // Fade out, update, fade in
                                $messageText.fadeOut(100, function() {
                                    $(this).data('original', novaMensagem).html(escapeHtml(novaMensagem).replace(/\n/g, '<br>'));
                                    $(this).fadeIn(100);
                                });
                            }
                            
                            const editModal = bootstrap.Modal.getInstance(document.getElementById('editMessageModal'));
                            editModal.hide();
                        } else {
                            alert('Erro ao editar: ' + (response.error || 'Tente novamente'));
                        }
                        $('#btnSaveEdit').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Salvar');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro:', error);
                        let mensagemErro = 'Erro ao editar mensagem.';
                        
                        if (status === 'timeout') {
                            mensagemErro += ' Tempo limite excedido.';
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            mensagemErro += ' ' + xhr.responseJSON.error;
                        }
                        
                        alert(mensagemErro);
                        $('#btnSaveEdit').prop('disabled', false).html('<i class="bi bi-check-circle"></i> Salvar');
                    }
                });
            });

            // Delete message handler
            let currentDeleteMsgId = null;
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                currentDeleteMsgId = $(this).data('msg-id');
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                deleteModal.show();
            });

            // Confirm delete
            $('#btnConfirmDelete').on('click', function() {
                if (!currentDeleteMsgId) {
                    alert('Erro: ID da mensagem não encontrado.');
                    return;
                }

                $(this).prop('disabled', true).html('<span class="loading-spinner"></span> Deletando...');

                $.ajax({
                    url: '/webchat/deletarMensagem.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: currentDeleteMsgId
                    },
                    timeout: 10000,
                    success: function(response) {
                        if (response.success) {
                            // Remover a mensagem do DOM com animação
                            const $messageItem = chatContainer.find('[data-msg-id="' + currentDeleteMsgId + '"]');
                            if ($messageItem.length > 0) {
                                $messageItem.fadeOut(200, function() {
                                    $(this).remove();
                                });
                            }
                            
                            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                            deleteModal.hide();
                        } else {
                            alert('Erro ao deletar: ' + (response.error || 'Tente novamente'));
                        }
                        $('#btnConfirmDelete').prop('disabled', false).html('<i class="bi bi-trash"></i> Deletar');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro:', error);
                        let mensagemErro = 'Erro ao deletar mensagem.';
                        
                        if (status === 'timeout') {
                            mensagemErro += ' Tempo limite excedido.';
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            mensagemErro += ' ' + xhr.responseJSON.error;
                        }
                        
                        alert(mensagemErro);
                        $('#btnConfirmDelete').prop('disabled', false).html('<i class="bi bi-trash"></i> Deletar');
                    }
                });
            });

            // Auto-refresh messages every 5 seconds
            setInterval(function() {
                carregaChat(departamentoInput.val());
            }, 5000);

            // Initial load
            carregaOperadores(0);  // Carrega operadores inicialmente
            carregaChat(0);
            
            // Scroll para o fim da página ao carregar
            setTimeout(function() {
                window.scrollTo(0, document.body.scrollHeight);
                chatContainer.scrollTop(chatContainer[0].scrollHeight);
            }, 500);
        });
    </script>
</body>
</html>