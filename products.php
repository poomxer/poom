<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านด้วงกว่าง | ด้วงกว่างพาเพลิน</title>
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
            color: #3d3d3d;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(93, 122, 84, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            font-size: 1.8rem;
        }

        .header h1 {
            font-size: 1.5rem;
            color: #ffffff;
        }

        .header-right {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-home {
            background: linear-gradient(135deg, #8b7355, #6d5947);
            color: white;
        }

        .btn-home:hover {
            background: linear-gradient(135deg, #6d5947, #8b7355);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(109, 89, 71, 0.3);
        }

        .btn-back {
            background: #7a9871;
            color: white;
        }

        .btn-back:hover {
            background: #5d7a54;
            transform: translateY(-2px);
        }

        .btn-cart {
            background: linear-gradient(135deg, #8b7355, #6d5947);
            color: white;
            position: relative;
        }

        .btn-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(109, 89, 71, 0.3);
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #c67b5c;
            color: white;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .products-section {
            background: linear-gradient(to bottom, #ffffff, #faf8f4);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid #d4c9b0;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(122, 152, 113, 0.25);
            border: 2px solid #7a9871;
        }

        .badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #c67b5c, #a0664f);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
            box-shadow: 0 2px 8px rgba(198, 123, 92, 0.3);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f7fafc;
        }

        .product-info {
            padding: 15px;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #5d7a54;
            margin-bottom: 10px;
            min-height: 2.2em;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #4a4a3d;
            margin-bottom: 15px;
        }

        .add-to-cart-btn {
            width: 100%;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(93, 122, 84, 0.4);
        }

        .add-to-cart-btn:active {
            transform: translateY(0);
        }

        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(93, 122, 84, 0.4);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.show {
            display: flex;
        }

        .add-to-cart-btn.locked {
            background: linear-gradient(135deg, #b0b0b0, #888);
            cursor: not-allowed;
            opacity: 0.75;
        }

        .add-to-cart-btn.locked:hover {
            transform: none;
            box-shadow: none;
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

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
            }

            .header-left,
            .header-right {
                width: 100%;
                justify-content: center;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }

            .btn {
                font-size: 0.85rem;
                padding: 8px 15px;
            }

            .toast {
                bottom: 20px;
                right: 20px;
                left: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <span class="logo">🪲</span>
                <h1>ด้วงกว่างพาเพลิน</h1>
            </div>
            <div class="header-right">
                <button class="btn btn-home" onclick="window.location.href='index.php'">
                    <i class="fas fa-home"></i> หน้าหลัก
                </button>
                <button class="btn btn-back" onclick="history.back()">
                    <i class="fas fa-arrow-left"></i> ย้อนกลับ
                </button>
                <button class="btn btn-cart" onclick="viewCart()">
                    <i class="fas fa-shopping-basket"></i> ตะกร้า
                    <span class="cart-badge" id="cartBadge">0</span>
                </button>
            </div>
        </div>

        <div class="products-section">
            <h2 style="font-size: 1.5rem; color: #5d7a54; margin-bottom: 10px;">
                <i class="fas fa-box"></i> สินค้าทั้งหมด
            </h2>
            <p style="color: #4a4a3d; margin-bottom: 20px;">เลือกด้วงกว่างคุณภาพดี จากฟาร์มของเรา</p>
            
            <div class="products-grid" id="productsGrid">
                <!-- Products will be inserted here -->
            </div>
        </div>
    </div>

    <div class="toast" id="toast">
        <i class="fas fa-check-circle" style="font-size: 1.2rem;"></i>
        <span id="toastMessage">เพิ่มสินค้าลงตะกร้าแล้ว!</span>
    </div>

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
        const products = [
            { id: 1,  name: 'ด้วงกว่าง',              price: 150,  image: 'img/1.jpg',  badge: false },
            { id: 2,  name: 'ด้วงกว่าง 5 เขา',         price: 4550, image: 'img/2.jpg',  badge: true  },
            { id: 3,  name: 'ด้วงกว่างคอเคซัส',        price: 1050, image: 'img/3.jpg',  badge: false },
            { id: 4,  name: 'ด้วงกว่างคอเคซัส1',       price: 1150, image: 'img/4.jpg',  badge: false },
            { id: 5,  name: 'ด้วงกว่างญี่ปุ่น',        price: 250,  image: 'img/5.jpg',  badge: false },
            { id: 6,  name: 'ด้วงกว่างเฮอร์คิวลิส',   price: 1500, image: 'img/6.jpg',  badge: true  },
            { id: 7,  name: 'ด้วงกว่างทิทิอุส',        price: 350,  image: 'img/7.jpg',  badge: false },
            { id: 8,  name: 'ด้วงกว่างโกไลแอท',        price: 750,  image: 'img/8.jpg',  badge: false },
            { id: 10, name: 'ด้วงกว่างแรด',             price: 150,  image: 'img/10.jpg', badge: false },
            { id: 15, name: 'ด้วงกว่างซางหูกระต่าย',   price: 1500, image: 'img/15.jpg', badge: false },
            { id: 14, name: 'ด้วงกว่างขาวแกรน',        price: 1500, image: 'img/14.jpg', badge: false },
            { id: 12, name: 'ด้วงกว่างเนปจูน',         price: 1500, image: 'img/12.jpg', badge: true  }
        ];

        let cart = [];
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

        // ── Modal ──────────────────────────────────────────
        function showLoginModal() {
            document.getElementById('loginModal').classList.add('show');
        }
        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('show');
        }
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) closeLoginModal();
        });

        // ── Render สินค้า ──────────────────────────────────
        function renderProducts() {
            const grid = document.getElementById('productsGrid');
            const btnLabel = isLoggedIn
                ? '<i class="fas fa-cart-plus"></i> เพิ่มลงตะกร้า'
                : '<i class="fas fa-lock"></i> เข้าสู่ระบบเพื่อซื้อ';
            const btnClass = isLoggedIn
                ? 'add-to-cart-btn'
                : 'add-to-cart-btn locked';

            grid.innerHTML = products.map(product => `
                <div class="product-card">
                    ${product.badge ? '<div class="badge"><i class="fas fa-fire"></i> ขายดี</div>' : ''}
                    <img src="${product.image}" alt="${product.name}" class="product-image"
                         onerror="this.src='img/placeholder.jpg'">
                    <div class="product-info">
                        <div class="product-name">${product.name}</div>
                        <div class="product-price">฿${product.price.toLocaleString()}</div>
                        <button class="${btnClass}" onclick="addToCart(${product.id})">
                            ${btnLabel}
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // ── เพิ่มลงตะกร้า ──────────────────────────────────
        async function addToCart(productId) {
            if (!isLoggedIn) {
                showLoginModal();
                return;
            }
            const product = products.find(p => p.id === productId);
            try {
                const response = await fetch('cartapi.php?action=add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        image: product.image,
                        quantity: 1
                    })
                });
                const data = await response.json();
                if (data.success) {
                    await loadCartFromDB();
                    showToast(`เพิ่ม ${product.name} ลงตะกร้าแล้ว!`);
                } else {
                    alert(data.message || 'เกิดข้อผิดพลาด');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('ไม่สามารถเพิ่มสินค้าได้');
            }
        }

        // ── โหลดตะกร้าจาก DB ───────────────────────────────
        async function loadCartFromDB() {
            try {
                const response = await fetch('cartapi.php?action=get');
                const data = await response.json();
                if (data.success && data.cart) {
                    cart = data.cart;
                    updateCartBadge();
                }
            } catch (error) {
                console.error('Error loading cart:', error);
            }
        }

        function updateCartBadge() {
            const total = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cartBadge').textContent = total;
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function viewCart() {
            window.location.href = 'cart.php';
        }

        // ── โหลดเมื่อเปิดหน้า ───────────────────────────────
        window.addEventListener('DOMContentLoaded', async () => {
            if (isLoggedIn) {
                await loadCartFromDB();
            }
            renderProducts();
        });
    </script>
</body>
</html>