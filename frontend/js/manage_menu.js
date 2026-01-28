
    // 1. Get the logged-in Stall ID (Assuming you saved this during login)
    // If you are testing without login, you can hardcode this: const stallId = 1;
    const stallId = sessionStorage.getItem('loggedStallId') || 1; 

    // Variable to store the file object for upload
    let selectedFile = null;

    // --- FUNCTION: LOAD MENU ITEMS FROM DB ---
    function loadMenu() {
        fetch(`../../Backend/stallController/get_menu_items.php?stall_id=${stallId}`)
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('menuGrid');
            grid.innerHTML = '';

            if(data.length === 0) {
                grid.innerHTML = '<p class="text-center text-muted">No items found. Add your first dish!</p>';
                return;
            }

            data.forEach((item) => {
                // Handle image path (if it's a relative path from upload, fix it)
                let displayImage = item.image_url;
                if(!displayImage.startsWith('http')) {
                    displayImage = '../../Backend/' + displayImage; 
                }

                grid.innerHTML += `
                <div class="col">
                    <div class="food-card h-100">
                        <div class="food-img-container">
                            <img src="${displayImage}" alt="${item.item_name}" onerror="this.src='https://via.placeholder.com/400x300'">
                            <div class="price-badge">LKR ${item.price}</div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-light text-secondary border">${item.category}</span>
                                <button class="btn btn-sm text-danger p-0" onclick="deleteMenu(${item.id})"><i class="bi bi-trash"></i></button>
                            </div>
                            <h6 class="fw-bold text-dark mb-0">${item.item_name}</h6>
                        </div>
                    </div>
                </div>`;
            });
        })
        .catch(error => console.error('Error loading menu:', error));
    }

    // --- FUNCTION: HANDLE FILE SELECTION ---
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            selectedFile = file; // Store file for upload
            const objectUrl = URL.createObjectURL(file);
            document.getElementById('previewImgDisplay').src = objectUrl;
            document.querySelector('.upload-box p').innerText = "Image Selected!";
            document.querySelector('.upload-box').style.borderColor = "#198754";
        }
    }

    // --- FUNCTION: LIVE TEXT PREVIEW ---
    function updatePreview() {
        document.getElementById('previewNameDisplay').innerText = document.getElementById('itemName').value || "Item Name";
        document.getElementById('previewPriceBadge').innerText = "LKR " + (document.getElementById('itemPrice').value || "0");
        document.getElementById('previewCategoryBadge').innerText = document.getElementById('itemCategory').value;
    }

    // --- FUNCTION: SUBMIT TO PHP ---
    document.getElementById('addMenuForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Use FormData to send files and text together
        const formData = new FormData();
        formData.append('stall_id', stallId);
        formData.append('name', document.getElementById('itemName').value);
        formData.append('price', document.getElementById('itemPrice').value);
        formData.append('category', document.getElementById('itemCategory').value);
        
        if(selectedFile) {
            formData.append('image', selectedFile);
        }

        // Send to PHP
        fetch('../../Backend/stallController/add_menu_item.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert("Item Added Successfully!");
                loadMenu(); // Refresh the grid
                
                // Reset Form
                e.target.reset();
                document.getElementById('previewImgDisplay').src = "https://via.placeholder.com/400x300?text=Preview";
                selectedFile = null;
                updatePreview();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // --- FUNCTION: DELETE ITEM ---
    function deleteMenu(id) {
        // You will need a delete_menu_item.php file for this to work
        if(confirm("Remove this item?")) {
            console.log("Delete logic for ID: " + id);
            // fetch('delete_menu.php', { method: 'POST', body: ... })
        }
    }

    // Initial Load
    loadMenu();
