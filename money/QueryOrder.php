<?php
include '../db_connect.php';
// 綠界支付API資訊
$gateway_url = 'https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V5'; // 依據實際情況替換
$merchant_id = '3002607'; // 替換為您的商店編號
$hash_key = 'pwFHCqoQZGmho4w6'; // 替換為您的 Hash Key
$hash_iv = 'EkRm7iFT261dpevs'; // 替換為您的 Hash IV
echo '<a href="../back/order.php">返回上一頁</a><br>';
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $query_params = array(
        'MerchantID' => $merchant_id,
        'TimeStamp' => time(),
        'MerchantTradeNo' => $order_id, // 替換訂單編號
    );
    
    ksort($query_params); //A到Z的順序
    // 產生 CheckValue
    $check_value = 'HashKey=' . $hash_key;
    foreach ($query_params as $key => $value) {
        $check_value .= '&' . $key . '=' . $value;
    }
    $check_value .= '&HashIV=' . $hash_iv;
    $check_value = urlencode($check_value); //URL encode
    
    // 還原特殊字元
    // $check_value = urldecode($check_value);
    $check_value = str_replace('%2D', '-', $check_value);
    $check_value = str_replace('%5F', '_', $check_value);
    $check_value = str_replace('%2E', '.', $check_value);
    $check_value = str_replace('%21', '!', $check_value);
    $check_value = str_replace('%2A', '*', $check_value);
    $check_value = str_replace('%28', '(', $check_value);
    $check_value = str_replace('%29', ')', $check_value);
    $check_value = str_replace('%20', '+', $check_value);
    
    $check_value = strtolower($check_value); //轉小寫
    // var_dump($check_value);
    
    
    // hash sha256加密後轉大寫寫回變數 $query_params
    $query_params['CheckMacValue'] = strtoupper(hash('sha256', $check_value));
    // var_dump($query_params);
    // 發送查詢訂單請求
    $response = httpPost($gateway_url, $query_params);
    // var_dump(json_decode($response, true));
    // 處理回應
    if ($response) {
        parse_str($response,$result);
        // print_r($result);
        $tradeStatus = (int)$result['TradeStatus'];
        if ($tradeStatus == 1) {
            // 交易訂單成立已付款，更新相應訂單的付款狀態為已付款
            $tradeStatus .= '已付款';
            $updatePaymentStatusQuery = "UPDATE orders SET payment_status = ? WHERE order_num = ?";
            $updateStmt = $conn->prepare($updatePaymentStatusQuery);
            $updateStmt->bind_param("ss", $tradeStatus, $order_id);
            $updateStmt->execute();
            $updateStmt->close();
            
            // 輸出查詢結果
            // print_r($result);
            echo '<br> 訂單編號：' .  $result['MerchantTradeNo'] . '，已付款，TradeStatus：' . $tradeStatus;
        } else {
            $tradeStatus .= '未付款';
            $updatePaymentStatusQuery = "UPDATE orders SET payment_status = ? WHERE order_num = ?";
            $updateStmt = $conn->prepare($updatePaymentStatusQuery);
            $updateStmt->bind_param("ss", $tradeStatus, $order_id);
            $updateStmt->execute();
            $updateStmt->close();
            // print_r($result);
            echo '<br> 訂單編號：' . $result['MerchantTradeNo'] . '，未付款，TradeStatus：' . $tradeStatus;
        }
    } else {
        echo 'Failed to connect to the payment gateway.';
    }

} else {
    echo '未收到訂單編號';
    exit;
}

// 送出 HTTP POST 請求
function httpPost($url, $params) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 將請求的結果以字串返回，而不是直接輸出
    curl_setopt($ch, CURLOPT_POST, true); // 啟用POST請求
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));  // HTTP標頭
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params)); // 設定POST請求的數據。
    
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}
?>
