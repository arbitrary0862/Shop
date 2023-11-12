<?php
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Insert the new product into the database
    $sql = "INSERT INTO products (name, price, description) VALUES ('$name', '$price', '$description')";

    if ($conn->query($sql) === TRUE) {
        echo "商品新增成功";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

}
echo "<script>alert('新增成功');location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";
?>