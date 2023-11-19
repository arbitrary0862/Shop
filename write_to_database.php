<?php
include 'db_connect.php';
// 綠界支付API資訊
$gateway_url = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5'; // 依據實際情況替換
$merchant_id = '3002607'; // 商店編號
$hash_key = 'pwFHCqoQZGmho4w6'; // Hash Key
$hash_iv = 'EkRm7iFT261dpevs'; // Hash IV

// 確保有 POST 資料
$input_data = json_decode(file_get_contents('php://input'), true);
// var_dump($input_data);
if ($input_data) {
    if (isset($input_data['name']) && isset($input_data['phone']) && isset($input_data['address'])) {
        $name = $input_data['name'];
        $phone = $input_data['phone'];
        $address = $input_data['address'];
        $productId = $input_data['productId']; // 產品ID
        $productquantity = $input_data['productquantity']; // 產品數量
        $order_price = (int) $input_data['order_price']; // 訂單金額
        $order_num = 'Test' . time();
        $paymentStatus = "待付款";

        // 寫入訂單到資料庫中
        $sql = "INSERT INTO orders (order_num, product_id, product_quantity, order_price, user_name, user_phone, user_address, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssissss", $order_num, $productId, $productquantity, $order_price, $name, $phone, $address, $paymentStatus);
            $stmt->execute();
            $orderID = $order_num;

            // 檢查是否成功寫入
            if ($stmt->affected_rows > 0) {
                // 串接綠界
                $order_params = array(
                    'MerchantID' => $merchant_id,
                    'MerchantTradeNo' => $orderID, // 訂單編號
                    'MerchantTradeDate' => date("Y/m/d H:i:s"), //交易時間
                    'PaymentType' => 'aio', // 交易類型
                    'TotalAmount' => $order_price, // 訂單金額
                    'TradeDesc' => 'product', // 商品描述
                    'ItemName' => 'product', // 商品名稱
                    'ReturnURL' => 'http://127.0.0.1/Shop/index.php', // 回傳網址
                    'ChoosePayment' => 'Credit',
                    'EncryptType' => 1 // 加密類型
                );
                ksort($order_params); // A到Z的順序
                // 產生 CheckValue
                $check_value = 'HashKey=' . $hash_key;
                foreach ($order_params as $key => $value) {
                    $check_value .= '&' . $key . '=' . $value;
                }
                $check_value .= '&HashIV=' . $hash_iv;
                $check_value = urlencode($check_value); //URL encode

                $check_value = str_replace('%2D', '-', $check_value);
                $check_value = str_replace('%5F', '_', $check_value);
                $check_value = str_replace('%2E', '.', $check_value);
                $check_value = str_replace('%21', '!', $check_value);
                $check_value = str_replace('%2A', '*', $check_value);
                $check_value = str_replace('%28', '(', $check_value);
                $check_value = str_replace('%29', ')', $check_value);
                $check_value = str_replace('%20', '+', $check_value);

                // $check_value = urldecode($check_value); // 還原特殊字元
                $check_value = strtolower($check_value); //轉小寫
                // var_dump($check_value);

                // hash sha256加密後轉大寫寫回變數 $order_params
                $order_params['CheckMacValue'] = strtoupper(hash('sha256', $check_value));
                // var_dump($order_params);

                // 發送訂單請求
                $response = httpPost($gateway_url, $order_params);
                // var_dump($response);
                // 處理回應
                if ($response) {
                    // $result = json_decode($response, true);
                    // if ($result && isset($result['RtnCode']) && $result['RtnCode'] === '1') {
                    //     // 支付成功
                    //     header("Location: " . $result['PaymentURL']);
                    //     // exit;
                    // } else {
                    //     // 處理錯誤
                    //     echo '建立付款訂單失敗。錯誤：' . $result['RtnCode'];
                    //     var_dump($response);
                    // }
                } else {
                    echo '無法連接綠界';
                }
                // 返回訂單編號
                echo $orderID;
                // 串接綠界結束，清空購物車
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

// 送出 HTTP POST 請求
function httpPost($url, $params)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 將請求的結果以字串返回，而不是直接輸出
    curl_setopt($ch, CURLOPT_POST, true); // 啟用POST請求
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded')); // HTTP標頭
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); // 設定POST請求的數據。

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

$conn->close();
?>