// Get store name from URL
const params = new URLSearchParams(window.location.search);
const storeName = params.get("store");

// Display store name in header
document.getElementById("storeName").innerText = storeName;

// Categories
const categories = ["Rice & Curry", "Kottu", "Biryani", "Fried Rice", "Noodles", "Burgers", "Drinks"];

// Food items by category
const foods = {
    "Rice & Curry": [
        { name: "Chicken Curry + Rice", price: 850, img: "../Resources/aftermenu/c1.jpg" },
        { name: "Fish Curry + Rice", price: 900, img: "../Resources/aftermenu/c2.jpg" },
        { name: "Vegetable Curry + Rice", price: 650, img: "../Resources/aftermenu/c3.jpg" }

    ],
    "Kottu": [
        { name: "Chicken Kottu", price: 900, img: "../Resources/aftermenu/k1.jpg" },
        { name: "Egg Kottu", price: 750, img: "../Resources/aftermenu/k2.jpg" },
        { name: "Vegetable Kottu", price: 650, img: "../Resources/aftermenu/k3.jpg" }
    ],
    "Biryani": [
        { name: "Chicken Biryani", price: 950, img: "../Resources/aftermenu/b1.jpg" },
        { name: "Mutton Biryani", price: 1100, img: "../Resources/aftermenu/b2.jpg" }
    ],
    "Fried Rice": [
        { name: "Chicken Fried Rice", price: 800, img: "../Resources/aftermenu/f1.jpg" },
        { name: "Egg Fried Rice", price: 700, img: "../Resources/aftermenu/f2.jpg" },
        { name: "Vegetable Fried Rice", price: 650, img: "../Resources/aftermenu/f3.jpg" }
    ],
    "Noodles": [
        { name: "Chicken Noodles", price: 750, img: "../Resources/aftermenu/n1.jpg" },
        { name: "Egg Noodles", price: 650, img: "../Resources/aftermenu/n2.jpg" },
         { name: "Vegetable Noodles", price: 650, img: "../Resources/aftermenu/n3.jpg" }
    ],
    "Burgers": [
        { name: "Chicken Burger", price: 900, img: "../Resources/aftermenu/bg1.jpg" },
        { name: "Veg Burger", price: 700, img: "../Resources/aftermenu/bg2.jpg" }
    ],
    "Drinks": [
        { name: "Iced Coffee", price: 400, img: "../Resources/aftermenu/d1.jpg" },
        { name: "Milk Shake", price: 500, img: "../Resources/aftermenu/d2.jpg" }
    ]
};

const categoryContainer = document.getElementById("categoryContainer");
const foodContainer = document.getElementById("foodContainer");

// Load categories dynamically
categories.forEach(category => {
    const div = document.createElement("div");
    div.className = "category-card";
    div.textContent = category;

    div.onclick = () => loadFoods(category);
    categoryContainer.appendChild(div);
});

// Load foods dynamically
function loadFoods(category) {
    foodContainer.innerHTML = "";

    foods[category].forEach(food => {
        const card = document.createElement("div");
        card.className = "food-card";

        card.innerHTML = `
            <img src="${food.img}">
            <h3>${food.name}</h3>
            <p>Rs. ${food.price}</p>
            <button>Order</button>
        `;

        card.querySelector("button").onclick = () => {
            alert(`${food.name} ordered!`);
        };

        foodContainer.appendChild(card);
         card.querySelector("button").onclick = () => {
            localStorage.setItem("foodList", JSON.stringify(foods[category]));
            localStorage.setItem("selectedFoodName", food.name);
            window.location.href = "foodDetails.html";
        };

    });
}
// Load first category by default
loadFoods(categories[0]);       