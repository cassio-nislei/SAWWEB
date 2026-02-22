<?php
echo "POST de teste funcionando!\n";
echo "Método: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Path: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . "\n";
?>
