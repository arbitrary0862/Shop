<?php
include 'db_connect.php';

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

  <div id="cart">
    <h2>購物車</h2>
    <ul id="cart-items"></ul>
    <p>Total: <span id="total">0</span> 元</p>
    <button onclick="checkout()">結帳</button>
  </div>
  <script src="main.js"></script>
</body>

</html>