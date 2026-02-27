<?php
session_start();
include 'connect.php';

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    if (empty($email)) {
        $error = "กรุณากรอกอีเมล";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } else {
        $check_email = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        
        if (mysqli_num_rows($result) > 0) {
            $reset_token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $update_sql = "UPDATE users SET reset_token = '$reset_token', reset_expiry = '$expiry' WHERE email = '$email'";
            
            if (mysqli_query($conn, $update_sql)) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_token'] = $reset_token;
                
                $message = "ระบบได้ส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลของคุณแล้ว<br>กรุณาตรวจสอบอีเมลของคุณ";
                
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'reset_password.php?token=$reset_token';
                    }, 2000);
                </script>";
            } else {
                $error = "เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง";
            }
        } else {
            $message = "หากอีเมลนี้มีอยู่ในระบบ เราจะส่งลิงก์รีเซ็ตรหัสผ่านไปให้";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน | ด้วงกว่าง พาเพลิน</title>
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
        }
        
        .forgot-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
        }

        .forgot-box { 
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

        .icon-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .lock-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
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
            margin-bottom: 10px;
            font-size: 1.7rem;
            font-weight: 600;
        }

        .subtitle {
            text-align: center;
            color: #6d5947;
            font-size: 0.9rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .error-message {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            animation: shake 0.5s;
        }

        .success-message {
            background: #e6ffed;
            border: 1px solid #a3e8b4;
            color: #2d7a4c;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            animation: slideIn 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .input-field { 
            margin-bottom: 25px;
            position: relative;
        }

        .input-field label { 
            display: block; 
            margin-bottom: 8px; 
            color: #4a4a3d; 
            font-size: 14px;
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
            padding: 13px 15px 13px 45px;
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

        .btn-submit { 
            width: 100%; 
            padding: 14px;
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

        .info-box {
            background: #f0f4e8;
            border: 1px solid #d4e8c4;
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
            font-size: 13px;
            color: #4a4a3d;
        }

        .info-box i {
            color: #7a9871;
            margin-right: 8px;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #6d5947;
        }

        .back-to-login a {
            color: #7a9871;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .back-to-login a:hover {
            color: #5d7a54;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .forgot-box {
                padding: 35px 25px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .lock-icon {
                width: 70px;
                height: 70px;
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<div class="forgot-container">
    <div class="forgot-box">
        <button class="back-button" onclick="window.location.href='login.php'">
            <i class="fas fa-arrow-left"></i>
        </button>

        <div class="icon-section">
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
        </div>

        <h2>ลืมรหัสผ่าน?</h2>
        <p class="subtitle">ไม่ต้องกังวล! กรอกอีเมลของคุณ<br>เราจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านให้</p>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="forgotForm">
            <div class="input-field">
                <label><i class="fas fa-envelope"></i> อีเมลที่ลงทะเบียน</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" placeholder="example@email.com" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน
            </button>
        </form>

        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            ลิงก์รีเซ็ตรหัสผ่านจะหมดอายุภายใน 1 ชั่วโมง
        </div>

        <div class="back-to-login">
            จำรหัสผ่านได้แล้ว? <a href="login.php">กลับไปเข้าสู่ระบบ</a>
        </div>
    </div>
</div>

</body>
</html>