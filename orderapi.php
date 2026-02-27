<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'create';

switch($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            $user_id = $_SESSION['user_id'] ?? null;
            $order_number = $data['orderNumber'];
            $customer = $data['customer'];
            $payment = $data['payment'];
            $subtotal = $data['subtotal'];
            $shipping = $data['shipping'];
            $total = $data['total'];
            $note = $data['note'] ?? '';
            $items = $data['items'];
            
            $conn->begin_transaction();
            
            try {
                // ใช้ชื่อคอลัมน์ที่ถูกต้องตามฐานข้อมูล
                $sql = "INSERT INTO orders (user_id, order_number, customer_name, customer_phone, 
                        customer_address, shipping_province, payment_method, subtotal, shipping_fee, total_amount, notes) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssssddds", 
                    $user_id, 
                    $order_number, 
                    $customer['name'], 
                    $customer['phone'], 
                    $customer['address'], 
                    $customer['province'], 
                    $payment, 
                    $subtotal, 
                    $shipping, 
                    $total, 
                    $note
                );
                $stmt->execute();
                $order_id = $conn->insert_id;
                
                // บันทึกรายการสินค้า - ไม่ใช้คอลัมน์ image
                $item_sql = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) 
                             VALUES (?, ?, ?, ?, ?, ?)";
                $item_stmt = $conn->prepare($item_sql);
                
                foreach ($items as $item) {
                    $item_subtotal = $item['price'] * $item['quantity'];
                    $item_stmt->bind_param("issdid", 
                        $order_id, 
                        $item['id'], 
                        $item['name'], 
                        $item['price'], 
                        $item['quantity'], 
                        $item_subtotal
                    );
                    $item_stmt->execute();
                }
                
                if ($user_id) {
                    $clear_cart = "DELETE FROM cart WHERE user_id = ?";
                    $stmt_clear = $conn->prepare($clear_cart);
                    $stmt_clear->bind_param("i", $user_id);
                    $stmt_clear->execute();
                }
                
                $conn->commit();
                echo json_encode(['success' => true, 'order_id' => $order_id, 'message' => 'Order created successfully']);
                
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
        break;
        
    case 'get_user_orders':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in', 'orders' => []]);
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        
        // ใช้ชื่อคอลัมน์ที่ถูกต้อง
        $sql = "SELECT o.order_id, o.order_number, o.customer_name, o.customer_phone, 
                o.customer_address, o.shipping_province as province, o.payment_method,
                o.subtotal, o.shipping_fee, o.total_amount, o.order_status, o.created_at
                FROM orders o
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $order_id = $row['order_id'];
            
            // ดึงรายการสินค้าแยก
            $item_sql = "SELECT product_name, quantity, price FROM order_items WHERE order_id = ?";
            $item_stmt = $conn->prepare($item_sql);
            $item_stmt->bind_param("i", $order_id);
            $item_stmt->execute();
            $item_result = $item_stmt->get_result();
            
            $items = [];
            $calculated_subtotal = 0;
            while ($item = $item_result->fetch_assoc()) {
                $calculated_subtotal += $item['price'] * $item['quantity'];
                $items[] = [
                    'name' => $item['product_name'],
                    'quantity' => intval($item['quantity']),
                    'price' => floatval($item['price']),
                    'image' => 'img/placeholder.jpg'
                ];
            }
            
            // คำนวณใหม่ถ้าข้อมูลในฐานข้อมูลเป็น 0
            $subtotal = floatval($row['subtotal']);
            $shipping_fee = floatval($row['shipping_fee']);
            $total_amount = floatval($row['total_amount']);
            
            if ($subtotal == 0 && $calculated_subtotal > 0) {
                $subtotal = $calculated_subtotal;
                $shipping_fee = ($subtotal >= 1000) ? 0 : 50;
                $total_amount = $subtotal + $shipping_fee;
            }
            
            $orders[] = [
                'order_id' => $row['order_id'],
                'order_number' => $row['order_number'],
                'customer_name' => $row['customer_name'],
                'customer_phone' => $row['customer_phone'],
                'shipping_address' => $row['customer_address'],
                'province' => $row['province'],
                'payment_method' => $row['payment_method'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping_fee,
                'total_amount' => $total_amount,
                'order_status' => $row['order_status'],
                'created_at' => $row['created_at'],
                'items' => $items
            ];
        }
        
        echo json_encode(['success' => true, 'orders' => $orders]);
        break;
        
    case 'get_order_detail':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit;
        }
        
        $order_id = $_GET['order_id'] ?? 0;
        $user_id = $_SESSION['user_id'];
        
        $sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }
        
        $order = $result->fetch_assoc();
        
        $item_sql = "SELECT * FROM order_items WHERE order_id = ?";
        $item_stmt = $conn->prepare($item_sql);
        $item_stmt->bind_param("i", $order_id);
        $item_stmt->execute();
        $item_result = $item_stmt->get_result();
        
        $items = [];
        while ($item = $item_result->fetch_assoc()) {
            $items[] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'price' => floatval($item['price']),
                'quantity' => intval($item['quantity']),
                'subtotal' => floatval($item['subtotal'])
            ];
        }
        
        $order['items'] = $items;
        
        echo json_encode(['success' => true, 'order' => $order]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>