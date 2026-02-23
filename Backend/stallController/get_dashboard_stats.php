<?php
// Backend/stallController/get_dashboard_stats.php

// 1. Setup Error Handling
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$response = [];

try {
    // 2. Connect to Database
    $db_path = '../DBMSConector/db_connect.php';
    if (!file_exists($db_path)) {
        throw new Exception("Database file not found.");
    }
    include $db_path;

    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database Connection Failed.");
    }

    // 3. Validate Input
    $stall_id = isset($_GET['stall_id']) ? intval($_GET['stall_id']) : 0;
    if ($stall_id <= 0) {
        throw new Exception("Invalid Stall ID provided.");
    }

    // --- QUERY 1: METRICS ---
    $sql_metrics = "SELECT 
        COALESCE(SUM(price * quantity), 0) as total_revenue,
        COUNT(DISTINCT order_id) as total_orders,
        COALESCE(SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END), 0) as pending_count
    FROM order_items 
    WHERE stall_id = ?";
    
    $stmt = $conn->prepare($sql_metrics);
    if (!$stmt) throw new Exception("Metrics Query Error: " . $conn->error);
    
    $stmt->bind_param("i", $stall_id);
    if (!$stmt->execute()) throw new Exception("Metrics Execution Failed: " . $stmt->error);
    
    $metrics = $stmt->get_result()->fetch_assoc();
    
    $response['revenue'] = (float)($metrics['total_revenue'] ?? 0);
    $response['orders']  = (int)($metrics['total_orders'] ?? 0);
    $response['pending'] = (int)($metrics['pending_count'] ?? 0);
    $stmt->close();

    // --- QUERY 2: TOP SELLING ITEMS ---
    $sql_top = "SELECT item_name, 
                       SUM(quantity) as sold_count, 
                       SUM(price * quantity) as earnings 
                FROM order_items 
                WHERE stall_id = ? 
                GROUP BY item_name 
                ORDER BY sold_count DESC 
                LIMIT 3";
    
    $stmt = $conn->prepare($sql_top);
    if (!$stmt) throw new Exception("Top Items Query Error: " . $conn->error);

    $stmt->bind_param("i", $stall_id);
    $stmt->execute();
    $result_top = $stmt->get_result();
    
    $top_items = [];
    while($row = $result_top->fetch_assoc()) { 
        $top_items[] = $row; 
    }
    $response['top_items'] = $top_items;
    $stmt->close();

    // --- QUERY 3: HOURLY CHART (FIXED FOR STRICT MODE) ---
    // Changes: 
    // 1. Added DATE_FORMAT(...) to GROUP BY
    // 2. Changed ORDER BY to MIN(o.created_at)
    $sql_chart = "SELECT DATE_FORMAT(o.created_at, '%l %p') as hour_label, 
                         SUM(oi.price * oi.quantity) as hourly_total
                  FROM order_items oi
                  JOIN orders o ON oi.order_id = o.id
                  WHERE oi.stall_id = ? 
                  AND DATE(o.created_at) = CURDATE()
                  GROUP BY HOUR(o.created_at), DATE_FORMAT(o.created_at, '%l %p')
                  ORDER BY MIN(o.created_at) ASC";

    $stmt = $conn->prepare($sql_chart);
    if (!$stmt) throw new Exception("Chart Query Error: " . $conn->error);

    $stmt->bind_param("i", $stall_id);
    $stmt->execute();
    $result_chart = $stmt->get_result();

    $chart_labels = [];
    $chart_data = [];
    
    while($row = $result_chart->fetch_assoc()) {
        $chart_labels[] = $row['hour_label'];
        $chart_data[] = (float)$row['hourly_total'];
    }
    $response['chart'] = ['labels' => $chart_labels, 'data' => $chart_data];
    $stmt->close();

    // 4. Output JSON
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode([
        "error" => true, 
        "message" => $e->getMessage()
    ]);
}

if (isset($conn)) $conn->close();
?>