<?php
require_once('includes/conexao.php');
$result = mysqli_query($conexao, 'SHOW CREATE TABLE tbanexos');
$row = mysqli_fetch_assoc($result);
echo $row['Create Table'];
?>
