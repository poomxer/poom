<?php 
session_start();

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ | ด้วงกว่าง พาเพลิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
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
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(93, 122, 84, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        header h1 {
            color: white;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn:hover {
            background: white;
            color: #5d7a54;
        }

        .user-info {
            background: linear-gradient(to bottom, #ffffff, #faf8f4);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #d4c9b0;
        }

        .user-info h2 {
            color: #5d7a54;
            margin-bottom: 10px;
        }

        .user-info p {
            color: #4a4a3d;
            margin-bottom: 5px;
        }

        .orders-container {
            display: grid;
            gap: 20px;
        }

        .order-card {
            background: linear-gradient(to bottom, #ffffff, #faf8f4);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border: 2px solid #d4c9b0;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(122, 152, 113, 0.2);
            border-color: #7a9871;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #d4c9b0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-number {
            font-size: 1.2rem;
            font-weight: 600;
            color: #5d7a54;
        }

        .order-date {
            color: #6d5947;
            font-size: 0.95rem;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
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

        .status-cancelled {
            background: #f8d7da;
            color: #842029;
        }

        .order-items {
            margin: 15px 0;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 10px;
            background: #f5f1e8;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid #d4c9b0;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #3d3d3d;
            margin-bottom: 5px;
        }

        .item-quantity {
            color: #6d5947;
            font-size: 0.9rem;
        }

        .item-price {
            color: #c67b5c;
            font-weight: 600;
        }

        .order-summary {
            display: grid;
            gap: 10px;
            padding: 15px;
            background: linear-gradient(135deg, #7a9871 0%, #5d7a54 100%);
            border-radius: 10px;
            color: white;
            margin-top: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
        }

        .summary-row.total {
            font-size: 1.3rem;
            font-weight: bold;
            padding-top: 10px;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
        }

        .order-info {
            margin-top: 15px;
            padding: 15px;
            background: #f5f1e8;
            border-radius: 10px;
            border: 1px solid #d4c9b0;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .info-label {
            font-weight: 600;
            color: #4a4a3d;
            min-width: 120px;
        }

        .info-value {
            color: #6d5947;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(to bottom, #ffffff, #faf8f4);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #d4c9b0;
        }

        .empty-state i {
            font-size: 5rem;
            color: #d4c9b0;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            color: #6d5947;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #8b7355;
        }

        .shop-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .shop-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 152, 113, 0.4);
        }

        .loading {
            text-align: center;
            padding: 60px 20px;
        }

        .loading i {
            font-size: 3rem;
            color: #7a9871;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
            }

            header h1 {
                font-size: 1.4rem;
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-item {
                flex-direction: column;
            }

            .item-image {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>
                <i class="fas fa-history"></i> ประวัติการสั่งซื้อ
            </h1>
            <div class="header-buttons">
                <a href="index.php" class="btn">
                    <i class="fas fa-home"></i> หน้าแรก
                </a>
                <a href="checkout.php" class="btn">
                    <i class="fas fa-shopping-cart"></i> ตะกร้า
                </a>
            </div>
        </header>

        <div class="user-info">
            <h2><i class="fas fa-user-circle"></i> บัญชีของคุณ</h2>
            <p><strong>ชื่อ:</strong> <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'ผู้ใช้'); ?></p>
            <p><strong>อีเมล:</strong> <?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : (isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '-'); ?></p>
        </div>

        <div id="ordersContainer">
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>กำลังโหลดข้อมูล...</p>
            </div>
        </div>
    </div>

    <script>
        async function loadOrders() {
            try {
                const response = await fetch('orderapi.php?action=get_user_orders');
                const data = await response.json();
                
                const container = document.getElementById('ordersContainer');
                
                if (data.success && data.orders.length > 0) {
                    let html = '<div class="orders-container">';
                    
                    data.orders.forEach(order => {
                        const statusClass = `status-${order.order_status}`;
                        const statusText = {
                            'pending': 'รอดำเนินการ',
                            'processing': 'กำลังจัดเตรียม',
                            'completed': 'เสร็จสิ้น',
                            'cancelled': 'ยกเลิก'
                        }[order.order_status] || order.order_status;
                        
                        const date = new Date(order.created_at);
                        const dateStr = date.toLocaleDateString('th-TH', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        
                        html += `
                            <div class="order-card">
                                <div class="order-header">
                                    <div>
                                        <div class="order-number">
                                            <i class="fas fa-receipt"></i> ${order.order_number}
                                        </div>
                                        <div class="order-date">
                                            <i class="far fa-calendar"></i> ${dateStr}
                                        </div>
                                    </div>
                                    <div class="order-status ${statusClass}">
                                        ${statusText}
                                    </div>
                                </div>
                                
                                <div class="order-items">
                                    ${order.items.map(item => `
                                        <div class="order-item">
                                            <img src="${item.image}" class="item-image" onerror="this.src='https://via.placeholder.com/80x80?text=สินค้า'">
                                            <div class="item-details">
                                                <div class="item-name">${item.name}</div>
                                                <div class="item-quantity">จำนวน: ${item.quantity} ชิ้น</div>
                                                <div class="item-price">฿${item.price.toLocaleString()}</div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                                
                                <div class="order-info">
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-user"></i> ผู้รับ:</span>
                                        <span class="info-value">${order.customer_name}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-phone"></i> โทร:</span>
                                        <span class="info-value">${order.customer_phone}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-map-marker-alt"></i> ที่อยู่:</span>
                                        <span class="info-value">${order.shipping_address}, ${order.province}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="fas fa-credit-card"></i> ชำระเงิน:</span>
                                        <span class="info-value">${order.payment_method}</span>
                                    </div>
                                </div>
                                
                                <div class="order-summary">
                                    <div class="summary-row">
                                        <span>ราคาสินค้า:</span>
                                        <span>฿${order.subtotal.toLocaleString()}</span>
                                    </div>
                                    <div class="summary-row">
                                        <span>ค่าจัดส่ง:</span>
                                        <span>฿${order.shipping_fee.toLocaleString()}</span>
                                    </div>
                                    <div class="summary-row total">
                                        <span>ยอดรวมทั้งหมด:</span>
                                        <span>฿${order.total_amount.toLocaleString()}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h2>ยังไม่มีประวัติการสั่งซื้อ</h2>
                            <p>คุณยังไม่เคยสั่งซื้อสินค้ากับเรา</p>
                            <a href="index.php" class="shop-btn">
                                <i class="fas fa-shopping-bag"></i> เริ่มช้อปปิ้ง
                            </a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                document.getElementById('ordersContainer').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h2>เกิดข้อผิดพลาด</h2>
                        <p>ไม่สามารถโหลดข้อมูลได้ กรุณาลองใหม่อีกครั้ง</p>
                    </div>
                `;
            }
        }

        // โหลดข้อมูลเมื่อเปิดหน้า
        window.addEventListener('DOMContentLoaded', loadOrders);
    </script>
</body>
</html>