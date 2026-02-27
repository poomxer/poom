<?php
session_start();
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT a.*, u.username, u.email 
            FROM addresses a 
            LEFT JOIN users u ON a.user_id = u.id 
            WHERE a.id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'success' => true,
            'address' => $row
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบข้อมูล'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่มีข้อมูล ID'
    ]);
}
?>