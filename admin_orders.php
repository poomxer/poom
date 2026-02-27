<?php
session_start();
include 'connect.php';

// ตรวจสอบว่าเป็น admin หรือไม่
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// เก็บข้อมูล admin
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// จัดการ action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $new_status = $_POST['status'];
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $order_id);
        
        if ($stmt->execute()) {
            $success_message = "อัพเดทสถานะสำเร็จ";
        }
    }
}

// ดึงข้อมูลคำสั่งซื้อทั้งหมด
$filter_status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM orders WHERE 1=1";
$params = [];
$types = "";

if ($filter_status !== 'all') {
    $sql .= " AND order_status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (order_number LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ | Admin</title>
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
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(93, 122, 84, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 2rem;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: white;
            color: #5d7a54;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
        }

        .filters {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #d4c9b0;
        }

        .filters-row {
            display: grid;
            grid-template-columns: 1fr 1fr 200px;
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            color: #4a4a3d;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .filter-group select,
        .filter-group input {
            padding: 12px;
            border: 2px solid #d4c9b0;
            border-radius: 8px;
            font-size: 0.95rem;
            background: #f5f1e8;
            transition: all 0.3s;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #7a9871;
            background: white;
        }

        .btn-filter {
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(122, 152, 113, 0.4);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #d4c9b0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(122, 152, 113, 0.2);
        }

        .stat-card h3 {
            color: #6d5947;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #5d7a54;
        }

        .orders-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: 2px solid #d4c9b0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f5f1e8;
        }

        th {
            padding: 15px;
            text-align: left;
            color: #5d7a54;
            font-weight: 600;
            border-bottom: 2px solid #d4c9b0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #d4c9b0;
            color: #3d3d3d;
        }

        tr:hover {
            background: #faf8f4;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
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

        .status-cancelled {
            background: #f8d7da;
            color: #842029;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
        }

        .btn-view {
            background: #7a9871;
            color: white;
        }

        .btn-edit {
            background: #8b7355;
            color: white;
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
            padding: 30px;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #5d7a54;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6d5947;
        }

        .success-message {
            background: #d1e7dd;
            color: #0f5132;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .filters-row {
                grid-template-columns: 1fr;
            }

            .orders-table {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-clipboard-list"></i>
                จัดการคำสั่งซื้อ
            </h1>
            <div class="header-actions">
                <span style="margin-right: 15px; opacity: 0.9;">
                    <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($admin_name); ?>
                    <?php if ($admin_role === 'super_admin'): ?>
                        <span style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 10px; font-size: 0.8rem; margin-left: 5px;">
                            Super Admin
                        </span>
                    <?php endif; ?>
                </span>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> กลับหน้าหลัก
                </a>
                <a href="admin_logout.php" class="btn btn-primary">
                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                </a>
            </div>
        </div>

        <?php if (isset($success_message)): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <div class="stats">
            <?php
            $stats_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(total_amount) as total_sales
                FROM orders";
            $stats_result = $conn->query($stats_sql);
            $stats = $stats_result->fetch_assoc();
            ?>
            <div class="stat-card">
                <h3><i class="fas fa-shopping-cart"></i> คำสั่งซื้อทั้งหมด</h3>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-clock"></i> รอดำเนินการ</h3>
                <div class="stat-value"><?php echo number_format($stats['pending']); ?></div>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-spinner"></i> กำลังจัดเตรียม</h3>
                <div class="stat-value"><?php echo number_format($stats['processing']); ?></div>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-check-circle"></i> สำเร็จแล้ว</h3>
                <div class="stat-value"><?php echo number_format($stats['completed']); ?></div>
            </div>
        </div>

        <form class="filters" method="GET">
            <div class="filters-row">
                <div class="filter-group">
                    <label><i class="fas fa-filter"></i> กรองตามสถานะ</label>
                    <select name="status">
                        <option value="all" <?php echo $filter_status === 'all' ? 'selected' : ''; ?>>ทั้งหมด</option>
                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>รอดำเนินการ</option>
                        <option value="processing" <?php echo $filter_status === 'processing' ? 'selected' : ''; ?>>กำลังจัดเตรียม</option>
                        <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                        <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>ยกเลิก</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> ค้นหา</label>
                    <input type="text" name="search" placeholder="เลขที่คำสั่งซื้อ, ชื่อลูกค้า, เบอร์โทร" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </form>

        <div class="orders-table">
            <table>
                <thead>
                    <tr>
                        <th>เลขที่คำสั่งซื้อ</th>
                        <th>วันที่</th>
                        <th>ลูกค้า</th>
                        <th>ยอดรวม</th>
                        <th>สถานะ</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                <small><?php echo htmlspecialchars($order['customer_phone']); ?></small>
                            </td>
                            <td><strong>฿<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['order_status']; ?>">
                                    <?php 
                                    $status_text = [
                                        'pending' => 'รอดำเนินการ',
                                        'processing' => 'กำลังจัดเตรียม',
                                        'completed' => 'เสร็จสิ้น',
                                        'cancelled' => 'ยกเลิก'
                                    ];
                                    echo $status_text[$order['order_status']] ?? $order['order_status'];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-small btn-view" onclick="viewOrder(<?php echo $order['order_id']; ?>)">
                                        <i class="fas fa-eye"></i> ดู
                                    </button>
                                    <button class="btn btn-small btn-edit" onclick="editOrder(<?php echo $order['order_id']; ?>, '<?php echo $order['order_status']; ?>')">
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px;">
                                <i class="fas fa-inbox" style="font-size: 3rem; color: #d4c9b0; margin-bottom: 10px;"></i>
                                <p>ไม่พบคำสั่งซื้อ</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal สำหรับแก้ไขสถานะ -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> แก้ไขสถานะคำสั่งซื้อ</h2>
                <button class="close-modal" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="order_id" id="edit_order_id">
                <div class="filter-group" style="margin-bottom: 20px;">
                    <label>เลือกสถานะใหม่</label>
                    <select name="status" id="edit_status" required>
                        <option value="pending">รอดำเนินการ</option>
                        <option value="processing">กำลังจัดเตรียม</option>
                        <option value="completed">เสร็จสิ้น</option>
                        <option value="cancelled">ยกเลิก</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter" style="width: 100%;">
                    <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                </button>
            </form>
        </div>
    </div>

    <script>
        function viewOrder(orderId) {
            window.location.href = 'order_success.php?order_id=' + orderId;
        }

        function editOrder(orderId, currentStatus) {
            document.getElementById('edit_order_id').value = orderId;
            document.getElementById('edit_status').value = currentStatus;
            document.getElementById('editModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // ปิด modal เมื่อคลิกนอก content
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>