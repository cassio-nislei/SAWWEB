<?php
require_once('includes/padrao.inc.php');

echo "🔄 Iniciando atualização de banco de dados...\n";

$sqls = [
    "ALTER TABLE tbchatoperadores ADD COLUMN IF NOT EXISTS id_destinatario INT NULL COMMENT 'ID do operador destinatário'",
    "ALTER TABLE tbchatoperadores ADD COLUMN IF NOT EXISTS eh_privada TINYINT(1) DEFAULT 0 COMMENT '1 = privada, 0 = pública'",
    "ALTER TABLE tbchatoperadores ADD COLUMN IF NOT EXISTS visualizado TINYINT(1) DEFAULT 0 COMMENT '1 = lida, 0 = não lida'",
    "ALTER TABLE tbchatoperadores ADD COLUMN IF NOT EXISTS data_leitura DATETIME NULL COMMENT 'Data da leitura'",
];

$erros = 0;
foreach($sqls as $sql) {
    if(!@mysqli_query($GLOBALS['conexao'], $sql)) {
        echo "⚠️  " . mysqli_error($GLOBALS['conexao']) . "\n";
        $erros++;
    } else {
        echo "✅ Coluna adicionada successfully\n";
    }
}

if($erros === 0) {
    echo "\n✅ Banco de dados atualizado com sucesso!\n";
} else {
    echo "\n⚠️  Some columns may already exist (que é ok).\n";
}
?>
