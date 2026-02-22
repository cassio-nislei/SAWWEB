-- Se a coluna 'acao' não existir, adiciona
ALTER TABLE `tbrespostasrapidas` ADD COLUMN `acao` INT DEFAULT 0 AFTER `resposta`;
