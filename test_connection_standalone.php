<?php
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db   = 'saw_qualiy';

echo "Testing connection to $host with user $user...\n";

try {
    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error . " (Error Code: " . $mysqli->connect_errno . ")\n");
    }

    echo "Connected successfully to database '$db'!\n";
    echo "Server info: " . $mysqli->server_info . "\n";
    $mysqli->close();
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
