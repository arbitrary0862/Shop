<?php
// 綠界支付API資訊
$gateway_url = 'https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V5'; // 依據實際情況替換
$merchant_id = '3002607'; // 替換為您的商店編號
$hash_key = 'pwFHCqoQZGmho4w6'; // 替換為您的 Hash Key
$hash_iv = 'EkRm7iFT261dpevs'; // 替換為您的 Hash IV

// 查詢訂單資訊
$query_params = array(
    'MerchantID' => $merchant_id,
    'TimeStamp' => time(),
    'MerchantTradeNo' => 'Test1700335157', // 替換為您的訂單編號
);
// var_dump($query_params); 

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
$query_params['CheckValue'] = strtoupper(hash('sha256', $check_value));
// var_dump($query_params);
// 發送查詢訂單請求
$response = httpPost($gateway_url, $query_params);
// var_dump(json_decode($response, true));
// 處理回應
if ($response) {
    parse_str($response,$result);
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

    return $response;
}
?>
