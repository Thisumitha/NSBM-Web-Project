        const API_URL_FETCH = '../../Backend/stallController/get_stalls.php';
        const API_URL_ADD = '../../Backend/stallController/add_stall.php';

        function previewFile() {
            const fileInput = document.getElementById('stallImage');
            const uploadText = document.getElementById('uploadText');
            const uploadIcon = document.getElementById('uploadIcon');

            if (fileInput.files && fileInput.files[0]) {
                uploadText.innerText = fileInput.files[0].name;
                uploadText.classList.add('text-success');
                uploadIcon.className = "bi bi-check-circle-fill fs-2 text-success";
            }
        }

        async function loadStalls() {
            try {
                const response = await fetch(API_URL_FETCH);
                const stalls = await response.json();

                const tbody = document.getElementById('stallTableBody');
                const totalCount = document.getElementById('totalCount');

                tbody.innerHTML = '';
                totalCount.innerText = `${stalls.length} Total`;

                if (stalls.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">No outlets found in database.</td></tr>`;
                    return;
                }

                stalls.forEach(stall => {

                    const badgeClass = stall.status === 'Open' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';

                    let imgHtml = '';
                    if (stall.image_path && stall.image_path !== "default.png") {
                        imgHtml = `<img src="../../Backend/${stall.image_path}" class="rounded-3 me-3 stall-img" alt="logo">`;
                    } else {
                        imgHtml = `<div class="bg-light rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px"><i class="bi bi-shop text-secondary"></i></div>`;
                    }

                    const row = `
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                ${imgHtml}
                                <div>
                                    <div class="fw-bold text-dark">${stall.name}</div>
                                    <small class="text-muted" style="font-size:0.75rem">ID: #${stall.id}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="text-secondary fw-medium">${stall.category}</span></td>
                        <td>${stall.owner}</td>
                        <td><span class="badge ${badgeClass} rounded-pill px-3 py-2 border-0 fw-bold">${stall.status}</span></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;

                    tbody.innerHTML += row;
                });

            } catch (error) {
                console.error('Error fetching data:', error);
                document.getElementById('stallTableBody').innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Error connecting to database.</td></tr>`;
            }
        }

        document.getElementById('addStallForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = "Saving...";
            submitBtn.disabled = true;

            const formData = new FormData(this);

            try {
                const response = await fetch(API_URL_ADD, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    this.reset();
                    document.getElementById('uploadText').innerText = "Click to upload image";
                    document.getElementById('uploadText').classList.remove('text-success');
                    document.getElementById('uploadIcon').className = "bi bi-cloud-arrow-up fs-2 text-secondary";
                    loadStalls();
                    alert("Outlet added successfully!");
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert("Failed to connect to server.");
            } finally {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });

        document.addEventListener('DOMContentLoaded', loadStalls);