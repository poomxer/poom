<?php
session_start();

// ถ้า login แล้วให้ไปหน้า admin_orders
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_orders.php');
    exit();
}

include 'connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // ตรวจสอบรหัสผ่าน
        // สำหรับ admin ที่สร้างจาก setup_admin.php จะใช้ password_hash
        // แต่ถ้าเป็น admin เก่าที่มี password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
        // (ซึ่งเป็น hash ของ 'password') ก็ให้ login ได้
        
        $password_valid = false;
        
        // ตรวจสอบด้วย password_verify
        if (password_verify($password, $admin['password_hash'])) {
            $password_valid = true;
        }
        // หรือถ้าเป็น password เริ่มต้น 'password' หรือ 'admin123'
        else if ($password === 'password' && $admin['password_hash'] === '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') {
            $password_valid = true;
        }
        else if ($password === 'admin123') {
            // ลอง verify อีกครั้งกับ admin123
            $test_hash = password_hash('admin123', PASSWORD_DEFAULT);
            if (password_verify($password, $admin['password_hash'])) {
                $password_valid = true;
            }
        }
        
        if ($password_valid) {
            // Login สำเร็จ
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            
            // อัพเดท last_login
            $update_sql = "UPDATE admins SET last_login = NOW() WHERE admin_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $admin['admin_id']);
            $update_stmt->execute();
            
            header('Location: admin_orders.php');
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบชื่อผู้ใช้นี้ในระบบ หรือบัญชีถูกปิดใช้งาน";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบผู้ดูแล | ด้วงกว่าง พาเพลิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
            font-family: 'Kanit', sans-serif; 
        }
        
        body { 
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,101.3C1248,85,1344,75,1392,69.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center;
            background-size: cover;
            animation: wave 10s linear infinite;
        }

        @keyframes wave {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
        }

        .login-box { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px 35px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(93, 122, 84, 0.3);
            position: relative;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(122, 152, 113, 0.1);
            border: none;
            color: #5d7a54;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-button:hover { 
            background: #7a9871;
            color: white;
            transform: translateX(-5px);
        }

        .admin-badge {
            background: linear-gradient(135deg, #8b7355, #6d5947);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #8b7355, #6d5947);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        h2 { 
            text-align: center; 
            background: linear-gradient(135deg, #5d7a54, #7a9871);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .subtitle {
            text-align: center;
            color: #6d5947;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .input-field { 
            margin-bottom: 18px;
            position: relative;
        }

        .input-field label { 
            display: block; 
            margin-bottom: 8px; 
            color: #4a4a3d; 
            font-size: 13px;
            font-weight: 500;
        }
        
        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b7355;
            font-size: 1.1rem;
        }

        .input-field input { 
            width: 100%; 
            padding: 12px 15px 12px 40px;
            border: 2px solid #d4c9b0;
            border-radius: 12px;
            outline: none; 
            font-size: 14px;
            transition: all 0.3s;
            background: #f5f1e8;
        }

        .input-field input:focus { 
            border-color: #7a9871;
            box-shadow: 0 0 0 4px rgba(122, 152, 113, 0.1);
            background: white;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #8b7355;
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #7a9871;
        }

        .btn-submit { 
            width: 100%; 
            padding: 13px;
            background: linear-gradient(135deg, #8b7355, #6d5947);
            color: white; 
            border: none; 
            border-radius: 12px;
            font-size: 15px; 
            font-weight: 600;
            cursor: pointer; 
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(139, 115, 85, 0.4);
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 115, 85, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            font-size: 13px;
            color: #856404;
        }

        .info-box i {
            color: #ffc107;
            margin-right: 8px;
        }

        .user-login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6d5947;
        }

        .user-login-link a {
            color: #7a9871;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .user-login-link a:hover {
            color: #5d7a54;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 35px 25px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .logo-icon {
                width: 70px;
                height: 70px;
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <button class="back-button" onclick="window.location.href='index.php'">
            <i class="fas fa-arrow-left"></i>
        </button>

        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="admin-badge">
                <i class="fas fa-crown"></i> ADMIN AREA
            </div>
        </div>

        <h2>ระบบผู้ดูแล</h2>
        <p class="subtitle">เข้าสู่ระบบจัดการร้านค้า</p>

        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-field">
                <label><i class="fas fa-user-shield"></i> ชื่อผู้ใช้</label>
                <div class="input-wrapper">
                    <i class="fas fa-user-shield input-icon"></i>
                    <input type="text" name="username" placeholder="admin" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" autocomplete="username">
                </div>
            </div>

            <div class="input-field">
                <label><i class="fas fa-key"></i> รหัสผ่าน</label>
                <div class="input-wrapper">
                    <i class="fas fa-key input-icon"></i>
                    <input type="password" name="password" id="password" placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบผู้ดูแล
            </button>
        </form>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            หน้านี้สำหรับผู้ดูแลระบบเท่านั้น<br>
            หากไม่มีสิทธิ์เข้าถึง กรุณาติดต่อผู้ดูแล
        </div>

        <div class="user-login-link">
            คุณเป็นลูกค้าใช่ไหม? <a href="login.php">เข้าสู่ระบบลูกค้า</a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>