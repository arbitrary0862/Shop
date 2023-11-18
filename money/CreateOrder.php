<?php
// 綠界支付API資訊
$gateway_url = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5'; // 依據實際情況替換
$merchant_id = '3002607'; // 商店編號
$hash_key = 'pwFHCqoQZGmho4w6'; // Hash Key
$hash_iv = 'EkRm7iFT261dpevs'; // Hash IV

// 訂單資訊
$order_params = array(
    'MerchantID' => $merchant_id,
    'MerchantTradeNo' => 'YourOrderNumber', // 訂單編號
    'MerchantTradeDate' => date("Y/m/d H:i:s"), //交易時間
    'PaymentType' => 'aio', // 交易類型
    'TotalAmount' => 100, // 訂單金額
    'TradeDesc' => 'product', // 商品描述
    'ItemName' => 'product', // 商品名稱
    // 'ReturnURL' => 'http://www.ecpay.com.tw/receive.php', //回傳網址
    'ChoosePayment' => 'Credit',
    'EncryptType' => 1 //加密類型
);
// var_dump($order_params); 

ksort($order_params); //A到Z的順序
// 產生 CheckValue
$check_value = 'HashKey=' . $hash_key;
foreach ($order_params as $key => $value) {
    $check_value .= '&' . $key . '=' . $value;
}
$check_value .= '&HashIV=' . $hash_iv;
$check_value = urlencode($check_value); //URL encode
$check_value = strtolower($check_value); //轉小寫
$check_value = urldecode($check_value); // 還原特殊字元
// var_dump($check_value);

// hash sha256加密後轉大寫寫回變數 $order_params
$order_params['CheckValue'] = strtoupper(hash('sha256', $check_value));
// var_dump($order_params);

// 發送訂單請求
$response = httpPost($gateway_url, $order_params);

// 處理回應
if ($response) {
    $result = json_decode($response, true);
    print_r($result);
} else {
    echo 'Failed to connect to the payment gateway.';
}

// 送出 HTTP POST 請求
function httpPost($url, $params) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    // 處理回應
    $result = json_decode($response, true);
    if ($result && isset($result['RtnCode']) && $result['RtnCode'] === '1') {
        // 支付成功
    } else {
        // 處理錯誤
    }

    return $response;
}

?>
