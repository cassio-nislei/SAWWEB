<?php
$host = '104.234.173.105';
$user = 'root';
$pass = 'Ncm@647534';
$db   = 'saw_quality';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if column exists
$result = $mysqli->query("SHOW COLUMNS FROM tbmsgatendimento LIKE 'reacaorec'");
if ($result->num_rows == 0) {
    echo "Column reacaorec does not exist. Adding it...\n";
    if ($mysqli->query("ALTER TABLE tbmsgatendimento ADD COLUMN reacaorec VARCHAR(60) DEFAULT NULL")) {
        echo "Column reacaorec added successfully.\n";
    } else {
        echo "Error adding column: " . $mysqli->error . "\n";
    }
} else {
    echo "Column reacaorec already exists.\n";
}

$mysqli->close();
?>
