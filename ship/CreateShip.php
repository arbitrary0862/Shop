<?php
// 綠界物流API資訊
$apiUrl = 'https://logistics-stage.ecpay.com.tw/Express/Create';
$merchant_id = '2000132'; // 商店編號
$hash_key = '5294y06JbISpM5x9'; // Hash Key
$hash_iv = 'v77hoKGq4kWxNNIS'; // Hash IV

$ship = '宅配';
// 特店Request參數
// 物流單資訊
// $logisticsType = 'HOME'; // 宅配
// $logisticsSubType = 'POST'; // 宅配
// $logisticsType  = 'CVS'; // 超取
// $logisticsSubType = 'FAMI'; // 全家超取
$goodsAmount = 100; // 商品金額

$ship_params = array(
    'MerchantID' => $merchant_id,
    'MerchantTradeNo' => 'Test' . time(), // 交易編號
    'MerchantTradeDate' => date('Y/m/d H:i:s'), // 交易時間
    'GoodsAmount' => $goodsAmount, // 商品金額
    'ServerReplyURL' => '127.0.0.1/back/order.php',    // Server端回覆網址
);

if($ship == '宅配'){
    $ship_params['LogisticsType'] = 'HOME';
    $ship_params['LogisticsSubType'] = 'POST';
    $ship_params['GoodsWeight'] = 2;                                // 商品重量
    $ship_params['SenderName'] = '測試寄件人';                       // 寄件人姓名
    $ship_params['SenderPhone'] = '0912345678';                     // 寄件人手機
    $ship_params['SenderZipCode'] = '100';                          // 寄件人郵遞區號
    $ship_params['SenderAddress'] = '測試寄件地址';                  // 寄件人地址
    $ship_params['ReceiverName'] = '測試收件人';                     // 收件人姓名
    $ship_params['ReceiverPhone'] = '0912345678';                   // 收件人手機
    $ship_params['ReceiverZipCode'] = '403';                        // 收件人郵遞區號
    $ship_params['ReceiverAddress'] = '台中市西區一二路34號';        // 收件人地址

}elseif($ship == '超取'){
    $ship_params['LogisticsType'] = 'CVS';
    $ship_params['LogisticsSubType'] = 'FAMI';
    $ship_params['SenderName'] = '測試寄件人';                       // 寄件人姓名
    $ship_params['ReceiverName'] = '測試收件人';                     // 收件人姓名
    $ship_params['ReceiverCellPhone'] = '0912345678';               // 收件人手機
    $ship_params['ReceiverStoreID'] = '';                           // 收件人門市代號 
}

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
    <form id="paymentForm" action="<?php echo $apiUrl; ?>" method="post">
        <?php
        foreach ($ship_params as $key => $value) {
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

?>