<?php
// 綠界支付API資訊
$gateway_url = 'https://payment.greenworld.com.tw/Pay4G/walpayadv'; // 依據實際情況替換
$merchant_id = 'YourMerchantID'; // 替換為您的商店編號
$hash_key = 'YourHashKey'; // 替換為您的 Hash Key
$hash_iv = 'YourHashIV'; // 替換為您的 Hash IV

// 訂單資訊
$order_params = array(
    'MerchantID' => $merchant_id,
    'RespondType' => 'JSON',
    'TimeStamp' => time(),
    'Version' => '1.2',
    'MerchantOrderNo' => 'YourOrderNumber', // 替換為您的訂單編號
    'Amt' => 100, // 替換為訂單金額
    'ItemDesc' => 'YourItemDescription', // 替換為商品描述
    'Email' => 'customer@example.com', // 替換為客戶Email
    'LoginType' => 0,
);

ksort($order_params);

// 產生 CheckValue
$check_value = 'HashKey=' . $hash_key;
foreach ($order_params as $key => $value) {
    $check_value .= '&' . $key . '=' . $value;
}
$check_value .= '&HashIV=' . $hash_iv;

$order_params['CheckValue'] = strtoupper(hash('sha256', $check_value));

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
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>
