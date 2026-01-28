
    // Mock Data
    const stalls = [
        { id: 1, name: "Spicy Wok", category: "Main", owner: "K. Perera", status: "Open", icon: "bi-fire" },
        { id: 2, name: "Cool Bar", category: "Drink", owner: "S. Silva", status: "Open", icon: "bi-cup-straw" },
        { id: 3, name: "Tasty Buns", category: "Snack", owner: "M. De Soysa", status: "Closed", icon: "bi-cookie" }
    ];

    function renderTable() {
        const tbody = document.getElementById('stallTableBody');
        tbody.innerHTML = '';

        stalls.forEach(stall => {
            let badgeClass = stall.status === 'Open' ? 'badge-soft-success' : 'badge-soft-danger';
            
            tbody.innerHTML += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                            <i class="bi ${stall.icon} text-secondary"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">${stall.name}</div>
                            <small class="text-muted">ID: #00${stall.id}</small>
                        </div>
                    </div>
                </td>
                <td><span class="text-secondary fw-medium">${stall.category}</span></td>
                <td>${stall.owner}</td>
                <td><span class="badge ${badgeClass}">${stall.status}</span></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            `;
        });
    }

    // Add Form Logic
    document.getElementById('addStallForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const newStall = {
            id: stalls.length + 1,
            name: document.getElementById('stallName').value,
            owner: document.getElementById('stallOwner').value,
            category: document.getElementById('stallCategory').value,
            status: document.getElementById('stallStatus').value,
            icon: "bi-shop"
        };
        stalls.push(newStall);
        renderTable();
        e.target.reset();
    });

    // Init
    renderTable();
