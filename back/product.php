<?php
include '../db_connect.php';

// 拉取所有商品
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>

<head>
    <title>後台管理</title>
</head>

<body>
    <h1>後台管理 - 商品</h1>

    <!-- 商品列表 -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>名稱</th>
            <th>價格</th>
            <th>描述</th>
        </tr>

        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>$" . $row['price'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <!-- 新增商品表單 -->
    <h2>新增商品</h2>
    <form action="add_product.php" method="post">
        商品名稱: <input type="text" name="name"><br>
        價格: <input type="text" name="price"><br>
        描述: <input type="text" name="description"><br>
        <input type="submit" value="新增商品">
    </form>
</body>

</html>