// Cart data (can later be replaced with localStorage / DB)
let cartItems = [
  { name: "Chicken Burger", price: 550, qty: 2 },
  { name: "Veg Pizza", price: 1200, qty: 1 },
  { name: "Iced Coffee", price: 350, qty: 3 }
];

const cartBody = document.getElementById("cart-body");
const grandTotalEl = document.getElementById("grand-total");

function renderCart() {
  cartBody.innerHTML = "";
  let grandTotal = 0;

  cartItems.forEach((item, index) => {
    const itemTotal = item.price * item.qty;
    grandTotal += itemTotal;

    const row = document.createElement("tr");

    row.innerHTML = `
      <td>${item.name}</td>
      <td>${item.price}</td>
      <td>${item.qty}</td>
      <td>${itemTotal}</td>
      <td>
        <button class="remove-btn" onclick="removeItem(${index})">✖</button>
      </td>
    `;

    cartBody.appendChild(row);
  });

  grandTotalEl.textContent = `Rs. ${grandTotal}`;
}

function removeItem(index) {
  cartItems.splice(index, 1);
  renderCart();
}

// Load cart on page load
renderCart();
