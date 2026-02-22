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
    <title>Status ChatSite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { padding: 2rem; background: #f5f5f5; }
        .status-box { background: white; padding: 1.5rem; margin-bottom: 1rem; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .status-ok { border-left: 4px solid #28a745; }
        .status-erro { border-left: 4px solid #dc3545; }
        .status-aviso { border-left: 4px solid #ffc107; }
        h2 { color: #2c3e50; margin: 2rem 0 1rem 0; }
        pre { background: #f5f5f5; padding: 1rem; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container-lg">
        <h1><i class="bi bi-graph-up"></i> Status ChatSite</h1>
        
        <!-- Autenticação -->
        <h2><i class="bi bi-shield-check"></i> Autenticação</h2>
        <div class="status-box <?php echo isset($_SESSION["usuariosaw"]) ? 'status-ok' : 'status-aviso'; ?>">
            <strong>usuariosaw:</strong> 
            <?php if (isset($_SESSION["usuariosaw"])): ?>
                <span class="badge bg-success">SETADO</span>
                <span><?php echo $_SESSION["usuariosaw"]["nome"] ?? 'N/A'; ?></span>
            <?php else: ?>
                <span class="badge bg-warning">NÃO SETADO</span>
                <em>Usuário não é operador</em>
            <?php endif; ?>
        </div>
        
        <div class="status-box <?php echo isset($_SESSION["chat"]) ? 'status-ok' : 'status-aviso'; ?>">
            <strong>chat:</strong> 
            <?php if (isset($_SESSION["chat"])): ?>
                <span class="badge bg-success">SETADO</span>
                <span>ID: <?php echo $_SESSION["chat"]["id_atendimento"] ?? 'N/A'; ?></span>
            <?php else: ?>
                <span class="badge bg-warning">NÃO SETADO</span>
                <em>Usuário não está em chat (cliente ou não autenticado)</em>
            <?php endif; ?>
        </div>

        <!-- Banco de Dados -->
        <h2><i class="bi bi-database"></i> Banco de Dados</h2>
        <?php
        $conn = new mysqli($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pass'], $GLOBALS['db']);
        if ($conn->connect_error):
        ?>
            <div class="status-box status-erro">
                <strong>Conexão:</strong> <span class="badge bg-danger">ERRO</span>
                <p><?php echo $conn->connect_error; ?></p>
            </div>
        <?php else: ?>
            <div class="status-box status-ok">
                <strong>Conexão:</strong> <span class="badge bg-success">OK</span>
                <p>Database: <?php echo $GLOBALS['db']; ?></p>
            </div>

            <!-- Tabelas -->
            <div class="status-box">
                <strong>Tabelas:</strong><br>
                <?php
                $tables = ['tbatendimento', 'tbmsgatendimento'];
                foreach ($tables as $table):
                    $result = $conn->query("SHOW TABLES LIKE '$table'");
                    $exists = $result->num_rows > 0;
                ?>
                    <div class="mt-2">
                        <span class="badge <?php echo $exists ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $table; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Dados -->
            <h2><i class="bi bi-list-ul"></i> Dados</h2>
            <?php
            $result = $conn->query("SELECT COUNT(*) as total FROM tbatendimento");
            $row = $result->fetch_assoc();
            ?>
            <div class="status-box">
                <strong>Total de Atendimentos:</strong> 
                <span class="badge bg-info"><?php echo $row['total']; ?></span>
            </div>

            <?php
            $result = $conn->query("
                SELECT situacao, COUNT(*) as total 
                FROM tbatendimento 
                GROUP BY situacao
                ORDER BY situacao
            ");
            ?>
            <div class="status-box">
                <strong>Atendimentos por Status:</strong>
                <table class="table table-sm mt-2">
                    <thead>
                        <tr><th>Situação</th><th>Qtd</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php 
                                $situacoes = ['A' => 'Ativo', 'T' => 'Aguardando', 'P' => 'Pendente', 'F' => 'Finalizado'];
                                echo $situacoes[$row['situacao']] ?? $row['situacao'];
                            ?></td>
                            <td><span class="badge bg-secondary"><?php echo $row['total']; ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Últimas Conversas -->
            <h2><i class="bi bi-chat-left"></i> Últimas Conversas</h2>
            <?php
            $result = $conn->query("
                SELECT id, numero, nome_cliente, situacao, dt_inicio 
                FROM tbatendimento 
                ORDER BY dt_inicio DESC 
                LIMIT 5
            ");
            ?>
            <div class="status-box">
                <table class="table table-sm">
                    <thead>
                        <tr><th>ID</th><th>Número</th><th>Cliente</th><th>Status</th><th>Data</th></tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><code><?php echo $row['numero']; ?></code></td>
                            <td><?php echo $row['nome_cliente']; ?></td>
                            <td>
                                <?php 
                                $situacoes = ['A' => 'Ativo', 'T' => 'Aguardando', 'P' => 'Pendente', 'F' => 'Finalizado'];
                                echo $situacoes[$row['situacao']] ?? $row['situacao'];
                                ?>
                            </td>
                            <td><?php echo date('d/m H:i', strtotime($row['dt_inicio'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; $conn->close(); ?>

        <!-- Testes de API -->
        <h2><i class="bi bi-gear"></i> Testes de API</h2>
        <div class="status-box">
            <p><strong>Para testar os endpoints de API:</strong></p>
            <ul>
                <li><a href="api_conversas.php" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-play-fill"></i> api_conversas.php
                </a></li>
            </ul>
        </div>

        <!-- Logs -->
        <h2><i class="bi bi-file-text"></i> Console do Browser</h2>
        <div class="status-box">
            <p>Abra o DevTools (F12) e vá para <strong>Console</strong> para ver mensagens de debug.</p>
            <p>Clique em <strong>Network</strong> para inspecionar requisições AJAX.</p>
        </div>

        <!-- Recomendações -->
        <h2><i class="bi bi-info-circle"></i> Checklist</h2>
        <div class="status-box status-aviso">
            <ul>
                <li>✅ Corrigidos nomes de colunas: <code>id</code> → <code>id_atendimento</code></li>
                <li>✅ Corrigido ID do botão: <code>btnFiltroAktivas</code> → <code>btnFiltroAtivas</code></li>
                <li>✅ Corrigido statusMap: <code>akia</code> → <code>ativo</code></li>
                <li>✅ Adicionado arquivo de debug.php</li>
                <li>⏳ Próximo: Verifique se os botões aparecem em conversas_sala.php</li>
                <li>⏳ Próximo: Use DevTools para verificar se api_conversas.php retorna dados</li>
            </ul>
        </div>

        <div class="mt-4 text-muted">
            <small>Última atualização: <?php echo date('d/m/Y H:i:s'); ?></small>
        </div>
    </div>
</body>
</html>
