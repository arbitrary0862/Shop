<?php
// 綠界串接超取API資訊
$gateway_url = 'https://logistics-stage.ecpay.com.tw/Express/map'; // 依據實際情況替換
$merchant_id = '2000132'; // 商店編號
$hash_key = '5294y06JbISpM5x9'; // Hash Key
$hash_iv = 'v77hoKGq4kWxNNIS'; // Hash IV

// 訂單資訊
$order_params = array(
    'MerchantID' => $merchant_id,
    'LogisticsType' => 'CVS', // 物流類型
    'PaymentType' => 'aio', // 交易類型
    'LogisticsSubType' => 'FAMI', // 物流子類型
    'IsCollection' => 'N', // 不代收貨款
    'ServerReplyURL' => 'http://127.0.0.1/Shop/index.php' // 回傳網址
);
// 嵌入HTML表單
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ship Form</title>
</head>
<body>
    <form id="ShipForm" action="<?php echo $gateway_url; ?>" method="post">
        <?php
        foreach ($order_params as $key => $value) {
            echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
        }
        ?>
        <input type="submit" value="ChooseShip with ECPay">
    </form>

    <script>
        // 自動提交表單
        document.getElementById('ShipForm').submit();
    </script>
</body>
</html>
