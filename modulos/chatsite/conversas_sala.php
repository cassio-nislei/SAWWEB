<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SAW Chat - Sala de Atendimento</title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --primary: #667eea;
            --primary-dark: #5568d3;
            --secondary: #764ba2;
            --light-bg: #f5f5f5;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Aplicar cor dinâmica do banco de dados */
        <?php 
        $cor = isset($_SESSION["parametros"]["color"]) ? $_SESSION["parametros"]["color"] : "#2c3e50";
        echo ":root { 
            --sidebar-bg: $cor !important;
            --sidebar-hover: " . hex2rgba($cor, 0.8) . " !important;
        }";
        
        function hex2rgba($color, $alpha = 0.5) {
            $color = ltrim($color, '#');
            $hex = array($color[0].$color[1], $color[2].$color[3], $color[4].$color[5]);
            $rgb = array_map('hexdec', $hex);
            return 'rgba('.implode(',', $rgb).',' . $alpha . ')';
        }
        ?>

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            background: white;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
        }

        /* ===== CONTAINER PRINCIPAL ===== */
        .chat-container {
            display: flex;
            flex-direction: row-reverse;
            height: 100vh;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .sidebar-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-title i {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-search {
            padding: 0.75rem;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-search input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            font-size: 0.9rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .sidebar-search input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .sidebar-search input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            outline: none;
            color: white;
        }

        .sidebar-list {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-list::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-list::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .sala-item {
            padding: 0.75rem 0.75rem;
            margin: 0.25rem 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            position: relative;
            user-select: none;
        }

        .sala-item:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        .sala-item.ativo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .sala-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .sala-item.ativo .sala-avatar {
            background: rgba(255, 255, 255, 0.3);
        }

        .sala-info {
            flex: 1;
            min-width: 0;
        }

        .sala-nome {
            font-weight: 600;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sala-status {
            font-size: 0.75rem;
            opacity: 0.7;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sala-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ===== MAIN CHAT ===== */
        .main-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-main-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow);
            flex-shrink: 0;
        }

        .chat-main-header h2 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chat-main-header p {
            margin: 0.25rem 0 0 0;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: #fafafa;
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #bbb;
        }

        .mensagem {
            display: flex;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
            align-items: flex-start;
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

        .mensagem.propria {
            justify-content: flex-end;
        }

        .msg-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .mensagem.propria .msg-avatar {
            background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);
            order: 2;
            margin-right: 0;
            margin-left: 0.75rem;
        }

        .msg-content {
            max-width: 65%;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .msg-header {
            font-size: 0.8rem;
            color: var(--text-muted);
            padding: 0 0.5rem;
        }

        .msg-text {
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            word-wrap: break-word;
            line-height: 1.5;
            color: var(--text-dark);
        }

        .mensagem.propria .msg-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .msg-hora {
            font-size: 0.75rem;
            color: var(--text-muted);
            padding: 0 0.5rem;
        }

        .system-msg {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            padding: 0.75rem;
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        /* ===== CHAT INPUT ===== */
        .chat-input-area {
            padding: 1.25rem;
            background: white;
            border-top: 1px solid var(--border-color);
            flex-shrink: 0;
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }

        .input-wrapper {
            flex: 1;
            display: flex;
            gap: 0.5rem;
        }

        .msg-textarea {
            flex: 1;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.95rem;
            resize: none;
            max-height: 100px;
            transition: all 0.3s ease;
        }

        .msg-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-enviar-msg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .btn-enviar-msg:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-enviar-msg:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            text-align: center;
            padding: 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 1rem;
        }

        .empty-state .btn {
            margin-top: 1rem;
        }

        .filtros-opções {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .filtros-opções .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                right: 0;
                top: 0;
                height: 100vh;
                z-index: 1000;
                transition: transform 0.3s ease;
                width: 250px;
            }

            .sidebar.oculto {
                transform: translateX(100%);
            }

            .main-chat {
                margin-left: 0;
            }

            .toggle-sidebar {
                display: flex !important;
            }

            .msg-content {
                max-width: 80%;
            }

            .chat-main-header h2 {
                font-size: 1rem;
            }

            .chat-messages {
                padding: 1rem;
            }

            .chat-input-area {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
            }

            .msg-content {
                max-width: 85%;
            }

            .chat-messages {
                padding: 0.75rem;
                gap: 0.75rem;
            }

            .chat-input-area {
                flexDirection: column;
                padding: 0.75rem;
            }

            .input-wrapper {
                flex-direction: column;
            }

            .msg-textarea {
                width: 100%;
            }

            .btn-enviar-msg {
                width: 100%;
            }
        }

        .toggle-sidebar {
            display: none;
            background: transparent;
            border: none;
            color: var(--text-dark);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- SIDEBAR -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h1 class="sidebar-title">
                    <i class="bi bi-chat-multiple-fill"></i>
                    <span>SAW Chat</span>
                </h1>
            </div>

            <div class="sidebar-search">
                <input type="text" id="searchSalas" placeholder="Buscar conversas..." autocomplete="off">
            </div>

            <ul class="sidebar-list" id="salasList">
                <li style="padding: 1rem; text-align: center; color: rgba(255, 255, 255, 0.5);">
                    <i class="bi bi-hourglass-split"></i> Carregando conversas...
                </li>
            </ul>
        </div>

        <!-- MAIN CHAT -->
        <div class="main-chat">
            <!-- HEADER -->
            <div class="chat-main-header">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h2 id="salaAtualNome">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Selecione uma Conversa</span>
                </h2>
                <p id="salaAtualStatus">Aguardando seleção...</p>
            </div>

            <!-- MENSAGENS -->
            <div class="chat-messages" id="chatMensagens">
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-chat-left"></i></div>
                    <div style="font-size: 1.1rem; margin-bottom: 0.5rem; font-weight: 500;">Bem-vindo ao Chat SAW</div>
                    <div style="opacity: 0.7; margin-bottom: 1.5rem;">Selecione uma conversa na lista para visualizar e responder</div>
                    <button class="btn btn-primary" id="btnNovaConversa">
                        <i class="bi bi-plus-circle"></i> Iniciar Nova Conversa
                    </button>
                    <div class="filtros-opções">
                        <button class="btn btn-outline-secondary btn-sm" id="btnFiltroTodos">
                            <i class="bi bi-funnel"></i> Todas
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" id="btnFiltroAtivas">
                            <i class="bi bi-circle-fill"></i> Ativas
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" id="btnFiltroPendentes">
                            <i class="bi bi-hourglass-split"></i> Pendentes
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" id="btnFiltroFinalizadas">
                            <i class="bi bi-check-circle-fill"></i> Finalizadas
                        </button>
                    </div>
                </div>
            </div>

            <!-- INPUT -->
            <div class="chat-input-area" id="inputArea" style="display: none;">
                <div class="input-wrapper">
                    <textarea id="msgInput" class="msg-textarea" placeholder="Digite sua mensagem..." rows="1"></textarea>
                    <button class="btn-enviar-msg" id="btnEnviarMsg" title="Enviar">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let salas = [];
        let salaAtual = null;
        let searchFilter = '';
        let filterStatus = [];

        // Auto-resize textarea
        const textarea = document.getElementById('msgInput');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            });
        }

        // Toggle sidebar mobile
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('oculto');
        });

        // Carregar salas
        function carregarSalas() {
            console.log('📡 Carregando conversas...');
            $.ajax({
                url: '/modulos/chatsite/api_conversas.php',
                type: 'GET',
                dataType: 'json',
                timeout: 10000,  // 10 segundos de timeout
                success: function(data) {
                    console.log('✅ Resposta recebida:', data);
                    
                    // Verificar se retornou erro
                    if (data && data.error) {
                        console.error('❌ Erro da API:', data.message);
                        renderizarErro('Erro na API: ' + (data.message || 'Erro desconhecido'));
                        return;
                    }
                    
                    if (!Array.isArray(data)) {
                        console.error('❌ Resposta inválida (não é array):', typeof data, data);
                        data = [];
                    }
                    salas = data;
                    console.log('📊 Total de conversas carregadas:', salas.length);
                    renderizarSalas();
                },
                error: function(xhr, status, error) {
                    console.error('❌ Erro AJAX:', {
                        status: status,
                        error: error,
                        statusCode: xhr.status,
                        response: xhr.responseText
                    });
                    
                    let mensagem = 'Erro ao carregar conversas. ';
                    
                    if (xhr.status === 0) {
                        mensagem += 'Verifique sua conexão de internet.';
                    } else if (xhr.status === 404) {
                        mensagem += 'API não encontrada (api_conversas.php).';
                    } else if (xhr.status === 500) {
                        mensagem += 'Erro no servidor: ' + xhr.responseText.substring(0, 100);
                    } else if (status === 'timeout') {
                        mensagem += 'Tempo limite de requisição excedido.';
                    } else if (status === 'parsererror') {
                        mensagem += 'Erro ao processar resposta. Verifique console.';
                    }
                    
                    renderizarErro(mensagem + ' Por favor, recarregue a página.');
                }
            });
        }

        // Renderizar salas no sidebar
        function renderizarSalas() {
            let filtered = salas.filter(s => {
                let passaBusca = !searchFilter || 
                    s.nome.toLowerCase().includes(searchFilter.toLowerCase()) ||
                    s.numero.includes(searchFilter);
                
                let passaFiltro = filterStatus.length === 0 || filterStatus.includes(s.situacao);
                
                return passaBusca && passaFiltro;
            });

            let html = '';
            
            if (filtered.length === 0) {
                html = `
                    <li style="padding: 1.5rem; text-align: center; color: rgba(255, 255, 255, 0.5); margin-top: 2rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;"><i class="bi bi-inbox"></i></div>
                        <div>${searchFilter ? 'Nenhuma conversa encontrada' : 'Nenhuma conversa ativa'}</div>
                        <div style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.7;">Clique em "Nova Conversa" para iniciar</div>
                    </li>
                `;
            } else {
                filtered.forEach(sala => {
                    let inicial = sala.nome.charAt(0).toUpperCase();
                    let ativo = salaAtual === sala.idatendimento ? 'ativo' : '';
                    
                    html += `
                        <li class="sala-item ${ativo}" onclick="selecionarSala(${sala.idatendimento})">
                            <div class="sala-avatar">${inicial}</div>
                            <div class="sala-info">
                                <div class="sala-nome">${sala.nome}</div>
                                <div class="sala-status">#${sala.numero}</div>
                            </div>
                            ${sala.qtd_msg_novas > 0 ? `<div class="sala-badge">${sala.qtd_msg_novas}</div>` : ''}
                        </li>
                    `;
                });
            }

            document.getElementById('salasList').innerHTML = html;
        }

        // Renderizar erro
        function renderizarErro(msg) {
            const html = `
                <li style="padding: 2rem; text-align: center; color: #ff6b6b;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;"><i class="bi bi-exclamation-triangle"></i></div>
                    <div style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.1rem;">Erro ao Carregar Conversas</div>
                    <div style="margin-bottom: 1.5rem; font-size: 0.95rem; line-height: 1.5;">${msg}</div>
                    <div style="margin-bottom: 1rem;">
                        <button onclick="carregarSalas()" class="btn btn-sm btn-danger">
                            <i class="bi bi-arrow-clockwise"></i> Tentar Novamente
                        </button>
                    </div>
                    <div style="font-size: 0.75rem; color: rgba(255, 107, 107, 0.7); border-top: 1px solid rgba(255, 107, 107, 0.3); padding-top: 1rem; margin-top: 1rem;">
                        <strong>Dica:</strong> Se o erro persistir:
                        <ol style="text-align: left; margin-top: 0.5rem;">
                            <li>Abra <strong>DevTools (F12)</strong> → <strong>Console</strong></li>
                            <li>Procure por mensagens em vermelho</li>
                            <li>Verifique a aba <strong>Network</strong></li>
                            <li>Teste <strong>api_conversas.php</strong> direto</li>
                            <li>Acesse <strong>status.php</strong> para diagnóstico</li>
                        </ol>
                    </div>
                </li>
            `;
            document.getElementById('salasList').innerHTML = html;
        }

        // Selecionar sala
        function selecionarSala(id) {
            salaAtual = id;
            document.getElementById('sidebar').classList.add('oculto');
            renderizarSalas();
            carregarMensagens();
        }

        // Carregar mensagens da sala
        function carregarMensagens() {
            if (!salaAtual) return;

            $.ajax({
                url: '/modulos/chatsite/api_mensagens.php?id=' + salaAtual,
                type: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(data) {
                    if (data.error) {
                        console.error('API Error (carregarMensagens):', data.message);
                        $('#chatMensagens').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> ' + data.message + '</div>');
                        return;
                    }

                    // Atualizar header
                    $('#salaAtualNome span').text(data.conversa.nome_cliente);
                    $('#salaAtualStatus').html(`<i class="bi bi-circle-fill" style="color: #28a745; font-size: 0.6rem; margin-right: 0.5rem;"></i> #${data.conversa.numero}`);

                    // Renderizar mensagens
                    let html = '';
                    if (data.mensagens.length === 0) {
                        html = '<div class="system-msg"><i class="bi bi-chat-left"></i> Nenhuma mensagem ainda</div>';
                    } else {
                        data.mensagens.forEach((msg, idx) => {
                            let inicial = msg.nome_chat.charAt(0).toUpperCase();
                            let propria = msg.id_atend == 0 ? 'propria' : '';
                            
                            // Mostrar separador de data
                            if (idx === 0 || (idx > 0 && data.mensagens[idx - 1].dt_msg !== msg.dt_msg)) {
                                html += `<div class="system-msg">${msg.dt_msg}</div>`;
                            }

                            html += `
                                <div class="mensagem ${propria}">
                                    <div class="msg-avatar">${inicial}</div>
                                    <div class="msg-content">
                                        <div class="msg-header">${msg.nome_chat}</div>
                                        <div class="msg-text">${msg.msg}</div>
                                        <div class="msg-hora">${msg.hr_msg}</div>
                                    </div>
                                </div>
                            `;
                        });
                    }

                    $('#chatMensagens').html(html);
                    $('#chatMensagens').scrollTop($('#chatMensagens')[0].scrollHeight);
                    $('#inputArea').show();
                    $('#msgInput').focus();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error (carregarMensagens):', status, error);
                    let msg = 'Erro ao carregar mensagens';
                    if (status === 'timeout') {
                        msg = 'Tempo limite de carregamento excedido';
                    } else if (status === 'error' && xhr.status === 404) {
                        msg = 'API não encontrada (api_mensagens.php)';
                    }
                    $('#chatMensagens').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> ' + msg + '</div>');
                }
            });
        }

        // Enviar mensagem
        document.getElementById('btnEnviarMsg').addEventListener('click', function() {
            let msg = $('#msgInput').val().trim();
            if (!msg) return;

            $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

            $.ajax({
                url: '/modulos/chatsite/enviar_resposta.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    id_atendimento: salaAtual,
                    mensagem: msg
                },
                timeout: 10000,
                success: function(response) {
                    if (response.error) {
                        console.error('❌ Erro ao enviar:', response.message);
                        alert('Erro ao enviar mensagem:\n\n' + response.message);
                    } else {
                        console.log('✅ Mensagem enviada:', response);
                        $('#msgInput').val('').css('height', 'auto');
                        carregarMensagens();
                    }
                    $('#btnEnviarMsg').prop('disabled', false).html('<i class="bi bi-send-fill"></i>');
                },
                error: function(xhr, status, error) {
                    console.error('❌ Erro AJAX:', error);
                    alert('Erro ao enviar mensagem. Tente novamente.');
                    $('#btnEnviarMsg').prop('disabled', false).html('<i class="bi bi-send-fill"></i>');
                }
            });
        });

        // Enviar com Shift+Enter
        document.getElementById('msgInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('btnEnviarMsg').click();
            }
        });

        // Nova Conversa
        document.getElementById('btnNovaConversa').addEventListener('click', function() {
            let nome = prompt('Informe o nome do cliente:');
            if (!nome || !nome.trim()) return;

            console.log('➕ Criando nova conversa com nome:', nome);
            
            $.ajax({
                url: '/modulos/chatsite/criar_conversa.php',
                type: 'POST',
                dataType: 'json',
                data: { nome: nome.trim() },
                timeout: 10000,
                success: function(response) {
                    if (response.error) {
                        console.error('❌ Erro ao criar:', response.message);
                        alert('Erro ao criar conversa:\n\n' + response.message);
                    } else {
                        console.log('✅ Conversa criada com sucesso:', response);
                        alert('✅ Conversa criada com sucesso! #' + response.numero);
                        carregarSalas();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Erro AJAX:', error);
                    let mensagem = 'Erro ao criar conversa. ';
                    if (status === 'timeout') {
                        mensagem += 'Tempo limite excedido.';
                    } else if (xhr.status === 500) {
                        mensagem += xhr.responseText.substring(0, 100);
                    } else {
                        mensagem += error;
                    }
                    alert(mensagem);
                }
            });
        });

        // Filtros - Valores para status
        const statusMap = {
            'todos': ['ativo', 'pendente', 'finalizado'],
            'ativo': ['ativo'],
            'pendentes': ['pendente'],
            'finalizadas': ['finalizado']
        };

        // Botões de filtro
        if (document.getElementById('btnFiltroTodos')) {
            document.getElementById('btnFiltroTodos').addEventListener('click', function() {
                searchFilter = '';
                filterStatus = [];
                document.getElementById('searchSalas').value = '';
                renderizarSalas();
            });
        }

        if (document.getElementById('btnFiltroAtivas')) {
            document.getElementById('btnFiltroAtivas').addEventListener('click', function() {
                searchFilter = '';
                filterStatus = ['ativo'];
                document.getElementById('searchSalas').value = '';
                renderizarSalas();
            });
        }

        if (document.getElementById('btnFiltroPendentes')) {
            document.getElementById('btnFiltroPendentes').addEventListener('click', function() {
                searchFilter = '';
                filterStatus = ['pendente'];
                document.getElementById('searchSalas').value = '';
                renderizarSalas();
            });
        }

        if (document.getElementById('btnFiltroFinalizadas')) {
            document.getElementById('btnFiltroFinalizadas').addEventListener('click', function() {
                searchFilter = '';
                filterStatus = ['finalizado'];
                document.getElementById('searchSalas').value = '';
                renderizarSalas();
            });
        }

        // Buscar salas
        document.getElementById('searchSalas').addEventListener('input', function() {
            searchFilter = this.value;
            renderizarSalas();
        });

        // Carregar inicial
        carregarSalas();
        setInterval(carregarSalas, 5000);
    </script>
</body>
</html>
