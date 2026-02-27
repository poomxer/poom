<?php
session_start();
include 'connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง กรุณากรอกอีเมลที่ถูกต้อง";
    } elseif (strlen($password) < 8) {
        $error = "รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร";
    } elseif ($password !== $confirm_password) {
        $error = "รหัสผ่านไม่ตรงกัน";
    } else {
        $check_email = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "อีเมลนี้ถูกใช้งานแล้ว";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (first_name, last_name, email, password_hash) 
                    VALUES ('$first_name', '$last_name', '$email', '$hashed_password')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location.href='login.php';</script>";
                exit();
            } else {
                $error = "เกิดข้อผิดพลาด: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก | ด้วงกว่าง พาเพลิน</title>
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
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            padding: 20px;
        }
        
        .register-box { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(93, 122, 84, 0.3);
            width: 100%;
            max-width: 420px;
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

        h2 { 
            text-align: center; 
            background: linear-gradient(135deg, #5d7a54, #7a9871);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .form-group { 
            margin-bottom: 15px;
            position: relative;
        }

        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            color: #4a4a3d; 
            font-size: 14px;
        }

        .form-group input { 
            width: 100%; 
            padding: 12px;
            border: 2px solid #d4c9b0;
            border-radius: 10px;
            font-size: 14px;
            background: #f5f1e8;
            transition: all 0.3s;
        }

        .form-group input:focus { 
            outline: none;
            border-color: #7a9871;
            background: white;
            box-shadow: 0 0 0 4px rgba(122, 152, 113, 0.1);
        }

        .form-group input.invalid { 
            border-color: #fc8181;
        }

        .form-group input.valid { 
            border-color: #68d391;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #8b7355;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #7a9871;
        }

        .validation-hint {
            font-size: 12px;
            color: #8b7355;
            margin-top: 4px;
        }

        .validation-hint.error {
            color: #fc8181;
        }

        .validation-hint.success {
            color: #68d391;
        }

        .btn-submit { 
            width: 100%; 
            padding: 12px;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white; 
            border: none; 
            border-radius: 10px;
            font-size: 15px; 
            font-weight: 600;
            cursor: pointer; 
            margin-top: 10px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(122, 152, 113, 0.4);
        }

        .btn-submit:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 152, 113, 0.5);
        }

        .login-text { 
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #6d5947;
        }

        .login-text a { 
            color: #7a9871;
            text-decoration: none;
            font-weight: 600;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #7a9871;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-btn:hover {
            color: #5d7a54;
        }

        .name-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="register-box">
    <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
    
    <h2>🪲 สมัครสมาชิก</h2>

    <?php if($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" id="registerForm">
        <div class="name-row">
            <div class="form-group">
                <label>ชื่อ</label>
                <input type="text" name="first_name" id="first_name" required>
            </div>
            <div class="form-group">
                <label>นามสกุล</label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
        </div>

        <div class="form-group">
            <label>อีเมล</label>
            <input type="email" name="email" id="email" required>
            <div class="validation-hint" id="emailHint">ตัวอย่าง: user@example.com</div>
        </div>
        
        <div class="form-group">
            <label>รหัสผ่าน (อย่างน้อย 8 ตัว)</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('password', 'icon1')">
                    <i class="fas fa-eye" id="icon1"></i>
                </button>
            </div>
            <div class="validation-hint" id="passwordHint"></div>
        </div>

        <div class="form-group">
            <label>ยืนยันรหัสผ่าน</label>
            <div class="password-wrapper">
                <input type="password" name="confirm_password" id="confirm_password" required>
                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', 'icon2')">
                    <i class="fas fa-eye" id="icon2"></i>
                </button>
            </div>
            <div class="validation-hint" id="confirmHint"></div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-user-plus"></i> สร้างบัญชี
        </button>
    </form>

    <div class="login-text">
        มีบัญชีแล้ว? <a href="login.php">เข้าสู่ระบบ</a>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    const emailInput = document.getElementById('email');
    const emailHint = document.getElementById('emailHint');
    
    emailInput.addEventListener('input', function() {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (this.value === '') {
            this.classList.remove('valid', 'invalid');
            emailHint.textContent = 'ตัวอย่าง: user@example.com';
            emailHint.classList.remove('error', 'success');
        } else if (emailPattern.test(this.value)) {
            this.classList.remove('invalid');
            this.classList.add('valid');
            emailHint.textContent = '✓ รูปแบบอีเมลถูกต้อง';
            emailHint.classList.remove('error');
            emailHint.classList.add('success');
        } else {
            this.classList.remove('valid');
            this.classList.add('invalid');
            emailHint.textContent = '✗ รูปแบบอีเมลไม่ถูกต้อง';
            emailHint.classList.remove('success');
            emailHint.classList.add('error');
        }
    });

    const passwordInput = document.getElementById('password');
    const passwordHint = document.getElementById('passwordHint');
    
    passwordInput.addEventListener('input', function() {
        if (this.value === '') {
            this.classList.remove('valid', 'invalid');
            passwordHint.textContent = '';
            passwordHint.classList.remove('error', 'success');
        } else if (this.value.length < 8) {
            this.classList.remove('valid');
            this.classList.add('invalid');
            passwordHint.textContent = '✗ รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร';
            passwordHint.classList.remove('success');
            passwordHint.classList.add('error');
        } else {
            this.classList.remove('invalid');
            this.classList.add('valid');
            passwordHint.textContent = '✓ รหัสผ่านเหมาะสม';
            passwordHint.classList.remove('error');
            passwordHint.classList.add('success');
        }
        
        checkConfirmPassword();
    });

    const confirmInput = document.getElementById('confirm_password');
    const confirmHint = document.getElementById('confirmHint');
    
    confirmInput.addEventListener('input', checkConfirmPassword);
    
    function checkConfirmPassword() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        
        if (confirm === '') {
            confirmInput.classList.remove('valid', 'invalid');
            confirmHint.textContent = '';
            confirmHint.classList.remove('error', 'success');
        } else if (password === confirm) {
            confirmInput.classList.remove('invalid');
            confirmInput.classList.add('valid');
            confirmHint.textContent = '✓ รหัสผ่านตรงกัน';
            confirmHint.classList.remove('error');
            confirmHint.classList.add('success');
        } else {
            confirmInput.classList.remove('valid');
            confirmInput.classList.add('invalid');
            confirmHint.textContent = '✗ รหัสผ่านไม่ตรงกัน';
            confirmHint.classList.remove('success');
            confirmHint.classList.add('error');
        }
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const email = emailInput.value;
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailPattern.test(email)) {
            e.preventDefault();
            alert('กรุณากรอกอีเมลให้ถูกต้อง');
            emailInput.focus();
            return false;
        }
        
        if (passwordInput.value.length < 8) {
            e.preventDefault();
            alert('รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร');
            passwordInput.focus();
            return false;
        }
        
        if (passwordInput.value !== confirmInput.value) {
            e.preventDefault();
            alert('รหัสผ่านไม่ตรงกัน');
            confirmInput.focus();
            return false;
        }
    });
</script>

</body>
</html>