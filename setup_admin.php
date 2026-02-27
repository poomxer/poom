<?php
/**
 * ไฟล์สำหรับสร้างบัญชีผู้ขาย
 * รันไฟล์นี้เพียงครั้งเดียว แล้วลบทิ้งทันที!
 */

include 'connect.php';

echo "<!DOCTYPE html>
<html lang='th'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>สร้างบัญชีผู้ขาย</title>
    <link href='https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { 
            color: #5d7a54; 
            margin-bottom: 10px;
            font-size: 2rem;
        }
        .subtitle {
            color: #6d5947;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
        .step {
            background: #f5f1e8;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #7a9871;
        }
        .step h3 {
            color: #5d7a54;
            margin-bottom: 10px;
        }
        .success { 
            background: #d1e7dd; 
            color: #0f5132; 
            padding: 20px; 
            border-radius: 12px; 
            margin: 15px 0;
            border-left: 4px solid #0f5132;
            font-size: 1.1rem;
        }
        .error { 
            background: #f8d7da; 
            color: #842029; 
            padding: 20px; 
            border-radius: 12px; 
            margin: 15px 0;
            border-left: 4px solid #842029;
            font-size: 1.1rem;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .warning h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        .credentials {
            background: #f5f1e8;
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
            border: 2px solid #d4c9b0;
        }
        .credential-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 15px;
            margin: 10px 0;
            padding: 10px 0;
            border-bottom: 1px solid #d4c9b0;
        }
        .credential-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #5d7a54;
        }
        .value {
            color: #3d3d3d;
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            background: white;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .btn {
            display: inline-block;
            margin: 10px 10px 10px 0;
            padding: 15px 30px;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 152, 113, 0.4);
        }
        .btn-secondary {
            background: white;
            color: #5d7a54;
            border: 2px solid #5d7a54;
        }
        .icon { margin-right: 8px; }
    </style>
</head>
<body>
<div class='container'>";

// ขั้นตอนที่ 1: สร้างตาราง admins
echo "<h1>🔧 ติดตั้งระบบผู้ขาย</h1>
<p class='subtitle'>กำลังสร้างตาราง admins และบัญชีผู้ขาย...</p>";

$create_table = "CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

echo "<div class='step'>
    <h3>📊 ขั้นตอนที่ 1: สร้างตารางฐานข้อมูล</h3>";

if ($conn->query($create_table)) {
    echo "<div class='success'>✓ สร้างตาราง 'admins' สำเร็จ</div>";
} else {
    echo "<div class='error'>✗ เกิดข้อผิดพลาด: " . $conn->error . "</div>
    </div></div></body></html>";
    exit;
}
echo "</div>";

// ขั้นตอนที่ 2: สร้างบัญชีผู้ขาย
echo "<div class='step'>
    <h3>👤 ขั้นตอนที่ 2: สร้างบัญชีผู้ขาย</h3>";

// ข้อมูลบัญชี
$accounts = [
    [
        'username' => 'admin',
        'password' => 'admin123',
        'full_name' => 'ผู้ดูแลร้าน',
        'email' => 'admin@beetleshop.com',
        'phone' => '064-746-8784',
        'role' => 'super_admin'
    ]
];

$created_accounts = [];

foreach ($accounts as $account) {
    // ตรวจสอบว่ามี username นี้แล้วหรือไม่
    $check = $conn->prepare("SELECT admin_id FROM admins WHERE username = ?");
    $check->bind_param("s", $account['username']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p style='color: #856404; margin: 10px 0;'>⚠ Username '{$account['username']}' มีอยู่แล้วในระบบ</p>";
        
        // ดึงข้อมูลบัญชีที่มีอยู่
        $created_accounts[] = $account;
        continue;
    }
    
    // สร้างบัญชีใหม่
    $password_hash = password_hash($account['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO admins (username, password_hash, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", 
        $account['username'], 
        $password_hash, 
        $account['full_name'], 
        $account['email'],
        $account['phone'],
        $account['role']
    );
    
    if ($stmt->execute()) {
        echo "<div class='success'>✓ สร้างบัญชี '{$account['username']}' สำเร็จ!</div>";
        $created_accounts[] = $account;
    } else {
        echo "<div class='error'>✗ ไม่สามารถสร้างบัญชี '{$account['username']}': " . $conn->error . "</div>";
    }
}

echo "</div>";

// แสดงข้อมูล Login
if (!empty($created_accounts)) {
    echo "<div class='step'>
        <h3>🔑 ขั้นตอนที่ 3: ข้อมูลการเข้าสู่ระบบ</h3>
        <p style='margin-bottom: 15px;'>บันทึกข้อมูลนี้ไว้ให้ดี!</p>";
    
    foreach ($created_accounts as $account) {
        echo "<div class='credentials'>
            <h4 style='color: #5d7a54; margin-bottom: 15px;'>{$account['full_name']}</h4>
            <div class='credential-row'>
                <div class='label'>Username:</div>
                <div class='value'>{$account['username']}</div>
            </div>
            <div class='credential-row'>
                <div class='label'>Password:</div>
                <div class='value'>{$account['password']}</div>
            </div>
            <div class='credential-row'>
                <div class='label'>สิทธิ์:</div>
                <div class='value'>" . ($account['role'] === 'super_admin' ? 'ผู้ดูแลสูงสุด' : 'ผู้ดูแล') . "</div>
            </div>
        </div>";
    }
    
    echo "</div>";
}

// คำเตือนด้านความปลอดภัย
echo "<div class='warning'>
    <h3>⚠️ สำคัญมาก! อ่านก่อนดำเนินการต่อ</h3>
    <ol style='margin: 15px 0; padding-left: 25px; line-height: 1.8;'>
        <li><strong>เปลี่ยนรหัสผ่านทันที</strong> หลังจาก Login ครั้งแรก</li>
        <li><strong>ลบไฟล์ setup_admin.php นี้ทันที</strong> (ไฟล์นี้มีความเสี่ยงด้านความปลอดภัย)</li>
        <li><strong>ห้ามแชร์</strong>ข้อมูล Login ให้ผู้อื่น</li>
        <li>บันทึกข้อมูล Login ไว้ในที่ปลอดภัย</li>
        <li>ใช้รหัสผ่านที่แข็งแรง (8+ ตัวอักษร, ผสมตัวเลขและอักขระพิเศษ)</li>
    </ol>
</div>";

// ปุ่มดำเนินการต่อ
echo "<div style='margin-top: 30px; text-align: center;'>
    <a href='admin_login.php' class='btn'>
        <span class='icon'>🔐</span> เข้าสู่ระบบผู้ขาย
    </a>
    <button onclick='if(confirm(\"คุณแน่ใจหรือว่าต้องการลบไฟล์นี้? \\n\\nกรุณาบันทึกข้อมูล Login ก่อน!\")) { alert(\"กรุณาลบไฟล์ setup_admin.php ด้วยตนเอง\"); }' class='btn btn-secondary'>
        <span class='icon'>🗑️</span> เตือนความจำ: ลบไฟล์นี้
    </button>
</div>";

echo "<div style='margin-top: 30px; padding: 20px; background: #f5f1e8; border-radius: 10px; text-align: center; color: #6d5947;'>
    <p><strong>URL สำหรับเข้าสู่ระบบ:</strong></p>
    <p style='font-size: 1.2rem; margin-top: 10px; font-family: monospace; color: #5d7a54;'>
        http://localhost/project_shop/admin_login.php
    </p>
</div>";

echo "</div>
</body>
</html>";

$conn->close();
?>