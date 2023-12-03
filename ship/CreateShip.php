<?php
include '../db_connect.php';
echo '<a href="../back/order.php">返回上一頁</a><br>';
// 綠界物流API資訊
$apiUrl = 'https://logistics-stage.ecpay.com.tw/Express/Create';
$merchant_id = '2000132'; // 商店編號
$hash_key = '5294y06JbISpM5x9'; // Hash Key
$hash_iv = 'v77hoKGq4kWxNNIS'; // Hash IV
if (isset($_GET['order_id'])) {
    $order_num = $_GET['order_id'];
    $result = $conn->query("SELECT * FROM orders Where order_num = '$order_num'");
    if ($result) {
        // 取得一筆資料
        $row = $result->fetch_assoc();
        $goodsAmount = $row['order_price'];         // 商品金額
        $deliveryMethod = $row['deliveryMethod'];   // 配送方式
        $user_name = $row['user_name'];             // 姓名
        $user_phone = $row['user_phone'];           // 電話
        $pos_code = $row['pos_code'];               // 郵遞區號
        $user_addressd = $row['user_address'];      // 地址
        $CVSStoreID = $row['CVSStoreID'];           // 門市ID
    }else{
        echo "查無此訂單";
    }

}
// 特店Request參數
// 物流單資訊
// $logisticsType = 'HOME'; // 宅配
// $logisticsSubType = 'POST'; // 宅配
// $logisticsType  = 'CVS'; // 超取
// $logisticsSubType = 'FAMI'; // 全家超取

$ship_params = array(
    'MerchantID' => $merchant_id,
    'MerchantTradeNo' => $order_num, // 交易編號
    'MerchantTradeDate' => date('Y/m/d H:i:s'), // 交易時間
    'GoodsAmount' => $goodsAmount, // 商品金額
    'ServerReplyURL' => '127.0.0.1/back/order.php',    // Server端回覆網址
);

if ($deliveryMethod == 'HOME') {
    $ship_params['LogisticsType'] = 'HOME';                        // 宅配 HOME 
    $ship_params['LogisticsSubType'] = 'POST';                     // 中華郵政 POST
    $ship_params['GoodsWeight'] = 2;                               // 商品重量
    $ship_params['SenderName'] = '寄件人';                         // 寄件人姓名
    $ship_params['SenderPhone'] = '0912345678';                    // 寄件人手機
    $ship_params['SenderZipCode'] = '100';                         // 寄件人郵遞區號
    $ship_params['SenderAddress'] = '測試寄件地址';                 // 寄件人地址
    $ship_params['ReceiverName'] = $user_name;                     // 收件人姓名
    $ship_params['ReceiverPhone'] = $user_phone;                   // 收件人手機
    $ship_params['ReceiverZipCode'] = $pos_code;                   // 收件人郵遞區號
    $ship_params['ReceiverAddress'] = $user_addressd;              // 收件人地址
    // $ship_params['ReceiverAddress'] = '台中市西區一二路34號';    // 收件人地址;              // 收件人地址

} elseif ($deliveryMethod == 'FAMI') {
    $ship_params['LogisticsType'] = 'CVS';                         // 超取
    $ship_params['LogisticsSubType'] = 'FAMI';                     // 全家超取 FAMI
    $ship_params['SenderName'] = '測試寄件人';                      // 寄件人姓名
    $ship_params['ReceiverName'] = $user_name;                     // 收件人姓名
    $ship_params['ReceiverCellPhone'] = $user_phone;               // 收件人手機
    $ship_params['ReceiverStoreID'] = $CVSStoreID;                 // 收件人門市代號 
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

// 發送建立物流請求
$response = httpPost($apiUrl, $ship_params);
// var_dump(json_decode($response, true));
// 處理回應
if ($response) {
    parse_str($response, $result);
    if(isset($result['RtnCode'])){
        // 交易訂單處理中，更新相應訂單物流狀態為訂單處理中
        $LogisticsStatus =$result['RtnCode'] . '串接成功' ;
        $updateShipStatusQuery = "UPDATE orders SET ship_status = ? WHERE order_num = ?";
        $updateStmt = $conn->prepare($updateShipStatusQuery);
        $updateStmt->bind_param("ss", $LogisticsStatus, $order_num);
        $updateStmt->execute();
        $updateStmt->close();
        echo '<br> 訂單編號：' . $order_num . '，串接成功';
    }else{
        print_r($result); 
    }
    
} else {
    echo 'Failed to connect to the 綠界物流.';
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