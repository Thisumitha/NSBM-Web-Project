const foodList = JSON.parse(localStorage.getItem("foodList"));
const selectedName = localStorage.getItem("selectedFoodName");

const container = document.getElementById("foodDetailsContainer");

if (!foodList || !selectedName) {
  container.innerHTML = "<p>No food selected</p>";
} else {

  const food = JSON.parse(localStorage.getItem("selectedFood"));

const div = document.getElementById("foodDetails");

div.innerHTML = `
  <h3>${food.restaurant}</h3>
  <img src="${food.img}">
  <h2>${food.name}</h2>
  <p>Rs.${food.price}</p>

  <button id="addCart">Add to Cart</button>
`;

document.getElementById("addCart").onclick = () => {

  localStorage.setItem("cartItem", JSON.stringify(food));

  window.location.href = "cart.html";
};



  // 🔁 LOOP ekak athule details generate wenne mehema
  foodList.forEach(food => {

    if (food.name === selectedName) {

      container.innerHTML += `
        <div class="food-details-container">

          <img src="${food.img}">

          <h2>${food.name}</h2>

          <p class="price">Rs. ${food.price}</p>

          <p>Fresh and tasty food prepared at campus cafe 😋</p>

          <h4>Choice of Size (Required)</h4>
          <label><input type="radio" name="size" checked> Regular</label><br>
          <label><input type="radio" name="size"> Large + Rs. 200</label>

          <button class="add-cart-btn">
            Add to cart - Rs. ${food.price}
          </button>

        </div>
      `;
    }

  });

}

