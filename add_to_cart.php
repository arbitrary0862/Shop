<?php
include 'db_connect.php';

$input_data = json_decode(file_get_contents('php://input'), true);

if ($input_data) {
    $productId = $input_data['productId'];
    $productName = $input_data['productName'];
    $productPrice = $input_data['productPrice'];
    $quantity = $input_data['quantity'];

    // 檢查購物車中是否已存在該商品
    $sql = "SELECT * FROM cart WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 如果商品已存在，更新購買數量
        $row = $result->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        $updateSql = "UPDATE cart SET quantity = ? WHERE product_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $newQuantity, $productId);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // 如果商品不存在，新增至購物車
        $insertSql = "INSERT INTO cart (product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("issi", $productId, $productName, $productPrice, $quantity);
        $insertStmt->execute();
        $insertStmt->close();
    }

    // 取得最新的購物車內容
    $cartSql = "SELECT product_id, product_name, product_price, quantity FROM cart";
    $cartResult = $conn->query($cartSql);

    $updatedCartContent = array();
    if ($cartResult->num_rows > 0) {
        while ($row = $cartResult->fetch_assoc()) {
            $updatedCartContent[] = $row;
        }
    }

    // 回傳更新後的購物車內容
    $response = array('cart' => $updatedCartContent);
    echo json_encode($response);
} else {
    echo "無效的請求";
}
$conn->close();
?>