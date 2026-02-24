<?php
echo "<h2>Teste de Extensões</h2>";
echo "<p>php.ini: " . php_ini_loaded_file() . "</p>";
echo "<p>ext_dir: " . ini_get('extension_dir') . "</p>";

$extensoes = array('gd', 'mbstring', 'curl', 'fileinfo', 'exif', 'sockets', 'mysqli', 'pdo_mysql');

echo "<h3>Status das Extensões:</h3>";
echo "<ul>";
foreach ($extensoes as $ext) {
    $status = extension_loaded($ext) ? "✅ CARREGADA" : "❌ NÃO CARREGADA";
    echo "<li><strong>$ext</strong>: $status</li>";
}
echo "</ul>";

echo "<h3>Funções GD:</h3>";
echo "<ul>";
$funcoes_gd = array('imagecreatefromstring', 'getimagesizefromstring', 'imagecreate');
foreach ($funcoes_gd as $func) {
    $status = function_exists($func) ? "✅ EXISTE" : "❌ NÃO EXISTE";
    echo "<li><strong>$func</strong>: $status</li>";
}
echo "</ul>";

// Teste com base64
echo "<h3>Teste de Validação de Imagem Base64:</h3>";
$test_base64 = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==";
try {
    $ImgRaw = explode("base64,", $test_base64);
    $ImgRaw = $ImgRaw[1];
    $ImgDecode = base64_decode($ImgRaw);
    if (function_exists('imagecreatefromstring')) {
        $ObjImg = imagecreatefromstring($ImgDecode);
        echo "<p>✅ imagecreatefromstring funcionou!</p>";
    } else {
        echo "<p>❌ imagecreatefromstring não existe!</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Erro: " . $e->getMessage() . "</p>";
}
?>
