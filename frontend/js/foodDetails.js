const foodList = JSON.parse(localStorage.getItem("foodList"));
const selectedName = localStorage.getItem("selectedFoodName");

const container = document.getElementById("foodDetailsContainer");

if (!foodList || !selectedName) {
  container.innerHTML = "<p>No food selected</p>";
} else {

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