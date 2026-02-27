<?php
/**
 * ไฟล์แก้ไขรหัสผ่าน Admin
 * รันไฟล์นี้เพื่อรีเซ็ตรหัสผ่าน admin
 * หลังจากรันเสร็จแล้วลบทิ้งทันที!
 */

include 'connect.php';

echo "<!DOCTYPE html>
<html lang='th'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>แก้ไขรหัสผ่าน Admin</title>
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
            max-width: 700px;
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
        .success { 
            background: #d1e7dd; 
            color: #0f5132; 
            padding: 20px; 
            border-radius: 12px; 
            margin: 15px 0;
            border-left: 4px solid #0f5132;
        }
        .error { 
            background: #f8d7da; 
            color: #842029; 
            padding: 20px; 
            border-radius: 12px; 
            margin: 15px 0;
            border-left: 4px solid #842029;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .info-box {
            background: #f5f1e8;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border: 2px solid #d4c9b0;
        }
        .credential {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 10px 0;
            font-size: 1.1rem;
            border: 1px solid #d4c9b0;
        }
        .btn {
            display: inline-block;
            margin: 10px 5px 0 0;
            padding: 12px 24px;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 152, 113, 0.4);
        }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔧 แก้ไขรหัสผ่าน Admin</h1>
    <p class='subtitle'>รีเซ็ตรหัสผ่านสำหรับเข้าสู่ระบบผู้ดูแล</p>";

// ตรวจสอบว่ามีตาราง admins หรือไม่
$check_table = "SHOW TABLES LIKE 'admins'";
$result = $conn->query($check_table);

if ($result->num_rows === 0) {
    echo "<div class='error'>❌ ไม่พบตาราง admins<br>กรุณารันไฟล์ setup_admin.php ก่อน</div>
    <a href='setup_admin.php' class='btn'>ไปที่ Setup Admin</a>
    </div></body></html>";
    exit;
}

// ข้อมูล Admin ที่ต้องการแก้ไข
$username = 'admin';
$new_password = 'admin123';
$full_name = 'ผู้ดูแลร้าน';
$email = 'admin@beetleshop.com';

// สร้าง password hash ใหม่
$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// ตรวจสอบว่ามี admin นี้อยู่แล้วหรือไม่
$check = $conn->prepare("SELECT admin_id, username, full_name FROM admins WHERE username = ?");
$check->bind_param("s", $username);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    // อัพเดทรหัสผ่านของ admin ที่มีอยู่
    $admin = $check_result->fetch_assoc();
    
    $update = $conn->prepare("UPDATE admins SET password_hash = ?, full_name = ?, email = ?, is_active = 1 WHERE username = ?");
    $update->bind_param("ssss", $password_hash, $full_name, $email, $username);
    
    if ($update->execute()) {
        echo "<div class='success'>
            ✅ อัพเดทรหัสผ่านสำเร็จ!<br>
            บัญชี: {$admin['username']} ({$admin['full_name']})
        </div>";
    } else {
        echo "<div class='error'>❌ ไม่สามารถอัพเดทรหัสผ่านได้: " . $conn->error . "</div>
        </div></body></html>";
        exit;
    }
} else {
    // สร้าง admin ใหม่
    $insert = $conn->prepare("INSERT INTO admins (username, password_hash, full_name, email, role, is_active) VALUES (?, ?, ?, ?, 'super_admin', 1)");
    $insert->bind_param("ssss", $username, $password_hash, $full_name, $email);
    
    if ($insert->execute()) {
        echo "<div class='success'>
            ✅ สร้างบัญชี Admin ใหม่สำเร็จ!
        </div>";
    } else {
        echo "<div class='error'>❌ ไม่สามารถสร้างบัญชีได้: " . $conn->error . "</div>
        </div></body></html>";
        exit;
    }
}

// แสดงข้อมูล Login
echo "<div class='info-box'>
    <h3 style='color: #5d7a54; margin-bottom: 15px;'>📋 ข้อมูลเข้าสู่ระบบ</h3>
    <p><strong>Username:</strong></p>
    <div class='credential'>$username</div>
    <p style='margin-top: 15px;'><strong>Password:</strong></p>
    <div class='credential'>$new_password</div>
</div>";

echo "<div class='warning'>
    <h3>⚠️ สำคัญมาก!</h3>
    <ol style='margin: 15px 0; padding-left: 25px; line-height: 1.8;'>
        <li><strong>ลบไฟล์ fix_admin_password.php นี้ทันที</strong> หลังจากบันทึกข้อมูล</li>
        <li>เปลี่ยนรหัสผ่านหลังจาก Login ครั้งแรก</li>
        <li>ห้ามแชร์ข้อมูล Login ให้ผู้อื่น</li>
    </ol>
</div>";

// แสดงปุ่ม
echo "<div style='margin-top: 30px; text-align: center;'>
    <a href='admin_login.php' class='btn'>
        🔐 เข้าสู่ระบบผู้ดูแล
    </a>
</div>";

// แสดง Admin ทั้งหมดในระบบ
echo "<h3 style='margin-top: 40px; color: #5d7a54;'>👥 รายชื่อ Admin ทั้งหมด:</h3>";
$all_admins = $conn->query("SELECT admin_id, username, full_name, email, role, is_active, created_at FROM admins ORDER BY admin_id");

if ($all_admins->num_rows > 0) {
    echo "<table style='width: 100%; margin-top: 15px; border-collapse: collapse;'>
        <thead>
            <tr style='background: #f5f1e8; border-bottom: 2px solid #d4c9b0;'>
                <th style='padding: 10px; text-align: left;'>Username</th>
                <th style='padding: 10px; text-align: left;'>ชื่อ</th>
                <th style='padding: 10px; text-align: center;'>สถานะ</th>
            </tr>
        </thead>
        <tbody>";
    
    while ($row = $all_admins->fetch_assoc()) {
        $status = $row['is_active'] ? '✅ ใช้งาน' : '❌ ปิด';
        $status_color = $row['is_active'] ? '#0f5132' : '#842029';
        echo "<tr style='border-bottom: 1px solid #d4c9b0;'>
            <td style='padding: 10px;'><strong>{$row['username']}</strong></td>
            <td style='padding: 10px;'>{$row['full_name']}</td>
            <td style='padding: 10px; text-align: center; color: $status_color;'>$status</td>
        </tr>";
    }
    
    echo "</tbody></table>";
}

echo "</div>
</body>
</html>";

$conn->close();
?>