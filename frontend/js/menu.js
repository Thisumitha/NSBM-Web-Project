const stores = [
    { name: "Green Mart", logo: "../Resources/menu/st1.png", status: "Open" },
    { name: "Campus Cafe", logo: "../Resources/menu/st2.png", status: "Open" },
    { name: "Book World", logo: "../Resources/menu/st3.png", status: "Open" },
    { name: "Tech Point", logo: "../Resources/menu/st4.png", status: "Open" },
    { name: "Fashion Zone", logo: "../Resources/menu/st5.png", status: "Open" }
];

const container = document.getElementById("storesContainer");

stores.forEach(store => {
    const card = document.createElement("div");
    card.classList.add("store-card");

    const statusClass = store.status === "Open" ? "open" : "closed";

    card.innerHTML = `
        <img src="${store.logo}" alt="${store.name}">
        <h2>${store.name}</h2>
        <span class="store-status ${statusClass}">${store.status}</span>
    `;

    // Click Event (Correct)
    card.addEventListener("click", () => {
        window.location.href = `aftermenu.html?store=${store.name}`;
    });

    container.appendChild(card);
});
