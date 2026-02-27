<?php
session_start();
include 'connect.php';

header('Content-Type: application/json');

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

$action = $_GET['action'] ?? '';

// ดึงข้อมูลตะกร้าจาก localStorage (สำหรับ sync)
function getLocalCart() {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    return $data['cart'] ?? [];
}

switch($action) {
    case 'get':
        // ดึงข้อมูลตะกร้าจากฐานข้อมูล
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $cart = [];
            while($row = $result->fetch_assoc()) {
                $cart[] = [
                    'id' => $row['product_id'],
                    'name' => $row['product_name'],
                    'price' => floatval($row['product_price']),
                    'image' => $row['product_image'],
                    'quantity' => intval($row['quantity'])
                ];
            }
            
            echo json_encode(['success' => true, 'cart' => $cart]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not logged in', 'cart' => []]);
        }
        break;
        
    case 'add':
        // เพิ่มสินค้าลงตะกร้า
        if (isLoggedIn()) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            $user_id = $_SESSION['user_id'];
            $product_id = $data['id'];
            $product_name = $data['name'];
            $product_price = $data['price'];
            $product_image = $data['image'];
            $quantity = $data['quantity'] ?? 1;
            
            // ตรวจสอบว่ามีสินค้านี้ในตะกร้าแล้วหรือไม่
            $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // ถ้ามีแล้ว เพิ่มจำนวน
                $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
            } else {
                // ถ้ายังไม่มี เพิ่มรายการใหม่
                $insert_sql = "INSERT INTO cart (user_id, product_id, product_name, product_price, product_image, quantity) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("iisdsi", $user_id, $product_id, $product_name, $product_price, $product_image, $quantity);
                $stmt->execute();
            }
            
            echo json_encode(['success' => true, 'message' => 'Added to cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
        }
        break;
        
    case 'update':
        // อัพเดทจำนวนสินค้า
        if (isLoggedIn()) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            $user_id = $_SESSION['user_id'];
            $product_id = $data['id'];
            $quantity = $data['quantity'];
            
            if ($quantity <= 0) {
                // ถ้าจำนวนเป็น 0 ให้ลบออก
                $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($delete_sql);
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
            } else {
                $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
            }
            
            echo json_encode(['success' => true, 'message' => 'Cart updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
        }
        break;
        
    case 'remove':
        // ลบสินค้าออกจากตะกร้า
        if (isLoggedIn()) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            $user_id = $_SESSION['user_id'];
            $product_id = $data['id'];
            
            $delete_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Item removed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
        }
        break;
        
    case 'clear':
        // ล้างตะกร้าทั้งหมด
        if (isLoggedIn()) {
            $user_id = $_SESSION['user_id'];
            
            $delete_sql = "DELETE FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
        }
        break;
        
    case 'sync':
        // Sync ตะกร้าจาก localStorage เข้าฐานข้อมูลเมื่อ login
        if (isLoggedIn()) {
            $localCart = getLocalCart();
            $user_id = $_SESSION['user_id'];
            
            foreach ($localCart as $item) {
                // ตรวจสอบว่ามีสินค้านี้ในตะกร้าแล้วหรือไม่
                $check_sql = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($check_sql);
                $stmt->bind_param("ii", $user_id, $item['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // ถ้ามีแล้ว รวมจำนวน
                    $row = $result->fetch_assoc();
                    $new_quantity = $row['quantity'] + $item['quantity'];
                    
                    $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                    $stmt = $conn->prepare($update_sql);
                    $stmt->bind_param("iii", $new_quantity, $user_id, $item['id']);
                    $stmt->execute();
                } else {
                    // ถ้ายังไม่มี เพิ่มรายการใหม่
                    $insert_sql = "INSERT INTO cart (user_id, product_id, product_name, product_price, product_image, quantity) 
                                   VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->bind_param("iisdsi", $user_id, $item['id'], $item['name'], $item['price'], $item['image'], $item['quantity']);
                    $stmt->execute();
                }
            }
            
            echo json_encode(['success' => true, 'message' => 'Cart synced']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>