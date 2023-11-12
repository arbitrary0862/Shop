前台功能：

前端頁面應該列出所有商品供使用者選擇，每個商品需要有加入購物車的按鈕，且可挑選商品數量。
每次按下加入購物車按鈕時，將商品加入購物車清單中，該購物清單需顯示在前台，且要可以移除商品。

訂單處理：
使用者填寫姓名、電話、地址等訂單資訊後，應能將購物車內容與訂單資訊一同送至後端處理。
在後端應該有一個處理訂單的功能，串接綠界測試金流，透過使用者提供的資訊進行付款。
付款狀態回傳：

在付款處理完畢後，接收並處理綠界測試金流回傳的付款狀態。根據回傳的狀態更新訂單的付款狀態，以便使用者能夠查看。

後台功能：
商品管理：
後台應該有新增、編輯和刪除商品的功能，可以透過後台介面對商品資訊進行管理，這些操作也需要對應到資料庫的修改。
訂單查看：
可以透過後台查看所有訂單，並能夠對訂單狀態進行管理，已付款等狀態。

資料庫結構（MySQL）：
商品表 products：

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    price DECIMAL(10, 2),
    description TEXT
);
訂單表 orders：

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(50),
    user_name VARCHAR(50),
    user_phone VARCHAR(20),
    user_address VARCHAR(100),
    payment_status VARCHAR(20)
);

====================================
<?php
// 此處應該處理與綠界支付系統的通信
// 獲取訂單資訊
$total_price = $_POST['total_price'];
$order_id = $_POST['order_id'];
// 其他訂單資訊

// 在這裡將訂單資訊發送到綠界支付系統
// 假設以下是綠界支付系統的模擬程式碼

// 模擬付款成功
$payment_status = '付款成功';

// 設置付款狀態，這部分應該根據綠界回傳的付款狀態來設置
// 這只是一個示例，實際情況中，您需要根據綠界回傳的資訊來設置付款狀態
if ($payment_status === '付款成功') {
    // 付款成功，更新訂單狀態為已付款
    include 'db_connect.php';

    $update_query = "UPDATE orders SET payment_status = '已付款' WHERE order_id = $order_id";

    if ($conn->query($update_query) === TRUE) {
        echo "訂單付款成功";
    } else {
        echo "Error: " . $update_query . "<br>" . $conn->error;
    }
} else {
    // 付款失敗，可以處理錯誤情況
    echo "付款失敗";
}
?>
=========================
