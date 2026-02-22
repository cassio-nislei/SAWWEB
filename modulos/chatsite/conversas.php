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
    <title>SAW Chat - Central de Conversas</title>
    
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
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #198754 0%, #0d6efd 100%);
            --danger-gradient: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        body {
            background: var(--light-bg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
        }

        /* ===== HEADER ===== */
        .chat-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem 1.5rem;
            border-bottom: none;
            box-shadow: var(--shadow-lg);
        }

        .chat-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chat-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.95;
            font-size: 0.95rem;
        }

        /* ===== CONTROLS ===== */
        .controls-section {
            background: white;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .search-wrapper {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-wrapper input {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .search-wrapper input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .search-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            background: white;
            border: 2px solid var(--border-color);
            color: var(--text-dark);
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }

        /* ===== CONTAINER ===== */
        .conversas-container {
            padding: 2rem 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* ===== CARDS ===== */
        .conversa-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .conversa-card {
                display: grid;
                grid-template-columns: auto 1fr auto;
                align-items: center;
                gap: 1.5rem;
            }
        }

        .conversa-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
            border-color: #667eea;
        }

        .conversa-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .conversa-info {
            flex: 1;
            min-width: 0;
        }

        .conversa-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .conversa-nome {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .conversa-numero {
            font-size: 0.8rem;
            color: var(--text-muted);
            background: var(--light-bg);
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            display: inline-block;
            margin-top: 0.25rem;
        }

        .conversa-msg {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
        }

        .conversa-meta {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .conversa-tempo {
            font-size: 0.85rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .conversa-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-ativo {
            background: #d4edda;
            color: #155724;
        }

        .status-pendente {
            background: #fff3cd;
            color: #856404;
        }

        .status-finalizado {
            background: #f8d7da;
            color: #721c24;
        }

        .conversa-badge {
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .conversa-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-acao {
            background: white;
            border: 2px solid var(--border-color);
            color: var(--text-dark);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-acao:hover {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        /* ===== MODAL ===== */
        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 12px 12px 0 0;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            .chat-header h1 {
                font-size: 1.5rem;
            }

            .conversas-container {
                padding: 1rem 0.75rem;
            }

            .conversa-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .filter-buttons {
                width: 100%;
            }

            .filter-btn {
                flex: 1;
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }

            .controls-section {
                flex-direction: column;
                align-items: stretch;
            }

            .search-wrapper {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="chat-header">
        <h1><i class="bi bi-chat-multiple-fill"></i> Central de Conversas</h1>
        <p><i class="bi bi-info-circle"></i> Gerencie todas as conversas ativas com os clientes</p>
    </div>

    <!-- CONTROLS -->
    <div class="controls-section">
        <div class="search-wrapper">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar por nome ou número...">
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="todos">
                <i class="bi bi-funnel"></i> Todos
            </button>
            <button class="filter-btn" data-filter="ativo">
                <i class="bi bi-circle-fill"></i> Ativos
            </button>
            <button class="filter-btn" data-filter="pendente">
                <i class="bi bi-hourglass-split"></i> Pendentes
            </button>
            <button class="filter-btn" data-filter="finalizado">
                <i class="bi bi-check-circle-fill"></i> Finalizados
            </button>
        </div>
    </div>

    <!-- CONVERSAS -->
    <div class="conversas-container">
        <div id="conversasLista">
            <!-- Cards carregados aqui -->
        </div>
        <div id="emptyState" class="empty-state" style="display: none;">
            <div class="empty-icon"><i class="bi bi-chat-left"></i></div>
            <div class="empty-title">Nenhuma conversa encontrada</div>
            <p>Não há conversas para exibir no momento.</p>
        </div>
    </div>

    <!-- MODAL DE CONVERSA -->
    <div class="modal fade" id="conversaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modalClienteNome"></h5>
                        <small id="modalClienteNumero" style="opacity: 0.7;"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalMessagens" style="height: 400px; overflow-y: auto; background: #f8f9fa;">
                    <!-- Mensagens aqui -->
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                    <div style="width: 100%;">
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <textarea id="respostaMsg" placeholder="Digite sua resposta..." class="form-control" rows="2" style="resize: none; border-radius: 8px;"></textarea>
                            <button type="button" class="btn btn-primary" id="btnEnviarResposta" style="align-self: flex-end;">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let conversas = [];
        let filtroAtual = 'todos';
        let searchAtual = '';
        let conversaSelecionada = null;

        // Carregar conversas
        function carregarConversas() {
            $.ajax({
                url: '/modulos/chatsite/api_conversas.php',
                type: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(data) {
                    if (data && data.error) {
                        console.error('Erro:', data.message);
                        alert('Erro ao carregar conversas: ' + data.message);
                        return;
                    }
                    if (!Array.isArray(data)) {
                        console.error('Resposta inválida:', data);
                        conversas = [];
                    } else {
                        conversas = data;
                    }
                    renderizarConversas();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    alert('Erro ao carregar conversas: ' + (status === 'timeout' ? 'Tempo limite excedido' : status));
                }
            });
        }

        // Renderizar cards
        function renderizarConversas() {
            let filtered = conversas.filter(c => {
                let passaFiltro = filtroAtual === 'todos' || c.situacao === filtroAtual;
                let passaBusca = !searchAtual || 
                    c.nome.toLowerCase().includes(searchAtual.toLowerCase()) ||
                    c.numero.includes(searchAtual);
                return passaFiltro && passaBusca;
            });

            let html = '';
            if (filtered.length === 0) {
                document.getElementById('emptyState').style.display = 'block';
                document.getElementById('conversasLista').innerHTML = '';
                return;
            }

            document.getElementById('emptyState').style.display = 'none';

            filtered.forEach(c => {
                let statusClass = 'status-' + c.situacao;
                let statusText = {
                    'ativo': 'Ativo',
                    'pendente': 'Pendente',
                    'finalizado': 'Finalizado'
                }[c.situacao] || c.situacao;

                let inicial = c.nome.charAt(0).toUpperCase();
                let tempoDecorrido = formatarTempo(c.dt_inicio);

                html += `
                    <div class="conversa-card" onclick="abrirConversa(${c.idatendimento})">
                        <div class="conversa-avatar">${inicial}</div>
                        <div class="conversa-info">
                            <div class="conversa-header">
                                <h5 class="conversa-nome">${c.nome}</h5>
                            </div>
                            <div class="conversa-numero">#${c.numero}</div>
                            <p class="conversa-msg">${c.ultima_msg || 'Nenhuma mensagem'}</p>
                            <div class="conversa-meta">
                                <span class="conversa-tempo"><i class="bi bi-clock"></i> ${tempoDecorrido}</span>
                                <span class="conversa-status ${statusClass}">
                                    <i class="bi bi-circle-fill"></i> ${statusText}
                                </span>
                            </div>
                        </div>
                        <div class="conversa-actions" onclick="event.stopPropagation();">
                            ${c.qtd_msg_novas > 0 ? `
                                <div class="conversa-badge" title="${c.qtd_msg_novas} novas mensagens">
                                    ${c.qtd_msg_novas}
                                </div>
                            ` : ''}
                            <button class="btn-acao" title="Abrir">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            document.getElementById('conversasLista').innerHTML = html;
        }

        function formatarTempo(dataStr) {
            const data = new Date(dataStr);
            const agora = new Date();
            const diff = agora - data;
            
            const minutos = Math.floor(diff / 60000);
            const horas = Math.floor(diff / 3600000);
            const dias = Math.floor(diff / 86400000);
            
            if (minutos < 1) return 'Agora';
            if (minutos < 60) return `Há ${minutos}m`;
            if (horas < 24) return `Há ${horas}h`;
            if (dias < 7) return `Há ${dias}d`;
            
            return data.toLocaleDateString('pt-BR');
        }

        function abrirConversa(id) {
            conversaSelecionada = id;
            $.ajax({
                url: '/modulos/chatsite/api_mensagens.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(data) {
                    if (data.error) {
                        console.error('Erro:', data.message);
                        alert('Erro: ' + data.message);
                        return;
                    }
                    
                    // Preencher dados do modal
                    $('#modalClienteNome').text(data.conversa.nome_cliente);
                    $('#modalClienteNumero').text('#' + data.conversa.numero + ' | Status: ' + data.conversa.situacao);
                    
                    // Renderizar mensagens
                    let html = '';
                    data.mensagens.forEach(msg => {
                        const isAtendente = parseInt(msg.id_atend) > 0;
                        const inicial = msg.nome_chat.charAt(0).toUpperCase();
                        
                        if (isAtendente) {
                            html += `
                                <div style="margin-bottom: 1rem; display: flex; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; flex-shrink: 0;">
                                        ${inicial}
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.25rem;">
                                            <strong>${msg.nome_chat}</strong> • ${msg.hr_msg}
                                        </div>
                                        <div style="background: white; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid #e9ecef;">
                                            ${msg.msg}
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            html += `
                                <div style="margin-bottom: 1rem; display: flex; gap: 0.75rem; justify-content: flex-end;">
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.85rem; color: #666; margin-bottom: 0.25rem; text-align: right;">
                                            <strong>${msg.nome_chat}</strong> • ${msg.hr_msg}
                                        </div>
                                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.75rem 1rem; border-radius: 8px; display: inline-block; max-width: 70%;">
                                            ${msg.msg}
                                        </div>
                                    </div>
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #198754; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; flex-shrink: 0;">
                                        ${inicial}
                                    </div>
                                </div>
                            `;
                        }
                    });
                    
                    $('#modalMessagens').html(html);
                    $('#modalMessagens').scrollTop($('#modalMessagens')[0].scrollHeight);
                    $('#respostaMsg').val('');
                    
                    // Mostrar modal
                    new bootstrap.Modal(document.getElementById('conversaModal')).show();
                },
                error: function() {
                    alert('Erro ao carregar mensagens');
                }
            });
        }

        // Enviar resposta
        document.getElementById('btnEnviarResposta').addEventListener('click', function() {
            const msg = $('#respostaMsg').val().trim();
            if (!msg) return;
            
            $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split spin"></i>');
            
            // Aqui você implementaria o envio da resposta
            // Por enquanto, apenas limpa
            setTimeout(() => {
                $('#respostaMsg').val('');
                $(this).prop('disabled', false).html('<i class="bi bi-send"></i>');
            }, 500);
        });

        // Filtros
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filtroAtual = this.dataset.filter;
                renderizarConversas();
            });
        });

        // Busca
        document.getElementById('searchInput').addEventListener('input', function() {
            searchAtual = this.value;
            renderizarConversas();
        });

        // Carregar ao iniciar
        carregarConversas();
        setInterval(carregarConversas, 5000); // Atualiza a cada 5 segundos

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </script>
</body>
</html>
