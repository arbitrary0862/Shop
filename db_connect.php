<?php
$host = "127.0.0.1";
$username = "admin";
$password = "admin";
$database = "shop";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("連線失敗: " . $conn->connect_error);
}
?>