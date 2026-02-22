<?php
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db   = 'saw_quality';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM tbmsgatendimento WHERE numero = '5511999999997'");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error: " . $mysqli->error;
}

$mysqli->close();
?>
