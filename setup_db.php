<?php
// Conexão direta ao banco
$conexao = mysqli_connect("104.234.173.105", "root", "Ncm@647534", "saw_quality");

if (!$conexao) {
    die("❌ Erro na conexão: " . mysqli_connect_error());
}

echo "✅ Conectado ao banco de dados\n\n";

$sqls = [
    "ALTER TABLE tbchatoperadores ADD COLUMN id_destinatario INT NULL COMMENT 'ID do operador destinatário'",
    "ALTER TABLE tbchatoperadores ADD COLUMN eh_privada TINYINT(1) DEFAULT 0 COMMENT '1 = privada, 0 = pública'",
    "ALTER TABLE tbchatoperadores ADD COLUMN visualizado TINYINT(1) DEFAULT 0 COMMENT '1 = lida, 0 = não lida'",
    "ALTER TABLE tbchatoperadores ADD COLUMN data_leitura DATETIME NULL COMMENT 'Data da leitura'",
];

$sucesso = 0;
$erros = 0;

foreach($sqls as $sql) {
    if(mysqli_query($conexao, $sql)) {
        echo "✅ Executado: $sql\n";
        $sucesso++;
    } else {
        $erro = mysqli_error($conexao);
        // Verificar se é aviso de coluna já existente
        if(strpos($erro, 'Duplicate column') !== false) {
            echo "⚠️ Coluna já existe: $sql\n";
            $sucesso++;
        } else {
            echo "❌ Erro: $erro\n";
            $erros++;
        }
    }
}

echo "\n========================================\n";
echo "✅ Sucesso: $sucesso\n";
echo "❌ Erros: $erros\n";
echo "========================================\n";

// Verificar estrutura final
echo "\n📋 Estrutura atual da tabela:\n";
$result = mysqli_query($conexao, "DESCRIBE tbchatoperadores");
while($row = mysqli_fetch_assoc($result)) {
    echo "- {$row['Field']} ({$row['Type']})\n";
}

mysqli_close($conexao);
?>
