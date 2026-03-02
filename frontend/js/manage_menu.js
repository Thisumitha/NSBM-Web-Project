
// 1. Get the logged-in Stall ID (Assuming you saved this during login)
// If you are testing without login, you can hardcode this: const stallId = 1;
const stallId = sessionStorage.getItem('stall_id') || 1;
console.log(stallId);
// Variable to store the file object for upload
let selectedFile = null;
let allMenuItems = []; // Store fetched items here

const API_URL_FETCH = '../../Backend/stallController/get_menu_items.php';
const API_URL_ADD = '../../Backend/stallController/add_menu_item.php';
const API_URL_UPDATE = '../../Backend/stallController/update_menu_item.php';
const API_URL_DELETE = '../../Backend/stallController/delete_menu_item.php';

// --- FUNCTION: LOAD MENU ITEMS FROM DB ---
function loadMenu() {
    fetch(`${API_URL_FETCH}?stall_id=${stallId}`)
        .then(response => response.json())
        .then(data => {
            const grid = document.getElementById('menuGrid');
            grid.innerHTML = '';

            // Update global store
            allMenuItems = data;

            if (data.length === 0) {
                grid.innerHTML = '<p class="text-center text-muted">No items found. Add your first dish!</p>';
                return;
            }

            data.forEach((item) => {
                // Handle image path (if it's a relative path from upload, fix it)
                let displayImage = item.image_url;
                if (displayImage && !displayImage.startsWith('http')) {
                    displayImage = '../../Backend/' + displayImage;
                }
                if (!displayImage || displayImage === "https://via.placeholder.com/150" || displayImage.includes("via.placeholder.com")) {
                    displayImage = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1 1'%3E%3Crect fill='%23cccccc' width='1' height='1'/%3E%3C/svg%3E";
                }

                grid.innerHTML += `
                <div class="col">
                    <div class="food-card h-100">
                        <div class="food-img-container">
                            <img src="${displayImage}" alt="${item.item_name}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100%25\' height=\'100%25\' viewBox=\'0 0 1 1\'%3E%3Crect fill=\'%23cccccc\' width=\'1\' height=\'1\'/%3E%3C/svg%3E'">
                            <div class="price-badge">LKR ${item.price}</div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-light text-secondary border">${item.category}</span>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary p-1 me-2" onclick="editMenu(${item.id})"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn btn-sm btn-outline-danger p-1" onclick="deleteMenu(${item.id})"><i class="bi bi-trash"></i></button>
                                </div>
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
document.getElementById('addMenuForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    submitBtn.disabled = true;

    const formData = new FormData(this);
    formData.append('stall_id', stallId);

    if (selectedFile) {
        formData.append('image', selectedFile);
    }

    const isUpdate = !!formData.get('id');
    const url = isUpdate ? API_URL_UPDATE : API_URL_ADD;

    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(isUpdate ? "Item Updated Successfully!" : "Item Added Successfully!");
                loadMenu();
                resetForm();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Failed to connect to server.");
        })
        .finally(() => {
            submitBtn.innerHTML = isUpdate ? '<i class="bi bi-check-lg me-2"></i>Update Item' : '<i class="bi bi-plus-lg me-2"></i>Publish Item';
            submitBtn.disabled = false;
        });
});

// --- FUNCTION: DELETE ITEM ---
function deleteMenu(id) {
    if (confirm("Are you sure you want to remove this item? This cannot be undone.")) {
        fetch(API_URL_DELETE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    loadMenu();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error('Error deleting:', error));
    }
}

// --- FUNCTION: EDIT ITEM ---
function editMenu(id) {
    // Find item from global array
    const item = allMenuItems.find(i => i.id == id);
    if (!item) {
        alert("Item not found!");
        return;
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });

    document.getElementById('itemId').value = item.id;
    document.getElementById('itemName').value = item.item_name;
    document.getElementById('itemPrice').value = item.price;
    document.getElementById('itemCategory').value = item.category;

    // Update Preview
    updatePreview();
    // If image exists, update preview (handling potential relative paths)
    // If image exists, update preview (handling potential relative paths)
    if (item.image_url) {
        let displayImage = item.image_url.startsWith('http') ? item.image_url : '../../Backend/' + item.image_url;

        // Check for placeholder URL
        if (displayImage.includes("via.placeholder.com")) {
            displayImage = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1 1'%3E%3Crect fill='%23cccccc' width='1' height='1'/%3E%3C/svg%3E";
        }

        document.getElementById('previewImgDisplay').src = displayImage;
    } else {
        document.getElementById('previewImgDisplay').src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1 1'%3E%3Crect fill='%23cccccc' width='1' height='1'/%3E%3C/svg%3E";
    }

    // Update UI State
    document.querySelector('.add-item-card h5').innerText = "Edit Menu Item";
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="bi bi-check-lg me-2"></i>Update Item';
    submitBtn.classList.remove('btn-primary');
    submitBtn.classList.add('btn-warning', 'text-dark'); // Visual cue for edit mode

    document.getElementById('cancelBtn').classList.remove('d-none');
    document.querySelector('.upload-box p').innerText = "Upload to change image";
}

// --- FUNCTION: RESET FORM ---
function resetForm() {
    document.getElementById('addMenuForm').reset();
    document.getElementById('itemId').value = '';
    selectedFile = null;

    // Reset UI State
    document.querySelector('.add-item-card h5').innerText = "Add New Item";
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="bi bi-plus-lg me-2"></i>Publish Item';
    submitBtn.classList.add('btn-primary');
    submitBtn.classList.remove('btn-warning', 'text-dark');

    document.getElementById('cancelBtn').classList.add('d-none');

    // Reset Preview
    document.getElementById('previewImgDisplay').src = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1 1'%3E%3Crect fill='%23cccccc' width='1' height='1'/%3E%3C/svg%3E";
    document.querySelector('.upload-box p').innerText = "Click to upload image";
    document.querySelector('.upload-box').style.borderColor = "#cbd5e1";
    updatePreview();
}

// --- FUNCTION: VIEW SHOP ---
function viewShop() {
    if (stallId) {
        window.open(`aftermenu.html?id=${stallId}`, '_blank');
    } else {
        alert("Stall ID not found. Please log in.");
    }
}

// Initial Load
document.addEventListener('DOMContentLoaded', loadMenu);
