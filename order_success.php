<?php
session_start();
include 'connect.php';

// ตรวจสอบว่าล็อกอินหรือไม่ (รองรับทั้ง user และ admin)
$is_customer = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['admin_id']);

if (!$is_customer && !$is_admin) {
    header('Location: login.php');
    exit();
}

// รับ order_id จาก URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
    header('Location: index.php');
    exit();
}

// ดึงข้อมูลคำสั่งซื้อ
// ถ้าเป็น customer ต้องตรวจสอบว่าเป็นของเขาเท่านั้น
// ถ้าเป็น admin ดูได้ทุกคำสั่งซื้อ
if ($is_customer) {
    $sql_order = "SELECT order_id, user_id, order_number, customer_name, customer_phone, customer_address,
                  shipping_province, payment_method, subtotal, shipping_fee, 
                  total_amount, notes, order_status, created_at 
                  FROM orders WHERE order_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql_order);
    mysqli_stmt_bind_param($stmt, 'ii', $order_id, $_SESSION['user_id']);
} else {
    // Admin - ไม่ต้องตรวจสอบ user_id
    $sql_order = "SELECT order_id, user_id, order_number, customer_name, customer_phone, customer_address,
                  shipping_province, payment_method, subtotal, shipping_fee, 
                  total_amount, notes, order_status, created_at 
                  FROM orders WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql_order);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
}
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result($stmt, 
    $o_id, $o_user_id, $o_number, $o_name, $o_phone, $o_address,
    $o_province, $o_payment, $o_subtotal, $o_shipping,
    $o_total, $o_note, $o_status, $o_created
);

if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php');
    exit();
}

$order = [
    'order_id' => $o_id,
    'user_id' => $o_user_id,
    'order_number' => $o_number,
    'customer_name' => $o_name,
    'customer_phone' => $o_phone,
    'customer_address' => $o_address,
    'province' => $o_province,
    'payment_method' => $o_payment,
    'subtotal' => $o_subtotal,
    'shipping_fee' => $o_shipping,
    'total_amount' => $o_total,
    'notes' => $o_note,
    'order_status' => $o_status,
    'created_at' => $o_created
];

mysqli_stmt_close($stmt);

// ดึงรายการสินค้าในคำสั่งซื้อ - แก้ไขไม่ให้ SELECT คอลัมน์ image
$sql_items = "SELECT product_id, product_name, price, quantity, subtotal 
              FROM order_items WHERE order_id = ?";
$stmt_items = mysqli_prepare($conn, $sql_items);
mysqli_stmt_bind_param($stmt_items, 'i', $order_id);
mysqli_stmt_execute($stmt_items);

mysqli_stmt_bind_result($stmt_items, $i_product_id, $i_name, $i_price, $i_quantity, $i_subtotal);

$items = [];
$calculated_subtotal = 0;
while (mysqli_stmt_fetch($stmt_items)) {
    $item_subtotal = $i_price * $i_quantity;
    $calculated_subtotal += $item_subtotal;
    
    $items[] = [
        'product_id' => $i_product_id,
        'product_name' => $i_name,
        'price' => $i_price,
        'quantity' => $i_quantity,
        'subtotal' => $item_subtotal
    ];
}

mysqli_stmt_close($stmt_items);

// คำนวณค่าจัดส่ง (ถ้าในฐานข้อมูลเป็น 0 ให้ใช้ค่าที่คำนวณ)
if ($order['subtotal'] == 0) {
    $order['subtotal'] = $calculated_subtotal;
}

// ค่าจัดส่งเริ่มต้น 50 บาท (ถ้ายอดรวมน้อยกว่า 1000)
if ($order['shipping_fee'] == 0) {
    $order['shipping_fee'] = ($order['subtotal'] >= 1000) ? 0 : 50;
}

