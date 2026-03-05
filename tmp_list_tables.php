<?php
$conn = new mysqli('localhost', 'root', '', 'simatik');
$res = $conn->query('SHOW TABLES');
while($row = $res->fetch_row()) {
    echo $row[0] . PHP_EOL;
}
$conn->close();
