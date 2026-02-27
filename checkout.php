<?php
session_start();
include 'connect.php';

// ตรวจสอบว่าล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน | ด้วงกว่าง พาเพลิน</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-btn-top {
            background: white;
            color: #7a9871;
            border: 2px solid #7a9871;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back-btn-top:hover {
            background: #7a9871;
            color: white;
        }

        .checkout-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .checkout-header h1 {
            color: #7a9871;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .checkout-header p {
            color: #6d5947;
            font-size: 1rem;
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            color: #7a9871;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #3d3d3d;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #d4c9b0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #7a9871;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .required {
            color: #e53e3e;
        }

        .cart-items-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .cart-item-mini {
            display: flex;
            gap: 15px;
            padding: 15px;
            border: 2px solid #d4c9b0;
            border-radius: 10px;
            margin-bottom: 10px;
            background: #f5f1e8;
        }

        .cart-item-mini img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h4 {
            color: #3d3d3d;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .cart-item-info p {
            color: #6d5947;
            font-size: 0.85rem;
        }

        .cart-item-price {
            text-align: right;
            color: #7a9871;
            font-weight: 700;
        }

        .empty-cart-message {
            text-align: center;
            padding: 40px 20px;
            color: #a0aec0;
        }

        .empty-cart-message i {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .summary-section {
            background: linear-gradient(135deg, #7a9871 0%, #5d7a54 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 1rem;
        }

        .summary-row.total {
            font-size: 1.5rem;
            font-weight: 700;
            padding-top: 15px;
            border-top: 2px solid rgba(255, 255, 255, 0.3);
            margin-top: 15px;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(72, 187, 120, 0.4);
        }

        .submit-btn:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .payment-option {
            position: relative;
        }

        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .payment-option label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 2px solid #d4c9b0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-option input[type="radio"]:checked + label {
            border-color: #7a9871;
            background: rgba(102, 126, 234, 0.1);
            font-weight: 600;
        }

        .payment-option label i {
            font-size: 1.2rem;
        }

        @media (max-width: 968px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .payment-methods {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="cart.php" class="back-btn-top">
            <i class="fas fa-arrow-left"></i> กลับไปตะกร้าสินค้า
        </a>

        <div class="checkout-header">
            <h1><i class="fas fa-lock"></i> ชำระเงิน</h1>
            <p>กรุณากรอกข้อมูลสำหรับจัดส่งสินค้า</p>
        </div>

        <form id="checkoutForm">
            <div class="checkout-content">
                <!-- ข้อมูลผู้รับสินค้า -->
                <div class="section">
                    <h2><i class="fas fa-user"></i> ข้อมูลผู้รับสินค้า</h2>
                    
                    <div class="form-group">
                        <label>ชื่อ-นามสกุล <span class="required">*</span></label>
                        <input type="text" name="name" id="name" placeholder="กรอกชื่อ-นามสกุล" value="<?php echo htmlspecialchars($user_name); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>เบอร์โทรศัพท์ <span class="required">*</span></label>
                        <input type="tel" name="phone" id="phone" placeholder="08X-XXX-XXXX" required>
                    </div>

                    <div class="form-group">
                        <label>ที่อยู่สำหรับจัดส่ง <span class="required">*</span></label>
                        <textarea name="address" id="address" placeholder="กรอกที่อยู่ที่ต้องการให้จัดส่งสินค้า" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>วิธีการชำระเงิน <span class="required">*</span></label>
                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" name="payment_method" id="bank_transfer" value="bank_transfer" checked>
                                <label for="bank_transfer">
                                    <i class="fas fa-university"></i> โอนเงินผ่านธนาคาร
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="payment_method" id="promptpay" value="promptpay">
                                <label for="promptpay">
                                    <i class="fas fa-qrcode"></i> พร้อมเพย์
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="payment_method" id="cod" value="cash_on_delivery">
                                <label for="cod">
                                    <i class="fas fa-money-bill-wave"></i> เก็บเงินปลายทาง
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="payment_method" id="credit_card" value="credit_card">
                                <label for="credit_card">
                                    <i class="fas fa-credit-card"></i> บัตรเครดิต
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>หมายเหตุเพิ่มเติม</label>
                        <textarea name="notes" id="notes" placeholder="ระบุหมายเหตุเพิ่มเติม (ถ้ามี)"></textarea>
                    </div>
                </div>

                <!-- สรุปคำสั่งซื้อ -->
                <div class="section">
                    <h2><i class="fas fa-clipboard-list"></i> สรุปคำสั่งซื้อ</h2>
                    
                    <div class="cart-items-list" id="cartItemsList">
                        <div class="empty-cart-message">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>กำลังโหลดข้อมูล...</p>
                        </div>
                    </div>

                    <div class="summary-section">
                        <div class="summary-row">
                            <span>ราคาสินค้า (<span id="itemCount">0</span> รายการ)</span>
                            <span id="subtotal">0 บาท</span>
                        </div>
                        <div class="summary-row">
                            <span>ค่าจัดส่ง <span id="shippingNote"></span></span>
                            <span id="shippingFee">0 บาท</span>
                        </div>
                        <div class="summary-row total">
                            <span>ยอดรวมทั้งหมด</span>
                            <span id="totalAmount">0 บาท</span>
                        </div>
                        <button type="submit" class="submit-btn" id="submitBtn">
                            <i class="fas fa-check-circle"></i> ดำเนินการสั่งซื้อ
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let cart = [];
        let subtotal = 0;
        let shippingFee = 0;
        let total = 0;

        // โหลดข้อมูลตะกร้า
        async function loadCart() {
            try {
                const response = await fetch('cartapi.php?action=get');
                const data = await response.json();
                
                console.log('Cart API Response:', data);
                
                if (data.success && data.cart && data.cart.length > 0) {
                    cart = data.cart;
                    renderCartItems();
                    calculateTotal();
                } else {
                    showEmptyCart();
                }
            } catch (error) {
                console.error('Error loading cart:', error);
                showEmptyCart();
            }
        }

        function renderCartItems() {
            const listDiv = document.getElementById('cartItemsList');
            
            let html = '';
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                html += `
                    <div class="cart-item-mini">
                        <img src="${item.image}" alt="${item.name}" onerror="this.src='img/placeholder.jpg'">
                        <div class="cart-item-info">
                            <h4>${item.name}</h4>
                            <p>฿${item.price.toLocaleString()} x ${item.quantity}</p>
                        </div>
                        <div class="cart-item-price">
                            ฿${itemTotal.toLocaleString()}
                        </div>
                    </div>
                `;
            });
            
            listDiv.innerHTML = html;
        }

        function calculateTotal() {
            subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            // คำนวณค่าจัดส่ง (ฟรีถ้าซื้อมากกว่า 1000 บาท)
            shippingFee = subtotal >= 1000 ? 0 : 50;
            total = subtotal + shippingFee;
            
            // อัพเดท UI
            document.getElementById('itemCount').textContent = cart.length;
            document.getElementById('subtotal').textContent = subtotal.toLocaleString() + ' บาท';
            document.getElementById('shippingFee').textContent = shippingFee.toLocaleString() + ' บาท';
            document.getElementById('totalAmount').textContent = total.toLocaleString() + ' บาท';
            
            if (shippingFee === 0) {
                document.getElementById('shippingNote').textContent = '(ฟรี!)';
            } else {
                document.getElementById('shippingNote').textContent = '';
            }
        }

        function showEmptyCart() {
            const listDiv = document.getElementById('cartItemsList');
            listDiv.innerHTML = `
                <div class="empty-cart-message">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>ไม่มีสินค้าในตะกร้า</h3>
                    <p>กรุณาเพิ่มสินค้าก่อนทำการสั่งซื้อ</p>
                </div>
            `;
            
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').textContent = 'ไม่สามารถสั่งซื้อได้';
        }

        // จัดการ Form Submit
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (cart.length === 0) {
                alert('ตะกร้าสินค้าว่างเปล่า');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังดำเนินการ...';
            
            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                notes: document.getElementById('notes').value,
                cart: cart,
                shipping_fee: shippingFee,
                subtotal: subtotal,
                total: total
            };
            
            try {
                const response = await fetch('checkout_updated.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('สั่งซื้อสำเร็จ! เลขที่คำสั่งซื้อ: ' + data.order_id);
                    window.location.href = 'order_success.php?order_id=' + data.order_id;
                } else {
                    alert('เกิดข้อผิดพลาด: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> ดำเนินการสั่งซื้อ';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการส่งข้อมูล กรุณาลองใหม่อีกครั้ง');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> ดำเนินการสั่งซื้อ';
            }
        });

        // โหลดข้อมูลเมื่อเริ่มต้น
        window.addEventListener('DOMContentLoaded', loadCart);
    </script>
</body>
</html>