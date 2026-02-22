<?php
require_once(__DIR__ . '/../../includes/padrao.inc.php');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban - Atendimentos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 0;
            margin: 0;
            overflow-x: hidden;
        }
        
        .kanban-header-title {
            margin-bottom: 10px;
            padding: 15px 10px 0 10px;
            margin: 0;
            font-size: 18px;
        }
        
        .kanban-container {
            display: flex;
            gap: 10px;
            overflow-x: hidden;
            overflow-y: visible;
            padding: 0 10px 15px 10px;
            min-height: auto;
            width: 100%;
            box-sizing: border-box;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        
        .kanban-column {
            background-color: #e8eef5;
            border-radius: 8px;
            padding: 12px;
            min-width: calc(24% - 8px);
            max-width: calc(24% - 8px);
            flex-shrink: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .kanban-column-header {
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 12px;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 2px solid #ccc;
            flex-shrink: 0;
        }
        
        .kanban-column-content {
            overflow-y: auto;
            flex: 1;
            padding-right: 5px;
        }
        
        .kanban-column-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .kanban-column-content::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .kanban-column-content::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        
        .kanban-column-content::-webkit-scrollbar-thumb:hover {
            background: #999;
        }
        
        .kanban-badge {
            background-color: #999;
            color: white;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .kanban-card {
            background-color: white;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: grab;
            transition: all 0.3s ease;
            border-left: 4px solid #4e73df;
            flex-shrink: 0;
            position: relative;
            user-select: none;
        }
        
        .kanban-card.dragging {
            cursor: grabbing;
            opacity: 0.9;
        }
        
        .kanban-card-foto {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ddd;
            object-fit: cover;
            background-color: #f0f0f0;
        }
        
        .kanban-card-avatar {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .kanban-card-content {
            padding-right: 45px;
        }
        
        .kanban-card-visualizar {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 15px;
            height: 15px;
            background: none;
            border: none;
            color: #4e73df;
            cursor: pointer;
            padding: 0;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .kanban-card-visualizar:hover {
            color: #2e5090;
            transform: scale(1.2);
        }
        
        .kanban-card-chat {
            position: absolute;
            bottom: 8px;
            right: 30px;
            width: 15px;
            height: 15px;
            background: none;
            border: none;
            color: #28a745;
            cursor: pointer;
            padding: 0;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .kanban-card-chat:hover {
            color: #1e7e34;
            transform: scale(1.2);
        }
        
        .kanban-card-chat:disabled,
        .kanban-card-chat.disabled {
            color: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .kanban-card-chat:disabled:hover,
        .kanban-card-chat.disabled:hover {
            color: #ccc;
            transform: none;
        }
        
        .kanban-card-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
            font-size: 13px;
            word-wrap: break-word;
        }
        
        .kanban-card-text {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
            word-wrap: break-word;
        }
        
        .kanban-card-numero {
            color: #4e73df;
            font-weight: bold;
            font-size: 10px;
        }
        
        .kanban-card-hora {
            color: #999;
            font-size: 10px;
            margin-top: 4px;
        }
        
        /* Cores por coluna */
        .triagem .kanban-column-header {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffc107;
        }
        
        .triagem .kanban-card {
            border-left-color: #ffc107;
        }
        
        .espera .kanban-column-header {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #17a2b8;
        }
        
        .espera .kanban-card {
            border-left-color: #17a2b8;
        }
        
        .andamento .kanban-column-header {
            background-color: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        
        .andamento .kanban-card {
            border-left-color: #28a745;
        }
        
        .encerrados .kanban-column-header {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        
        .encerrados .kanban-card {
            border-left-color: #dc3545;
        }
        
        .kanban-empty {
            padding: 15px;
            text-align: center;
            color: #999;
            font-style: italic;
            font-size: 12px;
        }
        
        /* Responsivo */
        @media (max-width: 1200px) {
            .kanban-column {
                min-width: calc(33.333% - 11px);
                max-width: calc(33.333% - 11px);
            }
        }
        
        @media (max-width: 900px) {
            .kanban-column {
                min-width: calc(50% - 8px);
                max-width: calc(50% - 8px);
            }
        }
        
        @media (max-width: 600px) {
            .kanban-column {
                min-width: 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }
            
            .kanban-container {
                flex-direction: column;
            }
        }
        
        /* Modal Histórico */
        .window {
            position: fixed !important;
            width: 90% !important;
            height: 90% !important;
            background: #fff !important;
            border-radius: 3px !important;
            box-shadow: 0 0 8px 0 #0000002b !important;
            left: 5% !important;
            top: 5% !important;
            z-index: 1000 !important;
            overflow: auto !important;
            margin: 0 20px !important;
            padding: 20px !important;
            box-sizing: border-box !important;
        }
        
        .window.maior {
            top: 20px !important;
            bottom: 20px !important;
            left: 20px !important;
            right: 20px !important;
            width: auto !important;
            height: auto !important;
            z-index: 1000 !important;
        }
        
        #fundo_preto {
            position: fixed !important;
            left: 0 !important;
            right: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            background: #000000a6 !important;
            z-index: 999 !important;
            display: none;
        }
        
        .window[style*="display: block"] ~ #fundo_preto {
            display: block !important;
        }
        
        /* Responsivo para telas menores */
        @media (max-width: 1024px) {
            .window.maior {
                top: 20px !important;
                bottom: 20px !important;
                left: 20px !important;
                right: 20px !important;
                padding: 15px !important;
            }
            
            ._3AwwN {
                flex-wrap: wrap !important;
            }
            
            ._1WBXd {
                flex: 1 1 100% !important;
                margin-top: 10px !important;
            }
            
            ._1i0-u {
                flex: 0 1 auto !important;
            }
        }
        
        @media (max-width: 768px) {
            .kanban-column {
                min-width: 250px;
                max-width: 250px;
            }
            
            .window.maior {
                top: 15px !important;
                bottom: 15px !important;
                left: 15px !important;
                right: 15px !important;
                padding: 15px !important;
            }
            
            ._3AwwN {
                flex-direction: column !important;
            }
            
            ._18tv- {
                margin-bottom: 10px !important;
            }
            
            ._1WBXd {
                margin-bottom: 10px !important;
            }
        }
        
        @media (max-width: 480px) {
            .window.maior {
                top: 10px !important;
                bottom: 10px !important;
                left: 10px !important;
                right: 10px !important;
                padding: 10px !important;
            }
        }
        
        /* Configurações de CSS das Modais de Finalização */
        .window.menor {
            width: 90% !important;
            max-width: 600px !important;
            height: auto !important;
            max-height: 85vh !important;
            z-index: 1000 !important;
            overflow-y: auto !important;
        }
        
        .window.menor.comprido {
            height: auto !important;
            max-height: 90vh !important;
        }
        
        .box-modal {
            padding: 1.5rem 2rem;
        }
        
        .box-modal .title {
            display: block;
            color: #557cf2;
            font-size: 1.7em;
            font-weight: 700;
            padding-bottom: 1rem;
        }
        
        .box-modal p {
            line-height: 1.3rem;
            padding-bottom: 1rem;
        }
        
        .uk-button-success {
            background-color: #557cf2 !important;
            color: #fff;
            border: 1px solid transparent;
        }
        
        .uk-button {
            border-radius: 3px;
            padding: 8px 16px;
            cursor: pointer;
            border: 1px solid #ddd;
            background-color: #f5f5f5;
        }
        
        .uk-button:hover {
            background-color: #e9e9e9;
        }
        
        .uk-button-success:hover {
            background-color: #4a68d8 !important;
        }
        
        .uk-form-label {
            color: #333;
            font-size: 0.875rem;
            margin-top: 13px;
            margin-bottom: 8px;
        }
        
        .uk-checkbox {
            margin-right: 8px;
            cursor: pointer;
        }
        
        .uk-text-right {
            text-align: right;
        }
        
        .uk-width-1-1@m {
            width: 100%;
        }

        .container-fluid {
            padding: 0;
            margin: 0;
            overflow: visible;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h2 class="kanban-header-title"><i class="fas fa-tasks"></i> Kanban - Atendimentos</h2>
        
        <div class="kanban-container">
            <!-- COLUNA TRIAGEM -->
            <div class="kanban-column triagem">
                <div class="kanban-column-header">
                    <span>📋 Triagem</span>
                    <span class="kanban-badge" id="badge-triagem">0</span>
                </div>
                <div id="col-triagem" class="kanban-column-content">
                </div>
            </div>
            
            <!-- COLUNA AGUARDANDO -->
            <div class="kanban-column espera">
                <div class="kanban-column-header">
                    <span>⏳ Aguardando</span>
                    <span class="kanban-badge" id="badge-espera">0</span>
                </div>
                <div id="col-espera" class="kanban-column-content">
                </div>
            </div>
            
            <!-- COLUNA EM ANDAMENTO -->
            <div class="kanban-column andamento">
                <div class="kanban-column-header">
                    <span>💬 Em Andamento</span>
                    <span class="kanban-badge" id="badge-andamento">0</span>
                </div>
                <div id="col-andamento" class="kanban-column-content">
                </div>
            </div>
            
            <!-- COLUNA ENCERRADOS -->
            <div class="kanban-column encerrados">
                <div class="kanban-column-header">
                    <span>✓ Encerrados</span>
                    <span class="kanban-badge" id="badge-encerrados">0</span>
                </div>
                <div id="col-encerrados" class="kanban-column-content">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- Modal de Histórico -->
    <div class="window maior" id="modalHistorico" style="display: none;">
        <div id="HistoricoAberto" class="_1GX8_">
            <div class="YUoyu" data-asset-chat-background="true"></div>
            <header class="_3AwwN">
                <div class="_1WBXd" role="button">
                    <div class="_2EbF-">
                        <div class="_2zCDG">
                            <span dir="auto" title="Histórico de Mensagens" class="_1wjpf" id="active-name-historico">Histórico</span>
                        </div>
                    </div>
                    <div class="_3sgkv Gd51Q">
                        <span id="numero-historico"></span>
                        Total de mensagens: (<span id="TotalMensagensHistorico" style="font-size:10px">0</span>)
                    </div>
                </div>
                <div class="_1i0-u">
                    <ul class="user-options alt">
                        <li class="tooltip">
                            <a href="javascript:;" id="fecharHistorico">
                                <i class="far fa-times-circle" style="font-size:24px" alt="Fechar Histórico" title="Fechar Histórico"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </header>
            <div class="_3zJZ2 ">
                <div class="copyable-area">
                    <div class="messages-container" id="panel-messages-container2" tabindex="0">
                        <div id="mensagensHistorico"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Fundo Preto -->
    <div id="fundo_preto"></div>
    
    <script>
        function abrirHistorico(numero, nome, protocolo) {
            // Mostrar modal e fundo preto
            $('#modalHistorico').css('display', 'block');
            $('#fundo_preto').css('display', 'block');
            
            // Atualizar header
            $('#active-name-historico').text(nome || 'Histórico');
            $('#numero-historico').text(numero);
            
            // Carregar histórico de mensagens
            var nome_encoded = encodeURIComponent(nome || '');
            var idCanal = '1';
            
            $.post("../../atendimento/qtdConversas.php", {
                numero: numero,
                protocolo: protocolo,
                id: 'all',
                id_canal: idCanal
            }, function(retorno) {
                $.ajax("../../atendimento/listaConversas.php?id=all&numero=" + numero + "&protocolo=" + protocolo + "&nome=" + nome_encoded + "&id_canal=" + idCanal).done(function(data) {
                    $('#mensagensHistorico').html(data);
                    scrollToHistoricoBottom();
                });
                $("#TotalMensagensHistorico").html(retorno);
            });
            
            // Fechar modal ao clicar em X
            $("#fecharHistorico").off('click').on('click', function() {
                $.post("../../includes/deletarArquivos.php", {numero: numero}, function() {
                    $('#modalHistorico').css('display', 'none');
                    $('#fundo_preto').css('display', 'none');
                });
            });
        }
        
        function scrollToHistoricoBottom() {
            $("#panel-messages-container2").animate({ scrollTop: $("#panel-messages-container2")[0].scrollHeight }, 1000);
            if( $("#mensagensHistorico").length ){
                var rolagem = document.getElementById('mensagensHistorico');
                var target = $('#mensagensHistorico');
                target.animate({ scrollTop: rolagem.scrollHeight }, 200);
            }
        }
        
        // Fechar modal ao clicar no fundo preto
        $(document).on('click', '#fundo_preto', function() {
            $('#modalHistorico').css('display', 'none');
            $('#fundo_preto').css('display', 'none');
        });
    </script>
    
    <script>
        function abrirModal(id) {
            var alturaTela = $(window).height();
            var larguraTela = $(window).width();

            //colocando o fundo preto
            $('#fundo_preto').css({'width': larguraTela, 'height': alturaTela});
            $('#fundo_preto').fadeIn(500);	
            $('#fundo_preto').fadeTo("slow", 0.8);

            $(id).show();
            
            // Centralizar o modal
            setTimeout(function() {
                var modalWidth = $(id).outerWidth();
                var modalHeight = $(id).outerHeight();
                var left = (larguraTela - modalWidth) / 2;
                var top = (alturaTela - modalHeight) / 2;
                $(id).css({'top': top + 'px', 'left': left + 'px'});
            }, 50);
            
            $(window).scrollTop(0);
        }

        function fecharModal() {
            $("#fundo_preto").fadeOut(500);
            $(".window").fadeOut(500);
        }
    </script>
    
    <script>
        function carregarKanban() {
            console.log('Carregando kanban...');
            $.ajax({
                url: 'kanban/dados.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Dados recebidos:', data);
                    
                    let counts = {
                        triagem: 0,
                        espera: 0,
                        andamento: 0,
                        encerrados: 0
                    };
                    
                    // Processar atendimentos
                    if(Array.isArray(data) && data.length > 0) {
                        $.each(data, function(idx, atendimento) {
                            console.log('Processando:', atendimento.situacao);
                            
                            // Verificar se o card já existe e obter seu tempo se estiver na mesma situação
                            let tempoExistente = null;
                            let cardExistente = document.querySelector(`.kanban-card[data-unique-id="${atendimento.unique_id}"]`);
                            if (cardExistente) {
                                let situacaoExistente = cardExistente.getAttribute('data-situacao');
                                // Se a situação é a mesma, preservar o tempo
                                if (situacaoExistente === atendimento.situacao) {
                                    tempoExistente = cardExistente.getAttribute('data-tempo-criacao');
                                }
                                // Remover card antigo para ser substituído
                                cardExistente.remove();
                            }
                            
                            let card = criarCard(atendimento, tempoExistente);
                            let coluna = atendimento.situacao;
                            
                            // Mapear situação para o padrão esperado
                            if(coluna === 'T' || coluna === 'TRIAGEM') {
                                $('#col-triagem').append(card);
                                counts.triagem++;
                            } else if(coluna === 'P' || coluna === 'PENDENTE') {
                                $('#col-espera').append(card);
                                counts.espera++;
                            } else if(coluna === 'A' || coluna === 'ATENDENDO') {
                                $('#col-andamento').append(card);
                                counts.andamento++;
                            } else if(coluna === 'F' || coluna === 'FINALIZADO') {
                                $('#col-encerrados').append(card);
                                counts.encerrados++;
                            }
                        });
                    } else {
                        console.log('Nenhum dado recebido ou array vazio');
                    }
                    
                    // Atualizar badges
                    $('#badge-triagem').text(counts.triagem);
                    $('#badge-espera').text(counts.espera);
                    $('#badge-andamento').text(counts.andamento);
                    $('#badge-encerrados').text(counts.encerrados);
                    
                    // Adicionar mensagens vazias
                    if(counts.triagem === 0) $('#col-triagem').html('<div class="kanban-empty">Nenhum atendimento</div>');
                    if(counts.espera === 0) $('#col-espera').html('<div class="kanban-empty">Nenhum atendimento</div>');
                    if(counts.andamento === 0) $('#col-andamento').html('<div class="kanban-empty">Nenhum atendimento</div>');
                    if(counts.encerrados === 0) $('#col-encerrados').html('<div class="kanban-empty">Nenhum atendimento</div>');
                    
                    console.log('Totais:', counts);
                },
                error: function(err) {
                    console.error('Erro ao carregar kanban:', err);
                    $('#col-triagem').html('<div class="kanban-empty">Erro ao carregar</div>');
                }
            });
        }
        
        function criarCard(atendimento, tempoExistente = null) {
            let nome = (atendimento.nome && atendimento.nome.trim()) ? atendimento.nome : 'Sem nome';
            let departamento = (atendimento.departamento && atendimento.departamento.trim()) ? atendimento.departamento : 'Sem Departamento';
            let atendente = (atendimento.atendente && atendimento.atendente.trim()) ? atendimento.atendente : 'Não atribuído';
            let fotoPerfil = (atendimento.foto_perfil && atendimento.foto_perfil.trim()) ? atendimento.foto_perfil : '';
            
            // Usar tempo existente se fornecido, caso contrário criar novo
            let tempoCriacao = tempoExistente || Date.now();
            
            // Verificar se é atendimento finalizado
            let isFinalized = atendimento.situacao === 'F' || atendimento.situacao === 'FINALIZADO';
            
            // Atributo para desabilitar botão de chat se finalizado
            let chatDisabled = isFinalized ? 'disabled' : '';
            let chatClass = isFinalized ? 'disabled' : '';
            
            let fotoHtml = '';
            if(fotoPerfil) {
                fotoHtml = `<img src="${fotoPerfil}" alt="Foto" class="kanban-card-foto">`;
            } else {
                // Gerar iniciais do nome
                let iniciais = nome.split(' ')
                    .map(n => n.charAt(0).toUpperCase())
                    .slice(0, 2)
                    .join('');
                fotoHtml = `<div class="kanban-card-avatar">${iniciais}</div>`;
            }
            
            return `
                <div class="kanban-card kanban-card-draggable" data-unique-id="${atendimento.unique_id}" data-situacao="${atendimento.situacao || ''}" data-tempo-criacao="${tempoCriacao}" data-hr-atend="${atendimento.hr_atend || ''}">
                    ${fotoHtml}
                    <button class="kanban-card-chat ${chatClass}" onclick="if(!this.disabled) { event.stopPropagation(); abrirConversas('${atendimento.numero}'); }" ${chatDisabled} title="Abrir conversa">
                        <i class="fas fa-comment"></i>
                    </button>
                    <button class="kanban-card-visualizar" onclick="event.stopPropagation(); abrirHistorico('${atendimento.numero}', '${nome}', '${atendimento.protocolo}')" title="Visualizar histórico">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="kanban-card-content">
                        <div class="kanban-card-title">${nome}</div>
                        <div class="kanban-card-numero">📱 ${atendimento.numero}</div>
                        <div class="kanban-card-text">Depto: ${departamento}</div>
                        <div class="kanban-card-text">Atendente: ${atendente}</div>
                        <div class="kanban-card-text">Protocolo: <strong>${atendimento.protocolo || 'N/A'}</strong></div>
                        <div class="kanban-card-hora">⏱️ <span id="kanban-hor${atendimento.numero}">00:00:00</span></div>
                    </div>
                </div>
            `;
        }
        
        function abrirConversa(id, numero, nome, id_canal) {
            let url = '../../atendimento/conversa.php?id=' + encodeURIComponent(id) + 
                      '&numero=' + encodeURIComponent(numero) + 
                      '&nome=' + encodeURIComponent(nome) + 
                      '&id_canal=' + encodeURIComponent(id_canal);
            window.location.href = url;
        }
        
        function abrirConversas(numero) {
            // Atualizar status para ANDAMENTO antes de abrir a conversa
            $.ajax({
                url: '../../atendimento/atualizar-status.php',
                type: 'POST',
                data: {
                    numero: numero,
                    situacao: 'A' // A = ANDAMENTO
                },
                success: function(response) {
                    console.log('Status atualizado para ANDAMENTO');
                    // Abrir em nova aba
                    let url = '../../conversas.php?numero=' + encodeURIComponent(numero);
                    window.open(url, '_blank');
                },
                error: function(err) {
                    console.error('Erro ao atualizar status:', err);
                    // Mesmo com erro, abrir em nova aba
                    let url = '../../conversas.php?numero=' + encodeURIComponent(numero);
                    window.open(url, '_blank');
                }
            });
        }
        
        function calcularTempoDecorrido(tempoMsCriacao) {
            if (!tempoMsCriacao) return '00:00:00';
            
            let tempoInicial = parseInt(tempoMsCriacao);
            let agora = Date.now();
            let diferenca = agora - tempoInicial;
            
            if (diferenca > 0) {
                let totalSegundos = Math.floor(diferenca / 1000);
                
                let h = Math.floor(totalSegundos / 3600);
                let m = Math.floor((totalSegundos % 3600) / 60);
                let s = totalSegundos % 60;
                
                return String(h).padStart(2, '0') + ':' + 
                       String(m).padStart(2, '0') + ':' + 
                       String(s).padStart(2, '0');
            }
            
            return '00:00:00';
        }
        
        function atualizarTodosTempos() {
            var cards = document.querySelectorAll('.kanban-card[data-hr-atend]');
            cards.forEach(function(card) {
                // Não atualizar tempo para atendimentos finalizados
                var situacao = card.getAttribute('data-situacao');
                if (situacao === 'F' || situacao === 'FINALIZADO') {
                    return; // Pula este card
                }
                
                var hrAtend = card.getAttribute('data-hr-atend');
                var uniqueId = card.getAttribute('data-unique-id');
                
                if (hrAtend && uniqueId) {
                    // Extrair numero do unique_id (formato: id_numero)
                    var numero = uniqueId.split('_')[1];
                    var tempoSpan = document.getElementById('kanban-hor' + numero);
                    
                    if (tempoSpan) {
                        // Calcular tempo decorrido desde hrAtend até agora
                        var tempoDecorrido = calcularTempoDesdeHora(hrAtend);
                        tempoSpan.textContent = tempoDecorrido;
                    }
                }
            });
        }
        
        function calcularTempoDesdeHora(hrAtend) {
            // hrAtend vem no formato HH:MM:SS
            if (!hrAtend) return '00:00:00';
            
            // Pegar a hora atual
            var agora = new Date();
            var horaAtual = agora.getHours().toString().padStart(2, '0') + ':' + 
                           agora.getMinutes().toString().padStart(2, '0') + ':' + 
                           agora.getSeconds().toString().padStart(2, '0');
            
            // Converter ambas para segundos desde meia-noite
            var [hAtend, mAtend, sAtend] = hrAtend.split(':').map(Number);
            var [hAgora, mAgora, sAgora] = horaAtual.split(':').map(Number);
            
            var segundosAtend = hAtend * 3600 + mAtend * 60 + sAtend;
            var segundosAgora = hAgora * 3600 + mAgora * 60 + sAgora;
            
            // Calcular diferença
            var diferenca = segundosAgora - segundosAtend;
            
            // Se for negativo, significa que passou para o próximo dia
            if (diferenca < 0) {
                diferenca += 24 * 3600; // Adiciona 24 horas em segundos
            }
            
            // Converter de volta para HH:MM:SS
            var h = Math.floor(diferenca / 3600);
            var m = Math.floor((diferenca % 3600) / 60);
            var s = diferenca % 60;
            
            return String(h).padStart(2, '0') + ':' + 
                   String(m).padStart(2, '0') + ':' + 
                   String(s).padStart(2, '0');
        }
        
        // Drag to scroll no kanban card
        $(document).on('mousedown', '.kanban-card-draggable', function(e) {
            var $card = $(this);
            var startY = e.pageY;
            var startScrollTop = $card.closest('.kanban-column-content').scrollTop();
            var isDragging = false;
            
            $(document).on('mousemove', function(e) {
                if (!isDragging) {
                    isDragging = true;
                    $card.addClass('dragging');
                }
                
                var deltaY = e.pageY - startY;
                var $container = $card.closest('.kanban-column-content');
                $container.scrollTop(startScrollTop - deltaY);
            });
            
            $(document).on('mouseup', function() {
                if (isDragging) {
                    $card.removeClass('dragging');
                }
                $(document).off('mousemove');
                $(document).off('mouseup');
            });
        });
        
        
        // Carregar kanban ao abrir a página
        $(document).ready(function() {
            console.log('Página pronta, carregando kanban');
            carregarKanban();
            // Atualizar a cada 10 segundos
            setInterval(carregarKanban, 10000);
            // Atualizar tempos a cada 1 segundo para contagem em tempo real
            setInterval(atualizarTodosTempos, 1000);
        });
    </script>
</body>
</html>