// คำนวณยอดรวมทั้งหมดใหม่
if ($order['total_amount'] == 0 || $order['total_amount'] != ($order['subtotal'] + $order['shipping_fee'])) {
    $order['total_amount'] = $order['subtotal'] + $order['shipping_fee'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งซื้อสำเร็จ | ด้วงกว่าง พาเพลิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f1e8 0%, #e8dcc4 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .success-header {
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            text-align: center;
            padding: 60px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(122, 152, 113, 0.3);
        }

        .success-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .success-header p {
            font-size: 1.2rem;
            opacity: 0.95;
        }

        .order-number {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px 30px;
            border-radius: 50px;
            display: inline-block;
            margin-top: 20px;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .order-details {
            background: linear-gradient(to bottom, #ffffff, #faf8f4);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: 2px solid #d4c9b0;
        }

        .section-title {
            color: #5d7a54;
            font-size: 1.5rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #d4c9b0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            padding: 15px;
            background: #f5f1e8;
            border-radius: 10px;
            border: 1px solid #d4c9b0;
        }

        .info-label {
            color: #6d5947;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .info-value {
            color: #3d3d3d;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #f5f1e8;
            padding: 15px;
            text-align: left;
            color: #5d7a54;
            font-weight: 600;
            border-bottom: 2px solid #d4c9b0;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #d4c9b0;
            color: #3d3d3d;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .total-section {
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .total-row.grand-total {
            font-size: 1.8rem;
            font-weight: 700;
            padding-top: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            margin-top: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(122, 152, 113, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #5d7a54;
            border: 2px solid #5d7a54;
        }

        .btn-secondary:hover {
            background: #5d7a54;
            color: white;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cfe2ff;
            color: #084298;
        }

        .status-completed {
            background: #d1e7dd;
            color: #0f5132;
        }

        .next-steps {
            background: linear-gradient(to bottom, #ffffff, #faf8f4);
            border-radius: 20px;
            padding: 30px;
            margin-top: 20px;
            border: 2px solid #d4c9b0;
        }

        .next-steps h3 {
            color: #5d7a54;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step {
            display: flex;
            align-items: start;
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f1e8;
            border-radius: 10px;
        }

        .step-number {
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .step-content h4 {
            color: #3d3d3d;
            margin-bottom: 5px;
        }

        .step-content p {
            color: #6d5947;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .success-header {
                padding: 40px 20px;
            }

            .success-header h1 {
                font-size: 2rem;
            }

            .order-details {
                padding: 25px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .items-table {
                font-size: 0.9rem;
            }

            .items-table th,
            .items-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>สั่งซื้อสำเร็จ!</h1>
            <p>ขอบคุณสำหรับการสั่งซื้อ</p>
            <div class="order-number">
                <i class="fas fa-receipt"></i> เลขที่คำสั่งซื้อ: <?php echo htmlspecialchars($order['order_number']); ?>
            </div>
        </div>

        <div class="order-details">
            <h2 class="section-title">
                <i class="fas fa-user"></i> ข้อมูลผู้รับสินค้า
            </h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-user"></i> ชื่อผู้รับ</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-phone"></i> เบอร์โทรศัพท์</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                </div>
                
                <div class="info-item" style="grid-column: span 2;">
                    <div class="info-label"><i class="fas fa-map-marker-alt"></i> ที่อยู่จัดส่ง</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['customer_address']); ?>, <?php echo htmlspecialchars($order['province']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-credit-card"></i> วิธีชำระเงิน</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['payment_method']); ?></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label"><i class="fas fa-tag"></i> สถานะ</div>
                    <div class="info-value">
                        <span class="status-badge status-<?php echo $order['order_status']; ?>">
                            <?php 
                            $status_text = [
                                'pending' => 'รอดำเนินการ',
                                'processing' => 'กำลังจัดเตรียม',
                                'completed' => 'เสร็จสิ้น'
                            ];
                            echo $status_text[$order['order_status']] ?? $order['order_status'];
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <h2 class="section-title">
                <i class="fas fa-shopping-bag"></i> รายการสินค้า
            </h2>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>สินค้า</th>
                        <th style="text-align: center;">จำนวน</th>
                        <th style="text-align: right;">ราคา/หน่วย</th>
                        <th style="text-align: right;">ราคารวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                        <td style="text-align: right;">฿<?php echo number_format($item['price'], 2); ?></td>
                        <td style="text-align: right;">฿<?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-section">
                <div class="total-row">
                    <span>ราคาสินค้า</span>
                    <span>฿<?php echo number_format($order['subtotal'], 2); ?></span>
                </div>
                <div class="total-row">
                    <span>ค่าจัดส่ง</span>
                    <span>฿<?php echo number_format($order['shipping_fee'], 2); ?></span>
                </div>
                <div class="total-row grand-total">
                    <span>ยอดรวมทั้งหมด</span>
                    <span>฿<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>

        <div class="next-steps">
            <h3><i class="fas fa-tasks"></i> ขั้นตอนถัดไป</h3>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>รอการยืนยันคำสั่งซื้อ</h4>
                    <p>ทางร้านจะตรวจสอบและยืนยันคำสั่งซื้อของคุณภายใน 24 ชั่วโมง</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>ชำระเงิน</h4>
                    <p>หลังจากได้รับการยืนยันคำสั่งซื้อ กรุณาชำระเงินตามวิธีที่เลือก</p>
                </div>
            </div>

            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>จัดส่งสินค้า</h4>
                    <p>หลังจากได้รับการชำระเงินแล้ว ทางร้านจะดำเนินการจัดส่งสินค้าให้คุณ</p>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="order_history.php" class="btn btn-secondary">
                <i class="fas fa-history"></i> ดูประวัติการสั่งซื้อ
            </a>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> กลับหน้าหลัก
            </a>
        </div>
    </div>
</body>
</html>