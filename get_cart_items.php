<?php
include 'db_connect.php';

// 從購物車表中取得資料
$result = $conn->query("SELECT * FROM cart");
$cartItems = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
}

// 回傳購物車內容
echo json_encode(['cart' => $cartItems]);

$conn->close();
?>