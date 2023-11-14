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
            <th>商品 ID</th>
            <th>商品 數量</th>
            <th>商品 價格</th>
            <th>使用者姓名</th>
            <th>使用者電話</th>
            <th>使用者地址</th>
            <th>付款狀態</th>
        </tr>

        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['product_id'] . "</td>";
            echo "<td>". $row["product_quantity"] . "</td>";
            echo "<td>". $row["order_price"] ."</td>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['user_phone'] . "</td>";
            echo "<td>" . $row['user_address'] . "</td>";
            echo "<td>" . $row['payment_status'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>

</html>