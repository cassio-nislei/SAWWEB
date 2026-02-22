<?php
$val1 = 2;
$val2 = 20251119215251;

echo "Valor 1: $val1\n";
echo "  is_int: " . (is_int($val1) ? 'SIM' : 'NÃO') . "\n";
echo "  INT range OK? " . ($val1 >= -2147483648 && $val1 <= 2147483647 ? 'SIM' : 'NÃO') . "\n";
echo "  Tipo: " . gettype($val1) . "\n\n";

echo "Valor 2: $val2\n";
echo "  is_int: " . (is_int($val2) ? 'SIM' : 'NÃO') . "\n";
echo "  INT range OK? " . ($val2 >= -2147483648 && $val2 <= 2147483647 ? 'SIM' : 'NÃO') . "\n";
echo "  Tipo: " . gettype($val2) . "\n";
?>
