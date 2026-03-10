<?php
// Router for PHP built-in server
// This file handles routing for the SAWWeb application

$requested_file = __DIR__ . $_SERVER["REQUEST_URI"];
$requested_file = urldecode($requested_file);

// If the file exists and is not a directory, serve it
if (file_exists($requested_file) && !is_dir($requested_file)) {
    return false;
}

// Otherwise, route to index.php
require_once __DIR__ . "/index.php";
