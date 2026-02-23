const cart = JSON.parse(localStorage.getItem("cartItem"));

const box = document.getElementById("cartBox");

if(cart){

    box.innerHTML = `
        <h3>${cart.restaurant}</h3>

        <img src="${cart.img}" width="150">

        <h4>${cart.name}</h4>

        <p>Price: Rs.${cart.price}</p>

        <button id="confirmBtn">Confirm Order</button>
    `;

}else{

    box.innerHTML = "<p>No items in cart</p>";
}

document.getElementById("confirmBtn")?.addEventListener("click",()=>{

    alert("Order Confirmed ✅");

    localStorage.removeItem("cartItem");
});
