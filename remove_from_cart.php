<?php
include 'db_connect.php';

$input_data = json_decode(file_get_contents('php://input'), true);

if ($input_data) {
    $productId = $input_data['productId'];
    $deleteSql = "DELETE FROM cart WHERE product_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // 商品成功刪除
        $cartSql = "SELECT product_id, product_name, product_price, quantity FROM cart";
        $cartResult = $conn->query($cartSql);

        $updatedCartContent = array();
        if ($cartResult->num_rows > 0) {
            while ($row = $cartResult->fetch_assoc()) {
                $updatedCartContent[] = $row;
            }
        }

        // 回傳更新後的購物車內容
        echo json_encode(["message" => "刪除成功", "cart" => $updatedCartContent]);
    } else {
        echo json_encode(["message" => "無法刪除該商品"]);
    }
} else {
    echo json_encode(["message" => "無效的請求"]);
}

$conn->close();
?>
