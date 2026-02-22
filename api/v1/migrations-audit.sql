-- ============================================
-- Tabelas de Auditoria para API
-- Data: 19/11/2025
-- Compatível com MySQL 5.5+
-- ============================================

-- Tabela de auditoria de login
CREATE TABLE IF NOT EXISTS tb_audit_login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    dispositivo VARCHAR(255),
    ip VARCHAR(45),
    login_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_login_em (login_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de auditoria de download
CREATE TABLE IF NOT EXISTS tb_audit_download (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anexo_id INT NOT NULL,
    usuario_id INT,
    ip VARCHAR(45),
    download_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_anexo_id (anexo_id),
    INDEX idx_download_em (download_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de log de API
CREATE TABLE IF NOT EXISTS tb_api_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    endpoint VARCHAR(255),
    metodo VARCHAR(10),
    status_code INT,
    tempo_resposta_ms INT,
    ip VARCHAR(45),
    user_agent VARCHAR(500),
    requisicao_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_endpoint (endpoint),
    INDEX idx_requisicao_em (requisicao_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Dados de exemplo - Usuário Admin
-- ============================================

-- Inserir usuário de teste (login: admin, senha: 123456)
INSERT IGNORE INTO tbusuario (id, login, nome, email, senha, situacao)
VALUES (
    999,
    'admin',
    'Administrador API',
    'admin@api.local',
    '123456',
    'A'
);


