<?php
include 'db_connect.php';

$input_data = json_decode(file_get_contents('php://input'), true);

if ($input_data) {
    echo $input_data;
    $productId = $input_data['productId'];
    $deleteSql = "DELETE FROM cart WHERE product_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["message" => "刪除成功"]);
    } else {
        echo json_encode(["message" => "無法刪除該商品"]);
    }
} else {
    echo json_encode(["message" => "無效的請求"]);
}
$conn->close();
?>