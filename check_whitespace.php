<?php
function checkLeadingWhitespace($filename) {
    $content = file_get_contents($filename);
    $firstTags = substr($content, 0, 5);
    echo "File: $filename\n";
    echo "First 5 bytes (hex): " . bin2hex($firstTags) . "\n";
    if (strpos($content, '<?php') !== 0) {
        echo "WARNING: File does not start immediately with <?php\n";
    } else {
        echo "File starts correctly with <?php\n";
    }
}

checkLeadingWhitespace('atendimento/listaConversas.php');
checkLeadingWhitespace('includes/padrao.inc.php');
checkLeadingWhitespace('conversas.php');
?>
