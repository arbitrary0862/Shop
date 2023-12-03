<?php
include '../db_connect.php';
// 綠界查詢物流API資訊
$gateway_url = 'https://logistics-stage.ecpay.com.tw/Helper/QueryLogisticsTradeInfo/V4'; // 依據實際情況替換
$merchant_id = '2000132'; // 替換為您的商店編號
$hash_key = '5294y06JbISpM5x9'; // 替換為您的 Hash Key
$hash_iv = 'v77hoKGq4kWxNNIS'; // 替換為您的 Hash IV
echo '<a href="../back/order.php">返回上一頁</a><br>';
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $ship_params = array(
        'MerchantID' => $merchant_id,
        'MerchantTradeNo' => $order_id, // 替換訂單編號
        // 'MerchantTradeNo' => 'Test1701589997', // 替換訂單編號
        'TimeStamp' => time(),
    );

    ksort($ship_params); //A到Z的順序

    // 產生 CheckValue
    $check_value = 'HashKey=' . $hash_key;
    foreach ($ship_params as $key => $value) {
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

    $check_value = strtolower($check_value); //轉小寫

    // MD5加密後轉大寫寫回變數 $ship_params
    $ship_params['CheckMacValue'] = strtoupper(md5($check_value));
    // var_dump($ship_params);
    // 發送查詢物流請求
    $response = httpPost($gateway_url, $ship_params);
    // var_dump(json_decode($response, true));
    // 處理回應
    if ($response) {
        parse_str($response, $result);
        // print_r($result);
        if(isset($result['LogisticsStatus'])){
            $LogisticsStatus = (int) $result['LogisticsStatus'];
            $MerchantTradeNo = $result['MerchantTradeNo'];
        }else{
            print_r($result);
            $LogisticsStatus = 0;
            $MerchantTradeNo = null;
        }
        if ($LogisticsStatus == 300) {
            // 交易訂單處理中，更新相應訂單物流狀態為訂單處理中
            $LogisticsStatus .= '訂單處理中';
            $updateShipStatusQuery = "UPDATE orders SET ship_status = ? WHERE order_num = ?";
            $updateStmt = $conn->prepare($updateShipStatusQuery);
            $updateStmt->bind_param("ss", $LogisticsStatus, $order_id);
            $updateStmt->execute();
            $updateStmt->close();

            // 輸出查詢結果
            // print_r($result);
            echo '<br> 物流編號：' . $MerchantTradeNo . '，已付款，LogisticsStatus：' . $LogisticsStatus;
        } else {
            $LogisticsStatus .= '異常';
            $updateShipStatusQuery = "UPDATE orders SET ship_status = ? WHERE order_num = ?";
            $updateStmt = $conn->prepare($updateShipStatusQuery);
            $updateStmt->bind_param("ss", $LogisticsStatus, $order_id);
            $updateStmt->execute();
            $updateStmt->close();
            // print_r($result);
            echo '<br> 物流編號：' . $MerchantTradeNo . '，異常，LogisticsStatus：' . $LogisticsStatus;
        }
    } else {
        echo 'Failed to connect to the 綠界物流.';
    }

} else {
    echo '未收到物流編號';
    exit;
}

// 送出 HTTP POST 請求
function httpPost($url, $params)
{
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