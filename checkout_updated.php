<?php
session_start();
require_once 'connect.php';

header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบว่ามีการ login หรือไม่
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อนทำการสั่งซื้อ'], JSON_UNESCAPED_UNICODE);
    exit;
}

// รับข้อมูลจาก POST
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

// ตรวจสอบข้อมูลที่จำเป็น
$required_fields = ['name', 'phone', 'address', 'payment_method', 'cart'];

foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            'success' => false, 
            'message' => 'กรุณากรอกข้อมูลให้ครบถ้วน: ' . $field
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// ตรวจสอบตะกร้าสินค้า
if (!is_array($data['cart']) || empty($data['cart'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'ตะกร้าสินค้าว่างเปล่า'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// เริ่ม transaction
mysqli_begin_transaction($conn);

try {
    $user_id = $_SESSION['user_id'];
    $customer_name = mysqli_real_escape_string($conn, $data['name']);
    $customer_phone = mysqli_real_escape_string($conn, $data['phone']);
    $customer_address = mysqli_real_escape_string($conn, $data['address']);
    $payment_method = mysqli_real_escape_string($conn, $data['payment_method']);
    $shipping_method = isset($data['shipping_method']) ? mysqli_real_escape_string($conn, $data['shipping_method']) : 'standard';
    $notes = isset($data['notes']) ? mysqli_real_escape_string($conn, $data['notes']) : '';
    
    // สร้าง order_number แบบไม่ซ้ำ
    $order_number = 'ORD' . date('YmdHis') . rand(100, 999);
    
    // คำนวณยอดรวม
    $total_amount = 0;
    foreach ($data['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // เพิ่มค่าจัดส่ง
    $shipping_fee = isset($data['shipping_fee']) ? floatval($data['shipping_fee']) : 0;
    $total_amount += $shipping_fee;
    
    // บันทึกคำสั่งซื้อ (เพิ่ม order_number)
    $sql_order = "INSERT INTO orders (
        user_id,
        order_number,
        customer_name, 
        customer_phone, 
        customer_address, 
        shipping_method, 
        payment_method, 
        total_amount, 
        status, 
        notes,
        order_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())";
    
    $stmt_order = mysqli_prepare($conn, $sql_order);
    mysqli_stmt_bind_param(
        $stmt_order, 
        'issssssds', 
        $user_id,
        $order_number,
        $customer_name, 
        $customer_phone, 
        $customer_address, 
        $shipping_method, 
        $payment_method, 
        $total_amount, 
        $notes
    );
    
    if (!mysqli_stmt_execute($stmt_order)) {
        throw new Exception('ไม่สามารถบันทึกคำสั่งซื้อได้: ' . mysqli_error($conn));
    }
    
    $order_id = mysqli_insert_id($conn);
    
    // บันทึกรายการสินค้า
    $sql_item = "INSERT INTO order_items (
        order_id,
        product_id, 
        product_name, 
        price, 
        quantity, 
        subtotal
    ) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt_item = mysqli_prepare($conn, $sql_item);
    
    foreach ($data['cart'] as $item) {
        $product_id = isset($item['id']) ? strval($item['id']) : '';
        $product_name = mysqli_real_escape_string($conn, $item['name']);
        $price = floatval($item['price']);
        $quantity = intval($item['quantity']);
        $subtotal = $price * $quantity;
        
        mysqli_stmt_bind_param(
            $stmt_item,
            'issdid',
            $order_id,
            $product_id,
            $product_name,
            $price,
            $quantity,
            $subtotal
        );
        
        if (!mysqli_stmt_execute($stmt_item)) {
            throw new Exception('ไม่สามารถบันทึกรายการสินค้าได้: ' . mysqli_error($conn));
        }
    }
    
    // ลบตะกร้าสินค้า
    $sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
    $stmt_clear = mysqli_prepare($conn, $sql_clear_cart);
    mysqli_stmt_bind_param($stmt_clear, 'i', $user_id);
    mysqli_stmt_execute($stmt_clear);
    
    // Commit transaction
    mysqli_commit($conn);
    
    // ส่งผลลัพธ์กลับ
    echo json_encode([
        'success' => true,
        'message' => 'สั่งซื้อสำเร็จ!',
        'order_id' => $order_id,
        'order_number' => $order_number,
        'total_amount' => $total_amount
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Rollback ถ้าเกิดข้อผิดพลาด
    mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

mysqli_close($conn);
?>