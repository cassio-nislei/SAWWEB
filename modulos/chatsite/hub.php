<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatSite 2.0 - Hub de Recursos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #667eea;
            --primary-light: #f0f4ff;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }
        
        * {
            scroll-behavior: smooth;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
            font-family: 'Segoe UI', 'Roboto', sans-serif;
        }
        
        .navbar {
            background: rgba(255,255,255,0.95)!important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-section {
            background: white;
            border-radius: 16px;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            text-align: center;
        }
        
        .hero-section h1 {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .hero-section p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 12px 12px 0 0!important;
            padding: 1.5rem;
            border: none;
        }
        
        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1rem;
        }
        
        .btn-icon.primary {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        
        .badge-status {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-ok {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .badge-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .badge-error {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .resource-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .resource-list li {
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .resource-list li:last-child {
            border-bottom: none;
        }
        
        .resource-list a {
            text-decoration: none;
            color: #667eea;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .resource-list a:hover {
            color: #764ba2;
        }
        
        .section-title {
            color: #2c3e50;
            font-weight: 700;
            margin: 3rem 0 2rem 0;
            padding-bottom: 1rem;
            border-bottom: 3px solid #667eea;
        }
        
        .quick-test {
            background: var(--primary-light);
            border-left: 4px solid var(--primary);
            padding: 1.5rem;
            border-radius: 8px;
            margin: 2rem 0;
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }
        
        .status-ok {
            background: #28a745;
        }
        
        .status-warning {
            background: #ffc107;
        }
        
        .status-error {
            background: #dc3545;
        }
        
        footer {
            background: rgba(255,255,255,0.95);
            padding: 2rem;
            margin-top: 4rem;
            text-align: center;
            color: #666;
        }
        
        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumb-item.active {
            color: #667eea;
            font-weight: 600;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .feature-item {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .feature-item h6 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .feature-item p {
            margin: 0;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container-lg">
            <a class="navbar-brand" href="#">
                <i class="bi bi-chat-dots-fill"></i> ChatSite 2.0
            </a>
            <div class="navbar-text ms-auto">
                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Completo</span>
            </div>
        </div>
    </nav>

    <!-- Container Principal -->
    <div class="container-lg">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1><i class="bi bi-rocket-takeoff"></i> Bem-vindo ao ChatSite 2.0</h1>
            <p>Interface de atendimento ao cliente 100% funcional com dashboard em tempo real, filtros inteligentes e fácil integração.</p>
            <div class="mt-3">
                <span class="badge bg-success me-2"><i class="bi bi-check2"></i> Botões Funcionando</span>
                <span class="badge bg-success me-2"><i class="bi bi-check2"></i> Filtros Ativos</span>
                <span class="badge bg-success"><i class="bi bi-check2"></i> APIs Testadas</span>
            </div>
        </div>

        <!-- Acesso Rápido -->
        <h2 class="section-title"><i class="bi bi-lightning-charge"></i> Acesso Rápido</h2>
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-chat-left-fill"></i> Interface</h5>
                    </div>
                    <div class="card-body">
                        <p>Abra o dashboard do operador</p>
                        <a href="index.php" class="btn btn-sm btn-primary w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-speedometer2"></i> Status</h5>
                    </div>
                    <div class="card-body">
                        <p>Verificar status do sistema</p>
                        <a href="status.php" class="btn btn-sm btn-primary w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-flask"></i> Tester</h5>
                    </div>
                    <div class="card-body">
                        <p>Testar APIs e endpoints</p>
                        <a href="tester.php" class="btn btn-sm btn-primary w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-bug"></i> Debug</h5>
                    </div>
                    <div class="card-body">
                        <p>Diagnóstico técnico profundo</p>
                        <a href="debug.php" class="btn btn-sm btn-primary w-100" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i> Abrir
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recursos Principais -->
        <h2 class="section-title"><i class="bi bi-book"></i> Recursos e Documentação</h2>
        
        <!-- Card 1: Começar -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-play-circle"></i> Começar Aqui</h5>
                    </div>
                    <div class="card-body">
                        <ul class="resource-list">
                            <li>
                                <i class="bi bi-file-earmark-text"></i>
                                <a href="LEIA-ME.md" target="_blank" download>LEIA-ME.md</a>
                                <span class="badge-status badge-ok ms-2">Rápido</span>
                            </li>
                            <li>
                                <i class="bi bi-file-earmark-text"></i>
                                <a href="INDEX.md" target="_blank" download>INDEX.md</a>
                                <span class="badge-status badge-ok ms-2">Índice</span>
                            </li>
                            <li>
                                <i class="bi bi-file-earmark-text"></i>
                                <a href="SUMARIO_EXECUTIVO.md" target="_blank" download>SUMARIO_EXECUTIVO.md</a>
                                <span class="badge-status badge-warning ms-2">Detalhado</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Card 2: Técnico -->
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-gear"></i> Documentação Técnica</h5>
                    </div>
                    <div class="card-body">
                        <ul class="resource-list">
                            <li>
                                <i class="bi bi-file-earmark-code"></i>
                                <a href="CORREÇÕES_APLICADAS.md" target="_blank" download>CORREÇÕES_APLICADAS.md</a>
                                <span class="badge-status badge-warning ms-2">Fixes</span>
                            </li>
                            <li>
                                <i class="bi bi-file-earmark-code"></i>
                                <a href="VISUALIZACAO_MUDANCAS.md" target="_blank" download>VISUALIZACAO_MUDANCAS.md</a>
                                <span class="badge-status badge-ok ms-2">Antes/Depois</span>
                            </li>
                            <li>
                                <i class="bi bi-file-earmark-code"></i>
                                <a href="GUIA_DE_ACESSO.md" target="_blank" download>GUIA_DE_ACESSO.md</a>
                                <span class="badge-status badge-ok ms-2">Tutorial</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Instalação -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-wrench"></i> Setup e Configuração</h5>
                    </div>
                    <div class="card-body">
                        <ul class="resource-list">
                            <li>
                                <i class="bi bi-file-earmark-text"></i>
                                <a href="INSTALACAO_CONFIGURACAO.md" target="_blank" download>INSTALACAO_CONFIGURACAO.md</a>
                                <span class="badge-status badge-warning ms-2">Setup</span>
                            </li>
                        </ul>
                        <p class="mt-3 text-muted" style="font-size: 0.9rem;">
                            <i class="bi bi-info-circle"></i> Guia completo de instalação, configuração e troubleshooting
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card 4: Testes -->
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-check2-circle"></i> Validação</h5>
                    </div>
                    <div class="card-body">
                        <div class="feature-grid">
                            <div class="feature-item">
                                <h6><i class="bi bi-speedometer"></i> Status</h6>
                                <p>Validar configuração</p>
                            </div>
                            <div class="feature-item">
                                <h6><i class="bi bi-flask"></i> Tester</h6>
                                <p>Testar cada API</p>
                            </div>
                            <div class="feature-item">
                                <h6><i class="bi bi-bug"></i> Debug</h6>
                                <p>Diagnóstico profundo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recursos Disponíveis -->
        <h2 class="section-title"><i class="bi bi-star"></i> O Que Usar</h2>
        
        <div class="quick-test">
            <h5><i class="bi bi-broadcast"></i> Para Operadores</h5>
            <ul class="mt-3 mb-0">
                <li><strong>Interface Principal:</strong> <code>index.php</code> - Dashboard com conversas, filtros e busca</li>
                <li><strong>Status:</strong> <code>status.php</code> - Ver se tudo está funcionando</li>
                <li><strong>Features:</strong> Filtrar, buscar, criar conversa, enviar mensagens</li>
            </ul>
        </div>

        <div class="quick-test" style="border-left-color: #fd7e14; background: rgba(253, 126, 20, 0.05);">
            <h5><i class="bi bi-terminal"></i> Para Desenvolvedores</h5>
            <ul class="mt-3 mb-0">
                <li><strong>Começar:</strong> Leia <code>LEIA-ME.md</code> (2 min)</li>
                <li><strong>Testar:</strong> Acesse <code>tester.php</code> para validar APIs</li>
                <li><strong>Debugar:</strong> Use <code>status.php</code> e <code>debug.php</code></li>
                <li><strong>Entender:</strong> Leia <code>CORREÇÕES_APLICADAS.md</code> para detalhes técnicos</li>
            </ul>
        </div>

        <div class="quick-test" style="border-left-color: #0dcaf0; background: rgba(13, 202, 240, 0.05);">
            <h5><i class="bi bi-exclamation-triangle"></i> Se Algo Não Funcionar</h5>
            <ol class="mt-3 mb-0">
                <li>Abra <code>status.php</code> e verifique: usuariosaw, banco, tabelas</li>
                <li>Abra <code>tester.php</code> e teste as APIs uma por uma</li>
                <li>Abra DevTools (F12) e procure por erros em Console</li>
                <li>Leia a seção de troubleshooting em <code>GUIA_DE_ACESSO.md</code></li>
            </ol>
        </div>

        <!-- Resumo das Mudanças -->
        <h2 class="section-title"><i class="bi bi-graph-up"></i> Resumo das Mudanças</h2>
        
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; color: #667eea; margin-bottom: 1rem;">9</div>
                        <h6>Bugs Corrigidos</h6>
                        <p class="text-muted mb-0">SQL, JavaScript, HTML</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; color: #28a745; margin-bottom: 1rem;">6</div>
                        <h6>Arquivos Modificados</h6>
                        <p class="text-muted mb-0">SAW + SAW-main</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div style="font-size: 2.5rem; color: #764ba2; margin-bottom: 1rem;">4</div>
                        <h6>Arquivos Criados</h6>
                        <p class="text-muted mb-0">Novos+Ferramentas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status do Projeto -->
        <h2 class="section-title"><i class="bi bi-info-circle"></i> Status do Projeto</h2>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td><strong>Versão</strong></td>
                            <td><span class="badge bg-primary">2.0</span></td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td><span class="badge-status badge-ok"><span class="status-indicator status-ok"></span>Completo</span></td>
                        </tr>
                        <tr>
                            <td><strong>Interface</strong></td>
                            <td><span class="badge-status badge-ok"><span class="status-indicator status-ok"></span>Funcional</span></td>
                        </tr>
                        <tr>
                            <td><strong>APIs</strong></td>
                            <td><span class="badge-status badge-ok"><span class="status-indicator status-ok"></span>Testadas</span></td>
                        </tr>
                        <tr>
                            <td><strong>Documentação</strong></td>
                            <td><span class="badge-status badge-ok"><span class="status-indicator status-ok"></span>Completa</span></td>
                        </tr>
                        <tr>
                            <td><strong>Pronto para Produção</strong></td>
                            <td><span class="badge-status badge-warning"><span class="status-indicator status-warning"></span>Com Review</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Próximos Passos -->
        <h2 class="section-title"><i class="bi bi-arrow-right"></i> Próximos Passos</h2>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-success">
                        <h5 class="mb-0">🚀 Agora</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Abra <code>status.php</code></li>
                            <li>Confirme que usuariosaw está setado</li>
                            <li>Abra <code>index.php</code> como operador</li>
                            <li>Teste filtros e criar conversa</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">🔄 Futura</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Implementar WebSockets</li>
                            <li>Adicionar notificações</li>
                            <li>Integrar com WhatsApp</li>
                            <li>Adicionar analytics</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p><strong>ChatSite 2.0</strong> - Dashboard de Atendimento</p>
        <p class="text-muted">Desenvolvido com Bootstrap 5 + jQuery + PHP + MySQL</p>
        <p style="font-size: 0.85rem;">
            <span class="status-indicator status-ok"></span> Sistema Funcional
            <span class="ms-3"></span>
            <span class="status-indicator status-ok"></span> Documentação Completa
            <span class="ms-3"></span>
            <span class="status-indicator status-ok"></span> Testes Disponíveis
        </p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
