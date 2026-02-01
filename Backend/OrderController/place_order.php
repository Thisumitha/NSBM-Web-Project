<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include '../DBMSConector/db_connect.php';

// Get JSON Input
$data = json_decode(file_get_contents("php://input"));

if(empty($data->items)) {
    echo json_encode(["success" => false, "message" => "Cart is empty"]);
    exit;
}

// 1. Insert Master Order
$table_id = $conn->real_escape_string($data->table_id);
$total = $conn->real_escape_string($data->total);

$sql_order = "INSERT INTO orders (table_id, total_amount) VALUES ('$table_id', '$total')";

if($conn->query($sql_order)){
    $order_id = $conn->insert_id; // Get the new Order ID
    
    // 2. Prepare Items Query
    $sql_items = "INSERT INTO order_items (order_id, stall_id, item_id, item_name, quantity, price) VALUES ";
    $values = [];
    
    foreach($data->items as $item){
        $s_id = (int)$item->stallId;
        $i_id = (int)$item->id;
        $name = $conn->real_escape_string($item->name);
        $qty = (int)$item->qty;
        $price = (float)$item->price;
        
        $values[] = "('$order_id', '$s_id', '$i_id', '$name', '$qty', '$price')";
    }
    
    $sql_items .= implode(", ", $values);
    
    // 3. Execute Items Insert
    if($conn->query($sql_items)){
        echo json_encode(["success" => true, "order_id" => $order_id]);
    } else {
        echo json_encode(["success" => false, "message" => "Item Error: " . $conn->error]);
    }
    
} else {
    echo json_encode(["success" => false, "message" => "Order Error: " . $conn->error]);
}

$conn->close();
?>