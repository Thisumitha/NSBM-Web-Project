
    // --- 1. GLOBAL VARIABLES ---
    let allStallsData = [];
    const CART_KEY = 'nsbm_cart';

    // --- 2. INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        
        // A. CHECK URL FOR TABLE NUMBER (QR SCAN)
        const urlParams = new URLSearchParams(window.location.search);
        const tableId = urlParams.get('table'); // e.g. ?table=5

        if (tableId) {
            // Save to Local Storage so it persists across pages
            localStorage.setItem('nsbm_table_id', tableId);
        }

        // B. DISPLAY TABLE BADGE IF EXISTS
        const savedTable = localStorage.getItem('nsbm_table_id');
        if (savedTable) {
            const badge = document.getElementById('headerTableBadge');
            const text = document.getElementById('tableNumberText');
            
            // Only update if elements exist in HTML
            if (badge && text) {
                badge.style.display = 'flex';
                text.innerText = `Table #${savedTable}`;
            }
        }

        loadStalls();
        updateCartPopup(); // Check cart on page load
    });

    // --- 3. LOAD STALLS (API) ---
// --- 3. LOAD STALLS (API) ---
    async function loadStalls() {
        const container = document.getElementById('storesContainer');
        
        // Base API URL
        let apiUrl = '../../Backend/TableController/loadAssignTables.php';

        // 1. Check if we have a Table ID stored
        const storedTableId = localStorage.getItem('nsbm_table_id');

        // 2. If yes, append it to the URL query string
        if (storedTableId) {
            apiUrl += `?table_id=${storedTableId}`;
            console.log("Fetching filtered stalls for Table:", storedTableId);
        } else {
            console.log("Fetching ALL stalls (No table scanned)");
        }

        try {
            const response = await fetch(apiUrl);
            const stalls = await response.json();
            
            allStallsData = stalls; // Update global data
            renderStalls(stalls);   // Update UI

        } catch (error) {
            console.error('Error loading stalls:', error);
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="alert alert-danger d-inline-block px-4 py-3 rounded-4 shadow-sm border-0">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Failed to load stores. <br> <small class="opacity-75">${error.message}</small>
                    </div>
                </div>`;
        }
    }

    // --- 4. RENDER FUNCTIONS ---
    function renderStalls(stalls) {
        const container = document.getElementById('storesContainer');
        container.innerHTML = ''; 

        if (stalls.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5 opacity-50">
                    <i class="bi bi-shop display-1 mb-3"></i>
                    <h5>No stores found.</h5>
                </div>`;
            return;
        }

        stalls.forEach(stall => {
            const rootPath = "../../"; 
            let imageUrl = "https://via.placeholder.com/600x400?text=NSBM+Store"; 

            // Image Path Logic
            if (stall.image_path) {
                if (stall.image_path.includes('Backend') || stall.image_path.includes('backend')) {
                    imageUrl = rootPath + stall.image_path;
                } else if (stall.image_path.includes('uploads/')) {
                    imageUrl = rootPath + "Backend/" + stall.image_path;
                } else {
                    imageUrl = stall.image_path; // Assume full URL
                }
            }

            const statusText = stall.status || 'Open';
            const statusClass = statusText.toLowerCase() === 'open' ? 'status-open' : 'status-closed';

            const cardHtml = `
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card store-card shadow-sm h-100">
                        <div class="img-wrapper">
                            <img src="${imageUrl}" alt="${stall.name}" onerror="this.src='https://via.placeholder.com/600x400?text=Image+Not+Found'">
                            <span class="status-badge ${statusClass}">
                                <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i> ${statusText}
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <span class="store-cat mb-1">${stall.category}</span>
                            <h5 class="fw-bold text-dark mb-1">${stall.name}</h5>
                            <p class="text-secondary small mb-3">Owner: ${stall.owner}</p>
                            
                            <div class="mt-auto">
                                <a href="aftermenu.html?id=${stall.id}" class="btn btn-visit w-100 shadow-sm">
                                    View Menu <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += cardHtml;
        });
    }

    function filterStalls() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const filtered = allStallsData.filter(stall => 
            stall.name.toLowerCase().includes(query) || 
            stall.category.toLowerCase().includes(query)
        );
        renderStalls(filtered);
    }

    // --- 5. CART POPUP LOGIC ---
    function updateCartPopup() {
        const cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
        const popup = document.getElementById('cartPopup');
        
        let totalQty = 0;
        let totalPrice = 0;

        cart.forEach(item => {
            totalQty += item.qty;
            totalPrice += (item.price * item.qty);
        });

        if (totalQty > 0) {
            popup.style.display = 'flex'; 
            popup.classList.add('pulse'); 
            document.getElementById('cartCount').innerText = `${totalQty} Items`;
            document.getElementById('cartTotal').innerText = `LKR ${totalPrice.toFixed(0)}`;
        } else {
            popup.style.display = 'none'; 
            popup.classList.remove('pulse');
        }
    }
