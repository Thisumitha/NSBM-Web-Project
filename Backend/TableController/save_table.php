<?php
// add_table.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include '../DBMSConector/db_connect.php';

$response = array();

// --- STEP 1: Create 'physical_tables' Table ---
$sql_tables = "CREATE TABLE IF NOT EXISTS physical_tables (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    table_number VARCHAR(50) NOT NULL UNIQUE, 
    qr_code_string VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql_tables)) {
    echo json_encode(["success" => false, "message" => "Error creating physical_tables: " . $conn->error]);
    exit;
}

// --- STEP 2: Create 'table_permissions' Table ---
// This table links specific tables to specific stalls
$sql_perms = "CREATE TABLE IF NOT EXISTS table_permissions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    table_id INT(11) NOT NULL,
    stall_id INT(11) NOT NULL,
    FOREIGN KEY (table_id) REFERENCES physical_tables(id) ON DELETE CASCADE
)";

if (!$conn->query($sql_perms)) {
    // Note: If 'stalls' table doesn't exist yet, a foreign key constraint might fail. 
    // For this simple version, we proceed, but in production, ensure 'stalls' exists first.
    echo json_encode(["success" => false, "message" => "Error creating table_permissions: " . $conn->error]);
    exit;
}

// --- STEP 3: Process the POST Request ---
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->table_number)) {
    
    $table_num = $conn->real_escape_string($data->table_number);
    
    // Insert Table
    $sql = "INSERT INTO physical_tables (table_number) VALUES ('$table_num')";
    
    // We use try-catch or check return to handle Duplicate Table Numbers
    if($conn->query($sql)) {
        $table_id = $conn->insert_id;
        
        // Add Permissions (if any stalls selected)
        if(!empty($data->allowed_stalls) && is_array($data->allowed_stalls)) {
            $values = [];
            foreach($data->allowed_stalls as $stall_id) {
                $sid = (int)$stall_id;
                // We map the new table ID to the existing Stall ID
                $values[] = "('$table_id', '$sid')";
            }
            
            if(count($values) > 0) {
                $sql_perm = "INSERT INTO table_permissions (table_id, stall_id) VALUES " . implode(", ", $values);
                if(!$conn->query($sql_perm)){
                     // Log error but don't stop execution, main table is created
                     error_log("Permission Insert Error: " . $conn->error);
                }
            }
        }
        
        echo json_encode(["success" => true, "message" => "Table created successfully"]);
    } else {
        // Check for duplicate entry error (Error code 1062)
        if ($conn->errno == 1062) {
             echo json_encode(["success" => false, "message" => "Table Number '$table_num' already exists."]);
        } else {
             echo json_encode(["success" => false, "message" => "Database Error: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing Table Number"]);
}

$conn->close();
?>