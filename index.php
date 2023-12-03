<?php
include 'db_connect.php';
// print_r($_POST);
if($_POST != null){
  $SHOP_ID = $_POST['CVSStoreID'];
  $SHOP_Name = $_POST['CVSStoreName'];
}else{
  $SHOP_ID = null;
  $SHOP_Name = null;
}


// print_r($SHOP);
// 從資料庫取得產品
$result = $conn->query("SELECT * FROM products");
?>

<!-- 商品列表 -->

<!DOCTYPE html>
<html>

<head>
  <title>商品列表</title>
</head>

<body>
  <div id="user-info">
    <h2>訂購人資訊</h2>
    <label for="name">姓名：</label>
    <input type="text" id="name" placeholder="請輸入姓名">

    <label for="phone">電話：</label>
    <input type="tel" id="phone" placeholder="請輸入電話">

    <label for="pos_code">郵遞區號：</label>
    <input type="text" id="pos_code" placeholder="請輸入郵遞區號">

    <label for="address">地址：</label>
    <input type="text" id="address" placeholder="請輸入地址">
  </div>

  <div id="products">
    <h2>可選商品</h2>
    <ul id="product-list">
      <?php
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<li>" . $row["name"] . " - " . $row["price"] . "元
                <input type='number' id='quantity-" . $row["id"] . "' value='1' min='1'>
                <button onclick='addToCart(" . $row["id"] . ", \"" . $row["name"] . "\", " . $row["price"] . ")'>加入購物車</button></li>";
        }
      } else {
        echo "0 結果";
      }
      $conn->close();
      ?>
    </ul>
  </div>
  <div id="delivery-info">
    <h2>配送方式</h2>
    <label>
      <input type="radio" name="delivery" value="FAMI" onclick="handleDeliveryOption('FAMI')" checked>
      全家取貨
    </label>
    <label>
      <input type="radio" name="delivery" value="HOME" onclick="handleDeliveryOption('HOME')">
      宅配
    </label>
  </div>
  <div id="cart">
    <h2>購物車</h2>
    <ul id="cart-items"></ul>
    <p>Total: <span id="total">0</span> 元</p>
    <input id="CVSStoreID" name="CVSStoreID" value="<?php echo $SHOP_ID?>">
    <input id="CVSStoreName" name="CVSStoreName" value="<?php echo $SHOP_Name?>">
    <div id="cvsStoreSection">
      <button onclick="chooseCVSStore()">選擇超取門市</button>
    </div>
    <button onclick="checkout()" id="checkoutButton">結帳</button>
  </div>
  <script src="main.js"></script>
</body>

</html>

<script>
  // 監聽配送方式的變化
  function handleDeliveryOption(option) {
    const cvsStoreSection = document.getElementById('cvsStoreSection');
    const CVSStoreID = document.getElementById('CVSStoreID');
    const CVSStoreName = document.getElementById('CVSStoreName');

    // 如果選擇的是全家取貨
    if (option === 'FAMI') {
      cvsStoreSection.style.display = 'block'; // 顯示選擇超取門市的按鈕
      CVSStoreID.style.display = 'block'; // 顯示選擇超取ID
      CVSStoreName.style.display = 'block'; // 顯示選擇超取門市
    } else {
      cvsStoreSection.style.display = 'none'; // 隱藏選擇超取門市的按鈕
      CVSStoreID.style.display = 'none'; // 隱藏選擇超取ID
      CVSStoreName.style.display = 'none'; // 隱藏選擇超取門市
    }
  }

  // 使用者選擇超取門市的函數，這部分需要你自行實現
  function chooseCVSStore() {
    window.location.href = 'ship/ChooseShip.php';
  }
</script>