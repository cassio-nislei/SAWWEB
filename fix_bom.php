<?php
function checkAndRemoveBOM($filename) {
    $content = file_get_contents($filename);
    $bom = pack("CCC", 0xef, 0xbb, 0xbf);
    if (strncmp($content, $bom, 3) === 0) {
        echo "BOM found in $filename. Removing it...\n";
        $content = substr($content, 3);
        file_put_contents($filename, $content);
        echo "BOM removed from $filename.\n";
    } else {
        echo "No BOM found in $filename.\n";
    }
}

checkAndRemoveBOM('atendimento/listaConversas.php');
checkAndRemoveBOM('includes/padrao.inc.php');
?>
