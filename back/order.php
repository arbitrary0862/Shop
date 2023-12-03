<?php
include '../db_connect.php';

// 拉取所有訂單
$result = $conn->query("SELECT * FROM orders");
?>

<!DOCTYPE html>
<html>

<head>
    <title>後台管理</title>
</head>

<body>
    <h1>後台管理 - 訂單</h1>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>訂單編號</th>
            <th>商品 ID</th>
            <th>商品 數量</th>
            <th>商品 價格</th>
            <th>使用者姓名</th>
            <th>使用者電話</th>
            <th>使用者地址</th>
            <th>付款狀態</th>
            <th>查詢付款狀態</th>
            <th>配送方式</th>
            <th>配送店ID</th>
            <th>配送店名</th>
            <th>串接物流</th>
            <th>物流編號</th>
            <th>物流狀態</th>
            <th>查詢物流狀態</th>
        </tr>

        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>". $row["order_num"] ."</td>";
            echo "<td>" . $row['product_id'] . "</td>";
            echo "<td>". $row["product_quantity"] . "</td>";
            echo "<td>". $row["order_price"] ."</td>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['user_phone'] . "</td>";
            echo "<td>" . $row['user_address'] . "</td>";
            echo "<td>" . $row['payment_status'] . "</td>";
            echo '<td>' . '<a href="../money/QueryOrder.php?order_id=' . $row['order_num'] . '">查詢付款</a></td>';
            echo "<td>" . $row['deliveryMethod'] . "</td>";
            echo "<td>" . $row['CVSStoreID'] . "</td>";
            echo "<td>" . $row['CVSStoreName'] . "</td>";
            echo '<td>' . '<a href="../ship/CreateShip.php?order_id=' . $row['order_num'] . '">串接物流</a></td>';
            echo "<td>" . $row['ship_num'] . "</td>";
            echo "<td>" . $row['ship_status'] . "</td>";
            echo '<td>' . '<a href="../ship/QueryShip.php?order_id=' . $row['order_num'] . '">查詢物流</a></td>';
            echo "</tr>";
        }
        ?>
    </table>
</body>

</html>