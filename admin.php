<?php 
session_start();

// ตรวจสอบการ login (สามารถปรับแต่งได้ตามระบบของคุณ)
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: admin_login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ร้านด้วงกว่าง พาเพลิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 25%, #a5d6a7 50%, #c8e6c9 75%, #e8f5e9 100%);
            background-size: 400% 400%;
            animation: forestGradient 20s ease infinite;
            color: #2c3e50;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 15% 25%, rgba(129, 199, 132, 0.15) 0%, transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(102, 187, 106, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 45% 85%, rgba(139, 195, 74, 0.1) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes forestGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            border-radius: 20px;
            margin-bottom: 2rem;
        }

        header h1 {
            font-size: 2.5rem;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        .nav-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .tab-btn {
            flex: 1;
            min-width: 200px;
            padding: 1rem 2rem;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .tab-btn:hover {
            background: #f0f4ff;
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: #667eea;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border: 2px solid #e2e8f0;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .stat-card.purple .icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stat-card.green .icon {
            background: linear-gradient(135deg, #56ab2f, #a8e063);
            color: white;
        }

        .stat-card.orange .icon {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .stat-card.blue .icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }

        .stat-card h3 {
            color: #718096;
            font-size: 0.95rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
        }

        .content-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #e2e8f0;
        }

        .content-card h2 {
            color: #667eea;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #56ab2f, #a8e063);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            font-weight: 600;
            font-size: 0.95rem;
        }

        tbody tr {
            transition: all 0.2s;
        }

        tbody tr:hover {
            background: #f7fafc;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
        }

        .status {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status.cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            color: #667eea;
            font-size: 1.5rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #718096;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            header h1 {
                font-size: 1.8rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.85rem;
            }

            th, td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <button class="btn btn-danger logout-btn" onclick="logout()">
        <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
    </button>

    <div class="container">
        <header>
            <h1><i class="fas fa-crown"></i> Admin Dashboard</h1>
            <p>ระบบจัดการร้านด้วงกว่าง พาเพลิน</p>
        </header>

        <!-- สถิติ -->
        <div class="stats-grid">
            <div class="stat-card purple">
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                <h3>คำสั่งซื้อวันนี้</h3>
                <div class="value">15</div>
            </div>
            <div class="stat-card green">
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                <h3>ยอดขายวันนี้</h3>
                <div class="value">12,450 ฿</div>
            </div>
            <div class="stat-card orange">
                <div class="icon"><i class="fas fa-box"></i></div>
                <h3>สินค้าทั้งหมด</h3>
                <div class="value">13</div>
            </div>
            <div class="stat-card blue">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>ลูกค้าทั้งหมด</h3>
                <div class="value">127</div>
            </div>
        </div>

        <!-- แท็บเมนู -->
        <div class="nav-tabs">
            <button class="tab-btn active" onclick="showTab('orders')">
                <i class="fas fa-list"></i> คำสั่งซื้อ
            </button>
            <button class="tab-btn" onclick="showTab('products')">
                <i class="fas fa-box"></i> จัดการสินค้า
            </button>
            <button class="tab-btn" onclick="showTab('customers')">
                <i class="fas fa-users"></i> ลูกค้า
            </button>
            <button class="tab-btn" onclick="window.location.href='manage_addresses.php'">
                <i class="fas fa-map-marker-alt"></i> จัดการที่อยู่
            </button>
        </div>

        <!-- คำสั่งซื้อ -->
        <div id="orders" class="tab-content active">
            <div class="content-card">
                <h2><i class="fas fa-shopping-cart"></i> คำสั่งซื้อทั้งหมด</h2>
                <table>
                    <thead>
                        <tr>
                            <th>รหัสออเดอร์</th>
                            <th>ลูกค้า</th>
                            <th>สินค้า</th>
                            <th>ยอดรวม</th>
                            <th>สถานะ</th>
                            <th>วันที่</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#ORD001</td>
                            <td>สมชาย ใจดี</td>
                            <td>ด้วงกว่างแอตลาส</td>
                            <td>950 ฿</td>
                            <td><span class="status pending">รอดำเนินการ</span></td>
                            <td>02/02/2026</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-success" onclick="updateOrderStatus('ORD001', 'completed')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="updateOrderStatus('ORD001', 'cancelled')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD002</td>
                            <td>สมหญิง รักดี</td>
                            <td>ด้วงกว่างคอเคซัส</td>
                            <td>1,050 ฿</td>
                            <td><span class="status completed">เสร็จสิ้น</span></td>
                            <td>02/02/2026</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-secondary">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>#ORD003</td>
                            <td>วิชัย มั่นคง</td>
                            <td>ด้วงกว่างเฮอร์คิวลิส</td>
                            <td>1,500 ฿</td>
                            <td><span class="status pending">รอดำเนินการ</span></td>
                            <td>01/02/2026</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-success" onclick="updateOrderStatus('ORD003', 'completed')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="updateOrderStatus('ORD003', 'cancelled')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- จัดการสินค้า -->
        <div id="products" class="tab-content">
            <div class="content-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2><i class="fas fa-box"></i> จัดการสินค้า</h2>
                    <button class="btn btn-primary" onclick="openAddProductModal()">
                        <i class="fas fa-plus"></i> เพิ่มสินค้าใหม่
                    </button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>รูปภาพ</th>
                            <th>ชื่อสินค้า</th>
                            <th>ราคา</th>
                            <th>สต็อก</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><img src="img/1.jpg" alt="Product" class="product-img"></td>
                            <td>ด้วงกว่างแอตลาส</td>
                            <td>950 ฿</td>
                            <td>20 ตัว</td>
                            <td><span class="status completed">พร้อมขาย</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-primary" onclick="editProduct('p1')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteProduct('p1')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="img/2.jpg" alt="Product" class="product-img"></td>
                            <td>ด้วงกว่างแอตลาส (ตัวใหญ่)</td>
                            <td>1,050 ฿</td>
                            <td>18 ตัว</td>
                            <td><span class="status completed">พร้อมขาย</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-primary" onclick="editProduct('p2')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteProduct('p2')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><img src="img/3.jpg" alt="Product" class="product-img"></td>
                            <td>ด้วงกว่างคอเคซัส</td>
                            <td>1,050 ฿</td>
                            <td>30 ตัว</td>
                            <td><span class="status completed">พร้อมขาย</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-primary" onclick="editProduct('p3')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteProduct('p3')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ลูกค้า -->
        <div id="customers" class="tab-content">
            <div class="content-card">
                <h2><i class="fas fa-users"></i> รายชื่อลูกค้า</h2>
                <table>
                    <thead>
                        <tr>
                            <th>รหัสลูกค้า</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>อีเมล</th>
                            <th>เบอร์โทร</th>
                            <th>ยอดซื้อรวม</th>
                            <th>วันที่สมัคร</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#C001</td>
                            <td>สมชาย ใจดี</td>
                            <td>somchai@email.com</td>
                            <td>081-234-5678</td>
                            <td>3,450 ฿</td>
                            <td>15/01/2026</td>
                        </tr>
                        <tr>
                            <td>#C002</td>
                            <td>สมหญิง รักดี</td>
                            <td>somying@email.com</td>
                            <td>082-345-6789</td>
                            <td>2,100 ฿</td>
                            <td>20/01/2026</td>
                        </tr>
                        <tr>
                            <td>#C003</td>
                            <td>วิชัย มั่นคง</td>
                            <td>wichai@email.com</td>
                            <td>083-456-7890</td>
                            <td>5,250 ฿</td>
                            <td>10/01/2026</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal เพิ่ม/แก้ไขสินค้า -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-box"></i> <span id="modalTitle">เพิ่มสินค้าใหม่</span></h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="productForm">
                <div class="form-group">
                    <label>ชื่อสินค้า</label>
                    <input type="text" id="productName" placeholder="กรอกชื่อสินค้า" required>
                </div>
                <div class="form-group">
                    <label>ราคา (บาท)</label>
                    <input type="number" id="productPrice" placeholder="กรอกราคา" required>
                </div>
                <div class="form-group">
                    <label>จำนวนสต็อก</label>
                    <input type="number" id="productStock" placeholder="กรอกจำนวน" required>
                </div>
                <div class="form-group">
                    <label>รายละเอียด</label>
                    <textarea id="productDesc" placeholder="กรอกรายละเอียดสินค้า"></textarea>
                </div>
                <div class="form-group">
                    <label>รูปภาพ</label>
                    <input type="file" id="productImage" accept="image/*">
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // แสดง/ซ่อน Tab
        function showTab(tabName) {
            // ซ่อนทุก tab
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // ลบ active จากปุ่มทั้งหมด
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // แสดง tab ที่เลือก
            document.getElementById(tabName).classList.add('active');
            
            // เพิ่ม active ให้ปุ่มที่กด
            event.target.classList.add('active');
        }

        // เปิด Modal เพิ่มสินค้า
        function openAddProductModal() {
            document.getElementById('modalTitle').textContent = 'เพิ่มสินค้าใหม่';
            document.getElementById('productForm').reset();
            document.getElementById('productModal').classList.add('active');
        }

        // แก้ไขสินค้า
        function editProduct(productId) {
            document.getElementById('modalTitle').textContent = 'แก้ไขสินค้า';
            // โหลดข้อมูลสินค้า (ตัวอย่าง)
            document.getElementById('productName').value = 'ด้วงกว่างแอตลาส';
            document.getElementById('productPrice').value = '950';
            document.getElementById('productStock').value = '20';
            document.getElementById('productModal').classList.add('active');
        }

        // ลบสินค้า
        function deleteProduct(productId) {
            if (confirm('คุณต้องการลบสินค้านี้ใช่หรือไม่?')) {
                alert('ลบสินค้า ' + productId + ' แล้ว');
                // เพิ่มโค้ดลบสินค้าจริงที่นี่
            }
        }

        // ปิด Modal
        function closeModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        // ส่งฟอร์มสินค้า
        document.getElementById('productForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('บันทึกข้อมูลสินค้าแล้ว!');
            closeModal();
        });

        // อัพเดทสถานะคำสั่งซื้อ
        function updateOrderStatus(orderId, status) {
            const statusText = status === 'completed' ? 'เสร็จสิ้น' : 'ยกเลิก';
            if (confirm(`ต้องการเปลี่ยนสถานะเป็น "${statusText}" ใช่หรือไม่?`)) {
                alert('อัพเดทสถานะคำสั่งซื้อ ' + orderId + ' แล้ว');
                // เพิ่มโค้ดอัพเดทสถานะจริงที่นี่
            }
        }

        // ออกจากระบบ
        function logout() {
            if (confirm('ต้องการออกจากระบบใช่หรือไม่?')) {
                window.location.href = 'index.php';
            }
        }

        // ปิด modal เมื่อคลิกนอก modal
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>