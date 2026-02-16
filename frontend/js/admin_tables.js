
        // --- CONFIGURATION ---
        const API_GET_STALLS = '../../Backend/stallController/get_stalls.php';
        const API_SAVE_TABLE = '../../Backend/TableController/save_table.php';
        const API_GET_TABLES = '../../Backend/TableController/get_tables.php';
        const APP_URL = 'http://localhost/NSBM-Web-Project/frontend/pages/menu.html';

        // 1. Load Stalls into Checkboxes
        async function loadStallsForForm() {
            try {
                const res = await fetch(API_GET_STALLS);
                const stalls = await res.json();
                const container = document.getElementById('stallListContainer');
                container.innerHTML = '';

                if(stalls.length === 0) {
                    container.innerHTML = '<div class="text-danger small text-center">No stalls found. Create stalls first.</div>';
                    return;
                }

                // "Select All" Option
                container.innerHTML += `
                    <div class="form-check border-bottom mb-2 pb-2">
                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAll(this)">
                        <label class="form-check-label fw-bold text-dark" for="selectAll">Select All Outlets</label>
                    </div>`;

                stalls.forEach(stall => {
                    container.innerHTML += `
                        <div class="form-check">
                            <input class="form-check-input stall-check" type="checkbox" value="${stall.id}" id="s_${stall.id}">
                            <label class="form-check-label" for="s_${stall.id}">
                                ${stall.name} <span class="text-muted small">(${stall.category})</span>
                            </label>
                        </div>`;
                });
            } catch (err) { console.error(err); }
        }

        function toggleAll(source) {
            document.querySelectorAll('.stall-check').forEach(cb => cb.checked = source.checked);
        }

        // 2. Load Existing Tables
        async function loadTables() {
            try {
                const res = await fetch(API_GET_TABLES);
                const tables = await res.json();
                const tbody = document.getElementById('tablesTableBody');
                tbody.innerHTML = '';

                if(tables.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-5 text-muted">No tables configured yet.</td></tr>';
                    return;
                }

                tables.forEach(t => {
                    const qrLink = `${APP_URL}?table=${t.table_number}`;
                    
                    // Styled badges using Bootstrap utilities
                    let assignedHtml = t.allowed_stalls 
                        ? `<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">${t.allowed_stalls}</span>`
                        : `<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill">All Outlets (Public)</span>`;

                    const row = `
                    <tr>
                        <td class="ps-4 fw-bold text-dark fs-5">#${t.table_number}</td>
                        <td>${assignedHtml}</td>
                        <td>
                            <a href="${qrLink}" target="_blank" class="btn btn-sm btn-light border text-primary">
                                <i class="bi bi-box-arrow-up-right"></i> Open
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteTable(${t.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                    tbody.innerHTML += row;
                });
            } catch (err) { console.error(err); }
        }

        // 3. Save Table
        document.getElementById('createTableForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;
            submitBtn.innerText = "Saving...";
            submitBtn.disabled = true;

            const tableNum = document.getElementById('tableNumber').value;
            const selectedStalls = Array.from(document.querySelectorAll('.stall-check:checked')).map(cb => cb.value);

            const payload = {
                table_number: tableNum,
                allowed_stalls: selectedStalls
            };

            try {
                const res = await fetch(API_SAVE_TABLE, {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if(result.success) {
                    alert("Table Created Successfully!");
                    document.getElementById('createTableForm').reset();
                    loadTables();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (err) { 
                alert("Connection Failed"); 
            } finally {
                submitBtn.innerText = originalText;
                submitBtn.disabled = false;
            }
        });

        // 4. Delete Table
        function deleteTable(id) {
            if(confirm("Delete this table configuration?")) {
                alert("Delete logic should be implemented in backend."); 
            }
        }

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            loadStallsForForm();
            loadTables();
        });
    