<?php 
session_start();
include 'connect.php';

// ตรวจสอบว่าล็อกอินหรือไม่
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า | ด้วงกว่าง พาเพลิน</title>
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
            background: linear-gradient(135deg, #f5f1e8 0%, #e8dcc4 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            background: linear-gradient(135deg, #7a9871 0%, #5d7a54 100%);
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: white;
            font-size: 1.8rem;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-btn:hover {
            background: white;
            color: #7a9871;
        }

        .cart-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #6d5947;
        }

        .empty-cart i {
            font-size: 5rem;
            color: #d4c9b0;
            margin-bottom: 20px;
        }

        .shop-btn {
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .cart-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            border: 2px solid #d4c9b0;
            border-radius: 12px;
            margin-bottom: 15px;
            background: white;
        }

        .item-image {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-details h3 {
            color: #7a9871;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .item-details p {
            color: #6d5947;
            margin: 5px 0;
        }

        .item-price {
            color: #c67b5c;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .item-controls {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-control button {
            width: 32px;
            height: 32px;
            border: 2px solid #7a9871;
            background: white;
            color: #7a9871;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity-control button:hover {
            background: #7a9871;
            color: white;
        }

        .remove-btn {
            background: #f56565;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background: #c53030;
        }

        .cart-summary {
            background: linear-gradient(135deg, #7a9871 0%, #5d7a54 100%);
            padding: 25px;
            border-radius: 12px;
            color: white;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .summary-row.total {
            font-size: 1.5rem;
            font-weight: bold;
            padding-top: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            margin-top: 15px;
        }

        .checkout-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }

        .clear-btn {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: white;
            border: 2px solid white;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-prompt {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .login-prompt a {
            color: #7a9871;
            font-weight: 600;
            text-decoration: none;
        }

        /* Modal Login Prompt */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(3px);
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 20px;
            padding: 40px 35px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            animation: popIn 0.3s ease;
        }

        @keyframes popIn {
            from { transform: scale(0.85); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }

        .modal-icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
        }

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
            box-shadow: 0 6px 18px rgba(93, 122, 84, 0.4);
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
            box-shadow: 0 6px 18px rgba(109, 89, 71, 0.4);
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

        .modal-btn-cancel:hover {
            color: #666;
        }

        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
            }
            .item-image {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-shopping-cart"></i> ตะกร้าสินค้า</h1>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> กลับหน้าแรก
            </a>
        </header>

        <?php if (!$isLoggedIn): ?>
        <div class="login-prompt">
            <i class="fas fa-info-circle"></i> 
            กรุณา <a href="login.php">เข้าสู่ระบบ</a> เพื่อบันทึกตะกร้าสินค้าของคุณ
        </div>
        <?php endif; ?>

        <div class="cart-section">
            <div id="cartItems"></div>
        </div>

        <div id="cartSummary"></div>
    </div>

    <!-- Modal: กรุณาเข้าสู่ระบบ -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <div class="modal-icon">🔒</div>
            <h2>กรุณาเข้าสู่ระบบก่อน</h2>
            <p>เพื่อดำเนินการสั่งซื้อสินค้า<br>กรุณาเข้าสู่ระบบหรือสมัครสมาชิกก่อนนะครับ</p>
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
        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;

        async function loadCart() {
            let cart = [];

            if (isLoggedIn) {
                // ดึงข้อมูลจากฐานข้อมูล
                try {
                    const response = await fetch('cartapi.php?action=get');
                    const data = await response.json();
                    
                    console.log('API Response:', data);
                    
                    if (data.success && data.cart) {
                        cart = data.cart;
                    }
                } catch (error) {
                    console.error('Error loading cart:', error);
                }
            } else {
                // ดึงข้อมูลจาก localStorage
                const cartData = localStorage.getItem('beetleCart');
                cart = cartData ? JSON.parse(cartData) : [];
            }
            
            console.log('Cart data:', cart);
            renderCart(cart);
        }

        function renderCart(cart) {
            const itemsDiv = document.getElementById('cartItems');
            const summaryDiv = document.getElementById('cartSummary');

            if (!cart || cart.length === 0) {
                itemsDiv.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h2>ตะกร้าสินค้าว่างเปล่า</h2>
                        <p>คุณยังไม่มีสินค้าในตะกร้า</p>
                        <a href="products.php" class="shop-btn">เลือกซื้อสินค้า</a>
                    </div>
                `;
                summaryDiv.innerHTML = '';
                return;
            }

            let html = '';
            let subtotal = 0;

            cart.forEach(item => {
                const total = item.price * item.quantity;
                subtotal += total;

                html += `
                    <div class="cart-item">
                        <img src="${item.image}" class="item-image" onerror="this.src='img/placeholder.jpg'">
                        <div class="item-details">
                            <h3>${item.name}</h3>
                            <p>ราคา: ${item.price.toLocaleString()} บาท</p>
                            <p class="item-price">รวม: ${total.toLocaleString()} บาท</p>
                        </div>
                        <div class="item-controls">
                            <div class="quantity-control">
                                <button onclick="updateQty(${item.id}, ${item.quantity - 1})">-</button>
                                <span>${item.quantity}</span>
                                <button onclick="updateQty(${item.id}, ${item.quantity + 1})">+</button>
                            </div>
                            <button class="remove-btn" onclick="removeItem(${item.id})">
                                <i class="fas fa-trash"></i> ลบ
                            </button>
                        </div>
                    </div>
                `;
            });

            itemsDiv.innerHTML = html;

            const shipping = subtotal >= 1000 ? 0 : 50;
            const total = subtotal + shipping;

            summaryDiv.innerHTML = `
                <div class="cart-section">
                    <div class="cart-summary">
                        <div class="summary-row">
                            <span>ราคาสินค้า (${cart.length} รายการ)</span>
                            <span>${subtotal.toLocaleString()} บาท</span>
                        </div>
                        <div class="summary-row">
                            <span>ค่าจัดส่ง ${subtotal >= 1000 ? '(ฟรี!)' : ''}</span>
                            <span>${shipping.toLocaleString()} บาท</span>
                        </div>
                        <div class="summary-row total">
                            <span>ยอดรวมทั้งหมด</span>
                            <span>${total.toLocaleString()} บาท</span>
                        </div>
                        <button class="checkout-btn" onclick="checkout()">
                            <i class="fas fa-check-circle"></i> ดำเนินการสั่งซื้อ
                        </button>
                        <button class="clear-btn" onclick="clearCart()">
                            <i class="fas fa-trash-alt"></i> ล้างตะกร้า
                        </button>
                    </div>
                </div>
            `;
        }

        async function updateQty(productId, newQty) {
            if (isLoggedIn) {
                // อัพเดทในฐานข้อมูล
                try {
                    const response = await fetch('cartapi.php?action=update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: productId,
                            quantity: newQty
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        loadCart();
                    }
                } catch (error) {
                    console.error('Error updating cart:', error);
                }
            } else {
                // อัพเดทใน localStorage
                const cartData = localStorage.getItem('beetleCart');
                let cart = cartData ? JSON.parse(cartData) : [];
                
                const itemIndex = cart.findIndex(item => item.id === productId);
                
                if (itemIndex !== -1) {
                    if (newQty <= 0) {
                        cart.splice(itemIndex, 1);
                    } else {
                        cart[itemIndex].quantity = newQty;
                    }
                }
                
                localStorage.setItem('beetleCart', JSON.stringify(cart));
                loadCart();
            }
        }

        async function removeItem(productId) {
            if (confirm('ต้องการลบสินค้านี้?')) {
                if (isLoggedIn) {
                    // ลบจากฐานข้อมูล
                    try {
                        const response = await fetch('cartapi.php?action=remove', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: productId })
                        });
                        const data = await response.json();
                        if (data.success) {
                            loadCart();
                        }
                    } catch (error) {
                        console.error('Error removing item:', error);
                    }
                } else {
                    // ลบจาก localStorage
                    const cartData = localStorage.getItem('beetleCart');
                    let cart = cartData ? JSON.parse(cartData) : [];
                    cart = cart.filter(item => item.id !== productId);
                    localStorage.setItem('beetleCart', JSON.stringify(cart));
                    loadCart();
                }
            }
        }

        async function clearCart() {
            if (confirm('ต้องการล้างสินค้าทั้งหมด?')) {
                if (isLoggedIn) {
                    // ล้างในฐานข้อมูล
                    try {
                        const response = await fetch('cartapi.php?action=clear', {
                            method: 'POST'
                        });
                        const data = await response.json();
                        if (data.success) {
                            loadCart();
                        }
                    } catch (error) {
                        console.error('Error clearing cart:', error);
                    }
                } else {
                    localStorage.removeItem('beetleCart');
                    loadCart();
                }
            }
        }

        function checkout() {
            if (!isLoggedIn) {
                document.getElementById('loginModal').classList.add('show');
                return;
            }
            window.location.href = 'checkout.php';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('show');
        }

        // ปิด modal เมื่อคลิกพื้นหลัง
        document.getElementById('loginModal').addEventListener('click', function(e) {
            if (e.target === this) closeLoginModal();
        });

        // โหลดตะกร้าเมื่อเปิดหน้า
        window.addEventListener('DOMContentLoaded', loadCart);
    </script>
</body>
</html>