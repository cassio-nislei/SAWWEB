<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tester ChatSite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { padding: 2rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
        h1 { color: #667eea; margin-bottom: 2rem; }
        .test-section { margin-bottom: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #667eea; }
        .btn-test { margin-bottom: 0.5rem; width: 100%; }
        .response-box { background: #1e1e1e; color: #d4d4d4; padding: 1rem; border-radius: 8px; margin-top: 1rem; max-height: 300px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 0.85rem; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .loading { color: #ffc107; }
        .test-form { background: white; padding: 1rem; border-radius: 8px; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="container-lg">
        <h1><i class="bi bi-flask"></i> ChatSite - Tester</h1>
        
        <!-- Test 1: API Conversas -->
        <div class="test-section">
            <h2><i class="bi bi-chat-left"></i> 1. Listar Conversas</h2>
            <p>Testa se a API de conversas está respondendo corretamente.</p>
            <button class="btn btn-primary btn-test" id="btnTestConversas">
                <i class="bi bi-play-fill"></i> Testar api_conversas.php
            </button>
            <div id="responseConversas" class="response-box" style="display: none;"></div>
        </div>

        <!-- Test 2: Criar Conversa -->
        <div class="test-section">
            <h2><i class="bi bi-plus-circle"></i> 2. Criar Conversa</h2>
            <p>Testa se é possível criar uma nova conversa.</p>
            <div class="test-form">
                <input type="text" id="inputNomeCliente" class="form-control" placeholder="Nome do cliente" value="Teste AutoBot">
                <button class="btn btn-success btn-test mt-2" id="btnTestCriar">
                    <i class="bi bi-plus-circle"></i> Testar criar_conversa.php
                </button>
            </div>
            <div id="responseCriar" class="response-box" style="display: none;"></div>
        </div>

        <!-- Test 3: Carregar Mensagens -->
        <div class="test-section">
            <h2><i class="bi bi-chat-dots"></i> 3. Carregar Mensagens</h2>
            <p>Testa se as mensagens de uma conversa carregam corretamente.</p>
            <div class="test-form">
                <input type="number" id="inputIdConversa" class="form-control" placeholder="ID da Conversa" value="1">
                <button class="btn btn-info btn-test mt-2" id="btnTestMensagens">
                    <i class="bi bi-download"></i> Testar api_mensagens.php
                </button>
            </div>
            <div id="responseMensagens" class="response-box" style="display: none;"></div>
        </div>

        <!-- Test 4: Enviar Resposta -->
        <div class="test-section">
            <h2><i class="bi bi-send"></i> 4. Enviar Resposta</h2>
            <p>Testa se é possível enviar uma resposta do operador.</p>
            <div class="test-form">
                <input type="number" id="inputIdAtendimento" class="form-control" placeholder="ID do Atendimento" value="1">
                <textarea id="inputMensagem" class="form-control mt-2" placeholder="Mensagem" rows="2">Teste de resposta automática</textarea>
                <button class="btn btn-warning btn-test mt-2" id="btnTestResposta">
                    <i class="bi bi-send-fill"></i> Testar enviar_resposta.php
                </button>
            </div>
            <div id="responseResposta" class="response-box" style="display: none;"></div>
        </div>

        <!-- Status Geral -->
        <div class="test-section" id="statusGeral">
            <h2><i class="bi bi-graph-up"></i> Status Geral</h2>
            <div id="statusContent">
                <p class="loading"><i class="bi bi-hourglass-split"></i> Carregando...</p>
            </div>
        </div>

        <!-- Dicas -->
        <div class="alert alert-info mt-4">
            <h4><i class="bi bi-lightbulb"></i> Dicas</h4>
            <ul>
                <li>Todos os testes são <strong>não-destrutivos</strong> (apenas leitura e criação de teste)</li>
                <li>Abra o <strong>DevTools (F12)</strong> e vá para <strong>Console</strong> para ver logs detalhados</li>
                <li>Na aba <strong>Network</strong>, você pode inspecionar as requisições</li>
                <li>Se os testes falharem, verifique o arquivo <strong>status.php</strong></li>
                <li>Se persistir, use o <strong>debug.php</strong> para diagnóstico completo</li>
            </ul>
        </div>
    </div>

    <script>
        // Função utilitária para exibir respostas
        function mostrarResposta(elementId, dados, tipo = 'json') {
            const elem = document.getElementById(elementId);
            elem.style.display = 'block';
            
            if (tipo === 'json') {
                elem.innerHTML = '<span class="success">✓ Sucesso</span>\n' + JSON.stringify(dados, null, 2);
            } else if (tipo === 'erro') {
                elem.innerHTML = '<span class="error">✗ Erro</span>\n' + dados;
            } else {
                elem.innerHTML = dados;
            }
            
            // Autoscroll
            elem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Test 1: Listar Conversas
        document.getElementById('btnTestConversas').addEventListener('click', function() {
            const elem = document.getElementById('responseConversas');
            elem.innerHTML = '<span class="loading"><i class="bi bi-hourglass-split"></i> Carregando...</span>';
            elem.style.display = 'block';
            
            $.ajax({
                url: '/modulos/chatsite/api_conversas.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Conversas carregadas:', data);
                    mostrarResposta('responseConversas', data, 'json');
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    mostrarResposta('responseConversas', 'Erro: ' + error + '\n' + xhr.responseText, 'erro');
                }
            });
        });

        // Test 2: Criar Conversa
        document.getElementById('btnTestCriar').addEventListener('click', function() {
            const nome = document.getElementById('inputNomeCliente').value;
            if (!nome.trim()) {
                alert('Informe um nome para o cliente');
                return;
            }
            
            const elem = document.getElementById('responseCriar');
            elem.innerHTML = '<span class="loading"><i class="bi bi-hourglass-split"></i> Criando...</span>';
            elem.style.display = 'block';
            
            $.ajax({
                url: '/modulos/chatsite/criar_conversa.php',
                type: 'POST',
                data: { nome: nome },
                success: function(response) {
                    console.log('Resposta ao criar:', response);
                    if (response == 1) {
                        mostrarResposta('responseCriar', '✓ Conversa criada com sucesso!', 'json');
                        // Recarregar conversas
                        jQuery.ajax({
                            url: '/modulos/chatsite/api_conversas.php',
                            dataType: 'json',
                            success: function(data) {
                                console.log('Conversas atualizadas:', data);
                            }
                        });
                    } else {
                        mostrarResposta('responseCriar', 'Erro na criação: Código ' + response, 'erro');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    mostrarResposta('responseCriar', 'Erro: ' + error, 'erro');
                }
            });
        });

        // Test 3: Carregar Mensagens
        document.getElementById('btnTestMensagens').addEventListener('click', function() {
            const id = document.getElementById('inputIdConversa').value;
            if (!id) {
                alert('Informe um ID de conversa');
                return;
            }
            
            const elem = document.getElementById('responseMensagens');
            elem.innerHTML = '<span class="loading"><i class="bi bi-hourglass-split"></i> Carregando...</span>';
            elem.style.display = 'block';
            
            $.ajax({
                url: '/modulos/chatsite/api_mensagens.php?id=' + id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Mensagens carregadas:', data);
                    mostrarResposta('responseMensagens', data, 'json');
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    mostrarResposta('responseMensagens', 'Erro: ' + error + '\n' + xhr.responseText, 'erro');
                }
            });
        });

        // Test 4: Enviar Resposta
        document.getElementById('btnTestResposta').addEventListener('click', function() {
            const id = document.getElementById('inputIdAtendimento').value;
            const msg = document.getElementById('inputMensagem').value;
            
            if (!id || !msg.trim()) {
                alert('Informe ID e mensagem');
                return;
            }
            
            const elem = document.getElementById('responseResposta');
            elem.innerHTML = '<span class="loading"><i class="bi bi-hourglass-split"></i> Enviando...</span>';
            elem.style.display = 'block';
            
            $.ajax({
                url: '/modulos/chatsite/enviar_resposta.php',
                type: 'POST',
                data: { 
                    id_atendimento: id,
                    mensagem: msg
                },
                success: function(response) {
                    console.log('Resposta ao enviar:', response);
                    if (response == 1) {
                        mostrarResposta('responseResposta', '✓ Resposta enviada com sucesso!', 'json');
                    } else {
                        mostrarResposta('responseResposta', 'Erro no envio: Código ' + response, 'erro');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    mostrarResposta('responseResposta', 'Erro: ' + error, 'erro');
                }
            });
        });

        // Carregar status geral
        function carregarStatus() {
            $.ajax({
                url: '/modulos/chatsite/status.php',
                type: 'GET',
                dataType: 'html',
                success: function(data) {
                    console.log('Status carregado');
                }
            });
        }

        // Carregar ao iniciar
        carregarStatus();
    </script>
</body>
</html>
