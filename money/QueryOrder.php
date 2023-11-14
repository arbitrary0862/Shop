<?php
// 綠界支付API資訊
$gateway_url = 'https://payment.ecpay.com.tw/Cashier/QueryTradeInfo'; // 依據實際情況替換
$merchant_id = '2000132'; // 替換為您的商店編號
$hash_key = 'ejCk326UnaZWKisg'; // 替換為您的 Hash Key
$hash_iv = 'q9jcZX8Ib9LM8wYk'; // 替換為您的 Hash IV

// 查詢訂單資訊
$query_params = array(
    'MerchantID' => $merchant_id,
    'RespondType' => 'JSON',
    'TimeStamp' => time(),
    'Version' => '1.2',
    'MerchantOrderNo' => 'YourOrderNumber', // 替換為您的訂單編號
);

ksort($query_params);

// 產生 CheckValue
$check_value = 'HashKey=' . $hash_key;
foreach ($query_params as $key => $value) {
    $check_value .= '&' . $key . '=' . $value;
}
$check_value .= '&HashIV=' . $hash_iv;

$query_params['CheckValue'] = strtoupper(hash('sha256', $check_value));

// 發送查詢訂單請求
$response = httpPost($gateway_url, $query_params);

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
