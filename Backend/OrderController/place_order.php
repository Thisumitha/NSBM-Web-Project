<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

ini_set('display_errors', 0);
error_reporting(E_ALL);

include '../DBMSConector/db_connect.php';

$response = array();

$sql_orders_table = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    table_id VARCHAR(50) NOT NULL, 
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql_orders_table)) {
    echo json_encode(["success" => false, "message" => "Error creating orders table: " . $conn->error]);
    exit;
}

$sql_items_table = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    stall_id INT(11) NOT NULL,
    item_id INT(11) NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    quantity INT(11) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)";

if (!$conn->query($sql_items_table)) {
    echo json_encode(["success" => false, "message" => "Error creating order_items table: " . $conn->error]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if(empty($data->items)) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}


$table_id = $conn->real_escape_string($data->table_id);
$total = $conn->real_escape_string($data->total);

$sql_order = "INSERT INTO orders (table_id, total_amount) VALUES ('$table_id', '$total')";

if($conn->query($sql_order)){
    $order_id = $conn->insert_id; 

    $sql_items = "INSERT INTO order_items (order_id, stall_id, item_id, item_name, quantity, price, status) VALUES ";
    $values = [];
    
    foreach($data->items as $item){
        $s_id = (int)$item->stallId;
        $i_id = (int)$item->id;
        $name = $conn->real_escape_string($item->name);
        $qty = (int)$item->qty;
        $price = (float)$item->price;
        $status = 'Pending';
        
        $values[] = "('$order_id', '$s_id', '$i_id', '$name', '$qty', '$price', '$status')";
    }
    
    $sql_items .= implode(", ", $values);

    if($conn->query($sql_items)){
        echo json_encode(["success" => true, "order_id" => $order_id, "message" => "Order placed successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Item Insert Error: " . $conn->error]);
    }
    
} else {
    echo json_encode(["success" => false, "message" => "Order Insert Error: " . $conn->error]);
}

$conn->close();
?>