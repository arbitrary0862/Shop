<?php
// 綠界支付API資訊
$gateway_url = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5'; // 依據實際情況替換
$merchant_id = '3002607'; // 商店編號
$hash_key = 'pwFHCqoQZGmho4w6'; // Hash Key
$hash_iv = 'EkRm7iFT261dpevs'; // Hash IV

// 訂單資訊
$order_params = array(
    'MerchantID' => $merchant_id,
    'MerchantTradeNo' => 'Test'.time(), // 訂單編號
    'MerchantTradeDate' => date("Y/m/d H:i:s"), //交易時間
    'PaymentType' => 'aio', // 交易類型
    'TotalAmount' => 100, // 訂單金額
    'TradeDesc' => 'product', // 商品描述
    'ItemName' => 'product', // 商品名稱
    'ReturnURL' => 'http://127.0.0.1/Shop/index.php', //回傳網址
    'ClientBackURL' => 'http://127.0.0.1/Shop/index.php', //返回網址
    'ChoosePayment' => 'Credit',
    'EncryptType' => 1 //加密類型
);

ksort($order_params); //A到Z的順序

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

$check_value = strtolower($check_value); //轉小寫

// hash sha256加密後轉大寫寫回變數 $order_params
$order_params['CheckMacValue'] = strtoupper(hash('sha256', $check_value));

// 嵌入HTML表單
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
</head>
<body>
    <form id="paymentForm" action="<?php echo $gateway_url; ?>" method="post">
        <?php
        foreach ($order_params as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
        ?>
        <input type="submit" value="Pay with ECPay">
    </form>

    <script>
        // 自動提交表單
        document.getElementById('paymentForm').submit();
    </script>
</body>
</html>
