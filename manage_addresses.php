<?php 
session_start();
require_once 'config.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบการ login admin (ปรับตามระบบของคุณ)
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: admin_login.php');
//     exit();
// }

// ดึงข้อมูลที่อยู่ทั้งหมด
$sql = "SELECT a.*, u.username, u.email 
        FROM addresses a 
        LEFT JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการที่อยู่ - Admin</title>
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
                radial-gradient(circle at 85% 75%, rgba(102, 187, 106, 0.12) 0%, transparent 45%);
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

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            padding: 0.8rem 1.5rem;
            background: white;
            border: 2px solid #667eea;
            border-radius: 12px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-btn:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        .content-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #e2e8f0;
        }

        .search-box {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .search-box input {
            flex: 1;
            padding: 0.8rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Kanit', sans-serif;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
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

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-default {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-primary {
            background: #fef3c7;
            color: #92400e;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-view {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
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
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
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

        .address-detail {
            line-height: 1.8;
        }

        .address-detail strong {
            color: #667eea;
            display: inline-block;
            width: 120px;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #718096;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <a href="admin.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> กลับหน้า Admin
    </a>

    <div class="container">
        <header>
            <h1><i class="fas fa-map-marker-alt"></i> จัดการที่อยู่ลูกค้า</h1>
            <p>ดูและจัดการที่อยู่จัดส่งของลูกค้าทั้งหมด</p>
        </header>

        <div class="content-card">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="ค้นหาชื่อ, อีเมล, ที่อยู่..." onkeyup="searchAddress()">
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
            <table id="addressTable">
                <thead>
                    <tr>
                        <th>รหัส</th>
                        <th>ลูกค้า</th>
                        <th>ชื่อผู้รับ</th>
                        <th>เบอร์โทร</th>
                        <th>ที่อยู่</th>
                        <th>ประเภท</th>
                        <th>วันที่เพิ่ม</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <?php echo htmlspecialchars($row['username'] ?? 'ไม่มีข้อมูล'); ?><br>
                            <small style="color: #718096;"><?php echo htmlspecialchars($row['email'] ?? ''); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($row['recipient_name'] ?? $row['full_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                        <td>
                            <?php 
                            $address = htmlspecialchars($row['address'] ?? '');
                            echo mb_strlen($address) > 40 ? mb_substr($address, 0, 40) . '...' : $address;
                            ?>
                        </td>
                        <td>
                            <?php 
                            $is_default = $row['is_default'] ?? 0;
                            if ($is_default) {
                                echo '<span class="status-badge status-primary"><i class="fas fa-star"></i> ค่าเริ่มต้น</span>';
                            } else {
                                echo '<span class="status-badge status-default">ทั่วไป</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['created_at'] ?? 'now')); ?></td>
                        <td>
                            <button class="btn btn-view" onclick="viewAddress(<?php echo $row['id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-map-marker-alt"></i>
                <h3>ยังไม่มีข้อมูลที่อยู่</h3>
                <p>ยังไม่มีลูกค้าเพิ่มที่อยู่ในระบบ</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal แสดงรายละเอียด -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-map-marker-alt"></i> รายละเอียดที่อยู่</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div id="addressDetails" class="address-detail">
                <!-- จะแสดงข้อมูลที่นี่ -->
            </div>
        </div>
    </div>

    <script>
        // ค้นหาที่อยู่
        function searchAddress() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('addressTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                if (found) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }

        // ดูรายละเอียดที่อยู่
        function viewAddress(id) {
            // ดึงข้อมูลจาก API
            fetch('get_address.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const address = data.address;
                        document.getElementById('addressDetails').innerHTML = `
                            <p><strong>รหัสที่อยู่:</strong> #${String(address.id).padStart(4, '0')}</p>
                            <p><strong>ลูกค้า:</strong> ${address.username || 'ไม่มีข้อมูล'}</p>
                            <p><strong>อีเมล:</strong> ${address.email || 'ไม่มีข้อมูล'}</p>
                            <p><strong>ชื่อผู้รับ:</strong> ${address.recipient_name || address.full_name || 'N/A'}</p>
                            <p><strong>เบอร์โทร:</strong> ${address.phone || 'N/A'}</p>
                            <p><strong>ที่อยู่:</strong> ${address.address || 'N/A'}</p>
                            <p><strong>จังหวัด:</strong> ${address.province || 'N/A'}</p>
                            <p><strong>อำเภอ:</strong> ${address.district || 'N/A'}</p>
                            <p><strong>ตำบล:</strong> ${address.subdistrict || 'N/A'}</p>
                            <p><strong>รหัสไปรษณีย์:</strong> ${address.postal_code || 'N/A'}</p>
                            <p><strong>ประเภท:</strong> ${address.is_default == 1 ? '<span class="status-badge status-primary">ค่าเริ่มต้น</span>' : '<span class="status-badge status-default">ทั่วไป</span>'}</p>
                            <p><strong>วันที่เพิ่ม:</strong> ${new Date(address.created_at).toLocaleDateString('th-TH')}</p>
                        `;
                        document.getElementById('viewModal').classList.add('active');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('ไม่สามารถโหลดข้อมูลได้');
                });
        }

        // ปิด Modal
        function closeModal() {
            document.getElementById('viewModal').classList.remove('active');
        }

        // ปิด modal เมื่อคลิกนอก modal
        window.onclick = function(event) {
            const modal = document.getElementById('viewModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>