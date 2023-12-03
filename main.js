let cart = [];
let total = 0;
let orders = [];

// 初始載入判定購物車是否為空
window.addEventListener('load', () => {
  fetchCartItems();
});

// 獲取購物車商品
function fetchCartItems() {
  fetch('get_cart_items.php')
  .then(response => {
      if (!response.ok) {
          throw new Error('連線失敗');
      }
      return response.json();
  })
  .then(data => {
      if (data.cart.length > 0) {
          cart = data.cart;
          updateCart();
      }
      // 如果購物車是空的，不執行任何額外的動作，保持原本的頁面狀態
  })
  .catch(error => {
      console.error('無法取得購物車內容：', error);
      // 處理錯誤的情況，例如顯示錯誤訊息給使用者
  });
}

// 更新購物車
function updateCart() {
  const cartList = document.getElementById("cart-items");
  cartList.innerHTML = "";
  total = 0;
  cart.forEach((item) => {
    const listItem = document.createElement("li");
    listItem.innerHTML = `
     ${item.product_name} - ${item.product_price} 元 x ${item.quantity}
     <button onclick="removeFromCart(${item.product_id})">刪除</button>
    `;

    cartList.appendChild(listItem);

    total += parseFloat(item.product_price) * parseInt(item.quantity);
  });

  document.getElementById("total").textContent = total;
}

// 加入購物車
function addToCart(productId, productName, productPrice) {
  const quantity = parseInt(document.getElementById(`quantity-${productId}`).value);
  const data = {
    productId: productId,
    productName: productName,
    productPrice: productPrice,
    quantity: quantity
  };

  fetch('add_to_cart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(data),
  })
  .then(response => response.json())
  .then(data => {
    // 更新本地的購物車數據
    cart = data.cart;
    updateCart();
  })
  .catch(error => {
    console.error('加入購物車失敗：', error);
  });
}

// 移除商品
function removeFromCart(productId) {
  fetch('remove_from_cart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ productId }),
  })
  .then(response => response.json())
  .then(data => {
    // 更新本地的購物車數據
    cart = data.cart;
    updateCart();
  })
  .catch(error => {
    console.error('刪除失敗：', error);
  });
}

// 送出檢查
function checkout() {
  const name = document.getElementById("name").value;
  const phone = document.getElementById("phone").value;
  const pos_code = document.getElementById("pos_code").value;
  const address = document.getElementById("address").value;
  const order_price = document.getElementById("total").textContent;
  const deliveryMethod = document.querySelector('input[name="delivery"]:checked').value;
  const CVSStoreID = document.getElementById("CVSStoreID").value;
  const CVSStoreName = document.getElementById("CVSStoreName").value;
  // 檢查是否填寫了訂購人資訊
  if (!name || !phone || !pos_code || !address) {
    alert("請填寫完整訂購人資訊");
    return;
  }
  // console.log(cart);
  const productIdList = cart.map(item => item.product_id);
  const productquantityList = cart.map(item => item.quantity);
  const order = {
    name: name,
    phone: phone,
    pos_code: pos_code,
    address: address,
    productId: productIdList.join(','),
    productquantity: productquantityList.join(','),
    order_price: order_price,
    deliveryMethod: deliveryMethod, // 加入配送方式
    CVSStoreID: CVSStoreID, // 配送門市號碼
    CVSStoreName: CVSStoreName, // 配送門市名稱
  };

  // 使用 fetch 將資料發送到寫入資料庫PHP
  fetch('write_to_database.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(order),
    })
    .then(response => response.json())
    .then(data => {
      const orderParams = data.orderParams;

      // 顯示訂單建立成功的提示訊息
      alert(`已建立訂單！訂單編號：${orderParams.MerchantTradeNo}，姓名：${name}，電話：${phone}，地址：${address}`);

      // 建立 Form 表單
      const form = document.createElement('form');

      // 將 Form 附加到 DOM 中
      form.setAttribute('action', 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5'); // 依據實際串接綠界網址替換
      form.setAttribute('enctype', 'application/x-www-form-urlencoded');
      form.setAttribute('method', 'post');

      Object.entries(orderParams).forEach(([key, value]) => {
        const input = document.createElement('input');
        input.setAttribute('type', 'hidden'); //隱藏表單欄位
        input.setAttribute("name", key);
        input.setAttribute("value", value);
        form.appendChild(input);
      });
      
      // 將表單附加到文檔的body中
      document.body.appendChild(form);
      
      // 提交 Form
      form.submit();
    })
    .catch(error => {
      console.error('錯誤發生：', error);
      alert(error);
    });
}

