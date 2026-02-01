// ... inside place_order.php ...

$table_id = $data->table_id;

// Check Database rules for this table
$rule_check = $conn->query("SELECT exclusive_stall_id FROM physical_tables WHERE table_number = '$table_id'");
$rule = $rule_check->fetch_assoc();

if ($rule && $rule['exclusive_stall_id'] != NULL) {
    // This is a private table. Check if any item in the cart is from a DIFFERENT stall
    foreach($data->items as $item) {
        if ($item->stallId != $rule['exclusive_stall_id']) {
            echo json_encode([
                "success" => false, 
                "message" => "Security Violation: You cannot order from Stall #{$item->stallId} while sitting at this table."
            ]);
            exit;
        }
    }
}

// ... Proceed to save order ...