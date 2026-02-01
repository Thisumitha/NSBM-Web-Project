<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../DBMSConector/db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->table_number)) {
    
    $table_num = $conn->real_escape_string($data->table_number);
    
    // 1. Create Table
    $sql = "INSERT INTO physical_tables (table_number) VALUES ('$table_num')";
    
    if($conn->query($sql)) {
        $table_id = $conn->insert_id;
        
        // 2. Add Permissions (if any stalls selected)
        if(!empty($data->allowed_stalls)) {
            $values = [];
            foreach($data->allowed_stalls as $stall_id) {
                $sid = (int)$stall_id;
                $values[] = "('$table_id', '$sid')";
            }
            
            $sql_perm = "INSERT INTO table_permissions (table_id, stall_id) VALUES " . implode(", ", $values);
            $conn->query($sql_perm);
        }
        
        echo json_encode(["success" => true, "message" => "Table created successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Table Number already exists or DB Error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing Table Number"]);
}
$conn->close();
?>