<?php
require_once('includes/conexao.php');

echo "=== TBANEXOS ===" . PHP_EOL;
$r = mysqli_query($conexao, 'DESCRIBE tbanexos');
if (!$r) { echo "ERRO: " . mysqli_error($conexao); exit; }
while($c = mysqli_fetch_assoc($r)) {
    echo $c['Field'] . ' | ' . $c['Type'] . ' | ' . $c['Null'] . ' | ' . $c['Key'] . ' | ' . $c['Extra'] . PHP_EOL;
}

echo PHP_EOL . "=== TBMSGATENDIMENTO ===" . PHP_EOL;
$r3 = mysqli_query($conexao, 'DESCRIBE tbmsgatendimento');
if (!$r3) { echo "ERRO: " . mysqli_error($conexao); exit; }
while($c = mysqli_fetch_assoc($r3)) {
    echo $c['Field'] . ' | ' . $c['Type'] . ' | ' . $c['Null'] . ' | ' . $c['Key'] . ' | ' . $c['Extra'] . PHP_EOL;
}

echo PHP_EOL . "--- Ultimos 5 registros tbanexos ---" . PHP_EOL;
$r2 = mysqli_query($conexao, "SELECT id, seq, numero, nome_arquivo, tipo_arquivo, LENGTH(arquivo) as arq_len, LENGTH(base64) as b64_len FROM tbanexos ORDER BY id DESC LIMIT 5");
if ($r2 && mysqli_num_rows($r2) > 0) {
    while($row = mysqli_fetch_assoc($r2)) {
        echo "id={$row['id']} seq={$row['seq']} num={$row['numero']} tipo={$row['tipo_arquivo']} arq_len={$row['arq_len']} b64_len={$row['b64_len']} nome={$row['nome_arquivo']}" . PHP_EOL;
    }
}

echo PHP_EOL . "--- Ultimos 5 registros tbmsgatendimento com anexo ---" . PHP_EOL;
$r4 = mysqli_query($conexao, "SELECT id, seq, numero, id_anexo, situacao, dt_msg, hr_msg, LEFT(msg, 50) as msg_preview FROM tbmsgatendimento WHERE id_anexo IS NOT NULL AND id_anexo > 0 ORDER BY seq DESC LIMIT 5");
if (!$r4) {
    echo "ERRO: " . mysqli_error($conexao) . PHP_EOL;
    // Try without id_anexo column
    echo "Tentando sem id_anexo..." . PHP_EOL;
    $r4 = mysqli_query($conexao, "SELECT id, seq, numero, situacao, dt_msg, hr_msg, LEFT(msg, 50) as msg_preview FROM tbmsgatendimento ORDER BY seq DESC LIMIT 5");
    if ($r4) {
        while($row = mysqli_fetch_assoc($r4)) {
            echo "id={$row['id']} seq={$row['seq']} sit={$row['situacao']} dt={$row['dt_msg']} msg={$row['msg_preview']}" . PHP_EOL;
        }
    }
} else {
    while($row = mysqli_fetch_assoc($r4)) {
        echo "id={$row['id']} seq={$row['seq']} id_anexo={$row['id_anexo']} sit={$row['situacao']} dt={$row['dt_msg']} msg={$row['msg_preview']}" . PHP_EOL;
    }
}

// Test: simular INSERT de áudio para ver se dá erro
echo PHP_EOL . "--- TESTE INSERT AUDIO ---" . PHP_EOL;
$testSeq = 99999;
$testNumero = 'TESTE_DEL';
$testBase64Clean = base64_encode('test audio data');
$testBase64Orig = 'data:audio/mpeg;base64,' . $testBase64Clean;
$testNome = 'audio_teste.mp3';

$sqlTest = "INSERT INTO tbanexos(seq,numero,arquivo,base64,nome_arquivo,nome_original,tipo_arquivo,canal,enviado)
            VALUES ('" . $testSeq . "','" . $testNumero . "','" . mysqli_real_escape_string($conexao, $testBase64Clean) . "','" . mysqli_real_escape_string($conexao, $testBase64Orig) . "','" . $testNome . "','" . $testNome . "','PTT','1',1)";

$testResult = mysqli_query($conexao, $sqlTest);
if (!$testResult) {
    echo "ERRO INSERT: " . mysqli_error($conexao) . PHP_EOL;
} else {
    $testId = mysqli_insert_id($conexao);
    echo "INSERT OK! ID=" . $testId . PHP_EOL;
    // Limpar o teste
    mysqli_query($conexao, "DELETE FROM tbanexos WHERE numero='TESTE_DEL' AND seq=99999");
    echo "Teste limpo." . PHP_EOL;
}

mysqli_close($conexao);
