<?php
include 'db_connect.php';

// 確保有 POST 資料
$input_data = json_decode(file_get_contents('php://input'), true);
// var_dump($input_data);
if ($input_data) {
    if (isset($input_data['name']) && isset($input_data['phone']) && isset($input_data['address'])) {
        $name = $input_data['name'];
        $phone = $input_data['phone'];
        $address = $input_data['address'];
        $productId = $input_data['productId']; // 產品ID
        $paymentStatus = "待付款";

        // 寫入訂單到資料庫中
        $sql = "INSERT INTO orders (product_id, user_name, user_phone, user_address, payment_status) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $productId, $name, $phone, $address, $paymentStatus);
            $stmt->execute();

            // 獲取最後一次插入操作生成的 ID
            $orderID = $conn->insert_id;

            // 檢查是否成功寫入
            if ($stmt->affected_rows > 0) {
                echo $orderID;
                // 清空購物車
                $sql = "TRUNCATE TABLE cart";
                $result = $conn->query($sql);

            } else {
                echo "無法寫入訂單到資料庫。";
            }

            $stmt->close();
        } else {
            echo "SQL語法錯誤";
        }
    } else {
        echo "請提供完整的訂購人資訊";
    }
} else {
    echo "沒有接收到有效的資料";
}

$conn->close();
?>