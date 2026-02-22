-- SQL Migration para adicionar coluna de foto do usuário
-- Executar este script diretamente no banco de dados ou via admin

ALTER TABLE tbusuario ADD COLUMN foto LONGTEXT COLLATE utf8mb4_unicode_ci AFTER nome_chat;
