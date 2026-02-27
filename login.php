<?php
ob_start();
session_start();
include 'connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['email'] = $user['email'];
            
            echo "<script>
                const localCart = JSON.parse(localStorage.getItem('beetleCart') || '[]');
                
                if (localCart.length > 0) {
                    fetch('cartapi.php?action=sync', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ cart: localCart })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            localStorage.removeItem('beetleCart');
                            alert('เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับคุณ " . addslashes($user['first_name'] . ' ' . $user['last_name']) . "');
                            window.location.href = 'index.php';
                        }
                    })
                    .catch(error => {
                        console.error('Error syncing cart:', error);
                        alert('เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับคุณ " . addslashes($user['first_name'] . ' ' . $user['last_name']) . "');
                        window.location.href = 'index.php';
                    });
                } else {
                    alert('เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับคุณ " . addslashes($user['first_name'] . ' ' . $user['last_name']) . "');
                    window.location.href = 'index.php';
                }
            </script>";
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบอีเมลนี้ในระบบ";
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | ด้วงกว่าง พาเพลิน</title>
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
            max-width: 380px;
        }

        .login-box { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px 30px 30px;
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

        .logo-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            font-size: 3rem;
            margin-bottom: 5px;
            display: inline-block;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        h2 { 
            text-align: center; 
            background: linear-gradient(135deg, #5d7a54, #7a9871);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .subtitle {
            text-align: center;
            color: #6d5947;
            font-size: 0.85rem;
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

        .extra-options { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 20px;
            font-size: 13px;
        }
        
        .remember-me { 
            display: flex; 
            align-items: center; 
            gap: 8px;
            color: #4a4a3d;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #7a9871;
        }
        
        .forgot-pass { 
            color: #7a9871;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-pass:hover { 
            color: #5d7a54;
            text-decoration: underline;
        }

        .btn-submit { 
            width: 100%; 
            padding: 13px;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white; 
            border: none; 
            border-radius: 12px;
            font-size: 15px; 
            font-weight: 600;
            cursor: pointer; 
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(122, 152, 113, 0.4);
            position: relative;
            overflow: hidden;
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
            box-shadow: 0 6px 20px rgba(122, 152, 113, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .divider { 
            text-align: center; 
            margin: 25px 0; 
            position: relative;
            color: #8b7355;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: #d4c9b0;
        }

        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .register-text { 
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6d5947;
        }

        .register-text a { 
            color: #7a9871;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-text a:hover {
            color: #5d7a54;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 35px 25px 25px;
            }

            h2 {
                font-size: 1.4rem;
            }

            .logo-icon {
                font-size: 2.5rem;
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
            <div class="logo-icon">🪲</div>
        </div>

        <h2>เข้าสู่ระบบ</h2>
        <p class="subtitle">ยินดีต้อนรับกลับมา!</p>

        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-field">
                <label><i class="fas fa-envelope"></i> อีเมล</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" placeholder="example@email.com" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>

            <div class="input-field">
                <label><i class="fas fa-lock"></i> รหัสผ่าน</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="extra-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    <span>จดจำฉันไว้</span>
                </label>
                <a href="forgot_password.php" class="forgot-pass">ลืมรหัสผ่าน?</a>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
            </button>
        </form>

        <div class="divider">หรือ</div>

        <div class="register-text">
            ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a>
        </div>

        <div class="divider">•••</div>

        <div class="register-text" style="margin-top: 15px;">
            <a href="admin_login.php" style="color: #8b7355; font-size: 0.9rem;">
                <i class="fas fa-shield-alt"></i> เข้าสู่ระบบผู้ดูแล
            </a>
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