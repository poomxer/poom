<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านด้วงกว่าง พาเพลิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #f5f1e8 0%, #e8dcc4 100%);
            color: #3d3d3d;
            min-height: 100vh;
        }

        header {
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(93, 122, 84, 0.3);
        }

        header h1 {
            font-size: 2.5rem;
            color: #ffffff;
            margin-bottom: 1rem;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .social-icons a {
            color: #fff;
            font-size: 1.5rem;
            transition: all 0.3s;
        }

        .social-icons a:hover {
            color: #e8dcc4;
            transform: scale(1.2);
        }

        nav {
            background: linear-gradient(to bottom, #fefefe, #f9f7f3);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            list-style: none;
        }

        .nav-left, .nav-right {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav li {
            list-style: none;
        }

        nav a {
            color: #4a4a3d;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        nav a:hover {
            background: #7a9871;
            color: white;
        }

        .cart-button {
            background: linear-gradient(135deg, #8b7355, #6d5947);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .cart-button:hover {
            background: linear-gradient(135deg, #6d5947, #8b7355);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(109, 89, 71, 0.3);
        }

        .cart-count {
            background: #c67b5c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }

        .hero {
            position: relative;
            height: 350px;
            overflow: hidden;
            margin: 2rem auto;
            max-width: 1400px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #7a9871, #5d7a54);
        }

        .hero img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .hero img.active {
            opacity: 1;
        }

        .best-seller {
            max-width: 1400px;
            margin: 3rem auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product {
            background: linear-gradient(to bottom, #ffffff, #faf8f4);
            border-radius: 20px;
            padding: 1.5rem;
            position: relative;
            transition: all 0.3s;
            border: 2px solid #d4c9b0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(122, 152, 113, 0.25);
            border-color: #7a9871;
        }

        .badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, #c67b5c, #a0664f);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: bold;
            z-index: 1;
            box-shadow: 0 2px 8px rgba(198, 123, 92, 0.3);
        }

        .product img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .product img:hover {
            transform: scale(1.05);
        }

        .product h3 {
            color: #5d7a54;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        .product p {
            color: #4a4a3d;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .product span {
            color: #c67b5c;
            font-weight: bold;
        }

        .buy-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 8px;
        }

        .buy-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(122, 152, 113, 0.4);
            background: linear-gradient(135deg, #5d7a54, #7a9871);
        }

        .detail-button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #8b7355, #6d5947);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .detail-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 115, 85, 0.4);
            background: linear-gradient(135deg, #6d5947, #8b7355);
        }

        .buy-button.locked {
            background: linear-gradient(135deg, #b0b0b0, #888);
            cursor: not-allowed;
            opacity: 0.75;
        }

        .buy-button.locked:hover {
            transform: none;
            box-shadow: none;
            background: linear-gradient(135deg, #b0b0b0, #888);
        }

        /* Modal Login Prompt */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px);
        }

        .modal-overlay.show { display: flex; }

        .modal-box {
            background: white;
            border-radius: 20px;
            padding: 40px 35px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            animation: popIn 0.3s ease;
        }

        @keyframes popIn {
            from { transform: scale(0.85); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }

        .modal-icon { font-size: 3.5rem; margin-bottom: 15px; }

        .modal-box h2 {
            color: #5d7a54;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .modal-box p {
            color: #6d5947;
            font-size: 1rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .modal-btn-login {
            flex: 1;
            min-width: 130px;
            padding: 14px 20px;
            background: linear-gradient(135deg, #5d7a54, #7a9871);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s;
        }

        .modal-btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(93,122,84,0.4);
        }

        .modal-btn-register {
            flex: 1;
            min-width: 130px;
            padding: 14px 20px;
            background: linear-gradient(135deg, #8b7355, #6d5947);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Kanit', sans-serif;
            transition: all 0.3s;
        }

        .modal-btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(109,89,71,0.4);
        }

        .modal-btn-cancel {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: #aaa;
            border: none;
            font-size: 0.9rem;
            cursor: pointer;
            margin-top: 12px;
            font-family: 'Kanit', sans-serif;
            transition: color 0.2s;
        }

        .modal-btn-cancel:hover { color: #666; }

        footer {
            background: linear-gradient(135deg, #5d7a54 0%, #4a6043 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: 3rem;
            box-shadow: 0 -4px 20px rgba(93, 122, 84, 0.2);
        }

        footer p {
            margin: 0.5rem 0;
            font-size: 1.1rem;
        }

        footer i {
            margin-right: 0.5rem;
            color: #e8dcc4;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-left, .nav-right {
                flex-direction: column;
                gap: 0.5rem;
            }

            header h1 {
                font-size: 2rem;
            }

            .best-seller {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><i class="fas fa-bug"></i> ร้านด้วงกว่าง พาเพลิน</h1>
        <div class="social-icons">
            <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="#" title="Line"><i class="fab fa-line"></i></a>
            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
    </header>

    <nav>
        <ul class="nav-container">
            <div class="nav-left">
                <li><a href="index.php"><i class="fas fa-home"></i> หน้าหลัก</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> ตะกร้าสินค้า</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="order_history.php"><i class="fas fa-history"></i> ประวัติการสั่งซื้อ</a></li>
                <?php endif; ?>
            </div>
            <div class="nav-right">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="#"><i class="fas fa-user"></i> <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'ผู้ใช้'; ?></a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> สมัครสมาชิก</a></li>
                <?php endif; ?>
            </div>
        </ul>
    </nav>

    <div class="hero">
        <img src="img/h1.jpg" alt="ด้วงกว่าง" class="active" onerror="this.style.display='none'">
        <img src="img/h2.jpg" alt="ด้วงกว่าง" onerror="this.style.display='none'">
        <img src="img/h3.jpg" alt="ด้วงกว่าง" onerror="this.style.display='none'">
    </div>

    <section class="best-seller">
        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/1.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p1'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างกิแรฟ'">
            <h3>ด้วงกว่างกิแรฟ</h3>
            <p>ราคา: <span>150 บาท</span></p>
            <button class="buy-button" onclick="addCart('p1','ด้วงกว่างกิแรฟ',150,'img/1.jpg',20)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p1'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/2.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p2'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างอัลแทส'">
            <h3>ด้วงกว่างอัลแทส</h3>
            <p>ราคา: <span>550 บาท</span></p>
            <button class="buy-button" onclick="addCart('p2','ด้วงกว่างอัลแทส',550,'img/2.jpg',35)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p2'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/3.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p3'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างคอเคซัส'">
            <h3>ด้วงกว่างคอเคซัส</h3>
            <p>ราคา: <span>1,050 บาท</span></p>
            <button class="buy-button" onclick="addCart('p3','ด้วงกว่างคอเคซัส',1050,'img/3.jpg',30)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p3'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/4.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p4'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างคอเคซัส1'">
            <h3>ด้วงกว่างคอเคซัส1</h3>
            <p>ราคา: <span>1,150 บาท</span></p>
            <button class="buy-button" onclick="addCart('p4','ด้วงกว่างคอเคซัส1',1150,'img/4.jpg',25)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p4'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/5.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p5'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างญี่ปุ่น'">
            <h3>ด้วงกว่างญี่ปุ่น</h3>
            <p>ราคา: <span>250 บาท</span></p>
            <button class="buy-button" onclick="addCart('p5','ด้วงกว่างญี่ปุ่น',250,'img/5.jpg',15)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p5'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/6.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p6'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างเฮอร์คิวลิส'">
            <h3>ด้วงกว่างเฮอร์คิวลิส</h3>
            <p>ราคา: <span>1,500 บาท</span></p>
            <button class="buy-button" onclick="addCart('p6','ด้วงกว่างเฮอร์คิวลิส',1500,'img/6.jpg',10)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p6'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/7.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p7'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างทิทิอุส'">
            <h3>ด้วงกว่างทิทิอุส</h3>
            <p>ราคา: <span>350 บาท</span></p>
            <button class="buy-button" onclick="addCart('p7','ด้วงกว่างทิทิอุส',350,'img/7.jpg',22)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p7'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/8.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p8'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างโกไลแอท'">
            <h3>ด้วงกว่างโกไลแอท</h3>
            <p>ราคา: <span>750 บาท</span></p>
            <button class="buy-button" onclick="addCart('p8','ด้วงกว่างโกไลแอท',750,'img/8.jpg',18)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p8'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/9.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p9'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างลูโคซัส'">
            <h3>ด้วงกว่างลูโคซัส</h3>
            <p>ราคา: <span>750 บาท</span></p>
            <button class="buy-button" onclick="addCart('p9','ด้วงกว่างลูโคซัส',750,'img/9.jpg',12)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p9'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/10.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p10'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างแรด'">
            <h3>ด้วงกว่างแรด</h3>
            <p>ราคา: <span>150 บาท</span></p>
            <button class="buy-button" onclick="addCart('p10','ด้วงกว่างแรด',150,'img/10.jpg',28)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p10'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/15.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p15'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างซางหูกระต่าย'">
            <h3>ด้วงกว่างซางหูกระต่าย</h3>
            <p>ราคา: <span>1,500 บาท</span></p>
            <button class="buy-button" onclick="addCart('p15','ด้วงกว่างซางหูกระต่าย',1500,'img/15.jpg',8)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p15'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <div class="badge">ขายดี</div>
            <img src="img/14.jpg" alt="ด้วงกว่าง" onclick="window.location.href='product_detail.php?id=p14'" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างขาวแกรน'">
            <h3>ด้วงกว่างขาวแกรน</h3>
            <p>ราคา: <span>1,500 บาท</span></p>
            <button class="buy-button" onclick="addCart('p14','ด้วงกว่างขาวแกรน',1500,'img/14.jpg',14)">เพิ่มลงตะกร้า</button>
            <button class="detail-button" onclick="window.location.href='product_detail.php?id=p14'">ดูรายละเอียด</button>
        </div>

        <div class="product">
            <img src="img/12.jpg" alt="ด้วงกว่าง" onerror="this.src='https://via.placeholder.com/300x250?text=ด้วงกว่างเนปจูน'">
            <h3>ด้วงกว่างเนปจูน</h3>
            <p>ราคา: <span>-</span></p>
            <p>จะมีในเร็วๆนี้</p>
        </div>
    </section>

    <footer>
        <p><i class="fas fa-phone"></i> โทร: 064-746-8784</p>
        <p><i class="fab fa-line"></i> Line: 1001</p>
        <p><i class="fas fa-map-marker-alt"></i> เชียงราย</p>
    </footer>

    <!-- Modal: กรุณาเข้าสู่ระบบ -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <div class="modal-icon">🔒</div>
            <h2>กรุณาเข้าสู่ระบบก่อน</h2>
            <p>เพื่อเพิ่มสินค้าลงตะกร้า<br>กรุณาเข้าสู่ระบบหรือสมัครสมาชิกก่อนนะครับ</p>
            <div class="modal-buttons">
                <button class="modal-btn-login" onclick="window.location.href='login.php'">
                    <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                </button>
                <button class="modal-btn-register" onclick="window.location.href='register.php'">
                    <i class="fas fa-user-plus"></i> สมัครสมาชิก
                </button>
            </div>
            <button class="modal-btn-cancel" onclick="closeLoginModal()">ยกเลิก / กลับไปดูสินค้า</button>
        </div>
    </div>

    <script>
        // Slider
        var slide = 0;
        var imgs = document.querySelectorAll('.hero img');
        
        function nextSlide() {
            if (imgs.length > 0) {
                imgs[slide].classList.remove('active');
                slide = (slide + 1) % imgs.length;
                imgs[slide].classList.add('active');
            }
        }
        
        // เริ่ม slider ถ้ามีรูปภาพ
        if (imgs.length > 0) {
            setInterval(nextSlide, 4000);
        }

        // เพิ่มสินค้า
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        function showLoginModal() {
            document.getElementById('loginModal').classList.add('show');
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('show');
        }

        // ปิด modal เมื่อคลิกพื้นหลัง
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) closeLoginModal();
        });

        async function addCart(id, name, price, img, stock) {
            if (!isLoggedIn) {
                showLoginModal();
                return;
            }
            // ถ้า login แล้ว ส่งไปยัง API
            try {
                const response = await fetch('cartapi.php?action=add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        name: name,
                        price: price,
                        image: img,
                        quantity: 1
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    updateCount();
                    alert('เพิ่ม ' + name + ' ลงตะกร้าแล้ว!');
                } else {
                    alert('ไม่สามารถเพิ่มสินค้าได้');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('ไม่สามารถเพิ่มสินค้าได้');
            }
        }

        // อัพเดทจำนวน
        async function updateCount() {
            let cart = [];
            let total = 0;
            
            if (isLoggedIn) {
                // ดึงจาก API
                try {
                    const response = await fetch('cartapi.php?action=get');
                    const data = await response.json();
                    
                    if (data.success && data.cart) {
                        cart = data.cart;
                    }
                } catch (error) {
                    console.error('Error loading cart:', error);
                }
            } else {
                // ดึงจาก localStorage
                const cartData = localStorage.getItem('beetleCart');
                cart = cartData ? JSON.parse(cartData) : [];
            }
            
            for (var i = 0; i < cart.length; i++) {
                total += cart[i].quantity;
            }
            
            var cartCountElement = document.getElementById('cartCount');
            if (cartCountElement) {
                cartCountElement.textContent = total;
            }
        }

        // โหลดตอนเปิดหน้า
        window.onload = function() {
            updateCount();
            // ล็อกปุ่มเพิ่มลงตะกร้าถ้ายังไม่ login
            if (!isLoggedIn) {
                document.querySelectorAll('.buy-button').forEach(function(btn) {
                    btn.classList.add('locked');
                    btn.innerHTML = '<i class="fas fa-lock"></i> เข้าสู่ระบบเพื่อซื้อ';
                });
            }
        };
    </script>
</body>
</html>