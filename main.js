let cart = [];
let total = 0;
let orders = [];

// 更新購物車
function updateCart() {
  const cartList = document.getElementById("cart-items");
  cartList.innerHTML = "";
  total = 0;

  cart.forEach((item) => {
    const listItem = document.createElement("li");
    listItem.innerHTML = `
      ${item.productName} - ${item.productPrice} 元 x ${item.quantity}
      <button onclick="removeFromCart(${item.productId})">刪除</button>
    `;
    cartList.appendChild(listItem);

    total += item.productPrice * item.quantity;
  });

  document.getElementById("total").textContent = total;
}

// 加入購物車
function addToCart(productId, productName, productPrice) {
  const quantity = parseInt(document.getElementById(`quantity-${productId}`).value);
  // 購物車內容
  const existingItem = cart.find((item) => item.productId === productId);
  if (existingItem) {
    existingItem.quantity += quantity;
  } else {
    cart.push({
      productId: productId,
      productName: productName, // 添加商品名稱
      productPrice: productPrice, // 添加商品價格
      quantity: quantity
    });
  }

  updateCart();
}

// 移除商品
function removeFromCart(productId) {
  const index = cart.findIndex((item) => item.productId === productId);
  if (index !== -1) {
    cart.splice(index, 1);
    updateCart();
  }
}

// 送出檢查
function checkout() {
  const name = document.getElementById("name").value;
  const phone = document.getElementById("phone").value;
  const address = document.getElementById("address").value;

  // 檢查是否填寫了訂購人資訊
  if (!name || !phone || !address) {
    alert("請填寫完整訂購人資訊");
    return;
  }
  const productIdList = cart.map(item => item.productId);
  const order = {
    name: name,
    phone: phone,
    address: address,
    productId: productIdList.join(',')
  };

  // 綠界金流

  // 在付款成功後寫入資料庫

  // 使用 fetch 將資料發送到寫入資料庫PHP
  fetch('write_to_database.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(order),
    })
    .then(response => response.text())
    .then(data => {
      alert(`付款成功！訂單編號：${data}，姓名：${name}，電話：${phone}，地址：${address}`);
    })
    .catch(error => {
      alert('付款成功但無法寫入資料庫');
    });
  updateCart();
}