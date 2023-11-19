前台功能：

前端頁面應該列出所有商品供使用者選擇，每個商品需要有加入購物車的按鈕，且可挑選商品數量。
每次按下加入購物車按鈕時，將商品加入購物車清單中(存入購物車資料庫)，該購物清單需顯示在前台，且要可以移除商品(移除購物車資料庫)。

訂單處理：
使用者填寫姓名、電話、地址等訂單資訊後，應能將購物車內容與訂單資訊一同送至後端處理。
在後端應該有一個處理訂單的功能，串接綠界測試金流，透過使用者提供的資訊進行付款。
付款狀態回傳：

在付款處理完畢後，接收並處理綠界測試金流回傳的付款狀態。根據回傳的狀態更新訂單的付款狀態，以便使用者能夠查看，確認完成後清空購物車，並刷新頁面。

後台功能：
商品管理：
後台應該有新增、編輯和刪除商品的功能，可以透過後台介面對商品資訊進行管理，這些操作也需要對應到資料庫的修改。
訂單查看：
可以透過後台查看所有訂單，並能夠對訂單狀態進行管理，已付款等狀態。

資料庫結構（MySQL）：
商品表 products：
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    price DECIMAL(10, 2),
    description TEXT
);

訂單表 orders：
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_num VARCHAR(50),
    product_id VARCHAR(50),
    product_quantity VARCHAR(50),
    order_price INT,
    user_name VARCHAR(50),
    user_phone VARCHAR(20),
    user_address VARCHAR(100),
    payment_status VARCHAR(20)
);

購物車 cart：
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    product_name VARCHAR(100),
    product_price DECIMAL(10, 2),
    quantity INT
);
