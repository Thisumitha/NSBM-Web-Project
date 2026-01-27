
    // 1. Load Data from Database when page loads
    document.addEventListener("DOMContentLoaded", function() {
        fetchStalls();
    });

    function fetchStalls() {
    // ✅ 1. Put the correct path here at the top
    fetch('../../Backend/stallController/get_stalls.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const tbody = document.getElementById('stallTableBody');
        tbody.innerHTML = ''; // Clear current list
        let count = 0;

        if (data.length === 0) {
             tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No outlets found. Add one!</td></tr>';
        } else {
            data.forEach(stall => {
                count++;
                addStallToTable(stall);
            });
        }
        
        // ✅ 2. Removed the broken fetch line that was here
        document.getElementById('totalCount').innerText = count;
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        document.getElementById('stallTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading data. Check console for details.</td></tr>';
    });
}

    // 2. Add New Stall to Database
    document.getElementById('addStallForm').addEventListener('submit', function(e) {
        e.preventDefault(); 

        const formData = {
            name: document.getElementById('stallName').value,
            category: document.getElementById('stallCategory').value,
            owner: document.getElementById('stallOwner').value,
            status: document.getElementById('stallStatus').value
        };

        fetch('../../Backend/stallController/add_stall.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(result => {
            alert('Saved to Database!');
            fetchStalls(); // Refresh table to show new data
            this.reset();
        })
        .catch(error => console.error('Error:', error));
    });

    // Helper function to render table row
    function addStallToTable(stall) {
        const tbody = document.getElementById('stallTableBody');
        
        let statusBadge = 'bg-success';
        if(stall.status === 'Closed') statusBadge = 'bg-danger';
        if(stall.status === 'Maintenance') statusBadge = 'bg-warning text-dark';

        let categoryBadge = 'bg-secondary';
        if(stall.category === 'Beverages') categoryBadge = 'bg-info text-dark';
        if(stall.category === 'Fast Food') categoryBadge = 'bg-warning text-dark';
        if(stall.category === 'Desserts') categoryBadge = 'bg-danger text-white'; // Added color for desserts

        const newRow = `
            <tr>
                <td>#${stall.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded p-1 me-2"><i class="bi bi-shop"></i></div>
                        <span class="fw-bold">${stall.name}</span>
                    </div>
                </td>
                <td><span class="badge ${categoryBadge}">${stall.category}</span></td>
                <td>${stall.owner}</td>
                <td><span class="badge ${statusBadge}">${stall.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-light text-danger" onclick="deleteStall(${stall.id})"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', newRow);
    }

    // 3. Optional: Delete Stall Function (Placeholder)
    function deleteStall(id) {
        if(confirm("Are you sure you want to delete Stall ID: " + id + "?")) {
            // Note: You need a delete_stall.php file to make this permanent
            console.log("Deleting stall " + id);
            // Example fetch code for delete:
            /*
            fetch('delete_stall.php', {
                method: 'POST',
                body: JSON.stringify({id: id})
            }).then(() => fetchStalls());
            */
           alert("Delete feature requires delete_stall.php backend script.");
        }
    }
