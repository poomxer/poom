<?php 
session_start();

// ข้อมูลสินค้าทั้งหมด
$products = [
    'p1' => [
        'id' => 1,
        'name' => 'ด้วงกว่าง',
        'price' => 150,
        'stock' => 20,
        'image' => 'img/1.jpg',
        'description' => 'ด้วงกว่างพันธุ์ดี คุณภาพเยี่ยม เหมาะสำหรับผู้เริ่มต้นเลี้ยง ขนาดกลาง แข็งแรง มีสีสันสวยงาม',
        'details' => [
            'ขนาด' => 'กลาง (4-5 ซม.)',
            'อายุ' => '2-3 เดือน',
            'เพศ' => 'ตัวผู้และตัวเมีย',
            'แหล่งที่มา' => 'ฟาร์มในประเทศ',
            'การดูแล' => 'ง่าย เหมาะสำหรับมือใหม่'
        ]
    ],
    'p2' => [
        'id' => 2,
        'name' => 'ด้วงกว่าง 5 เขา',
        'price' => 4550,
        'stock' => 17,
        'image' => 'img/2.jpg',
        'description' => 'ด้วงกว่าง 5 เขา สายพันธุ์หายาก มีเขา 5 แฉก ขนาดใหญ่ สวยงามมาก เหมาะสำหรับนักสะสม',
        'details' => [
            'ขนาด' => 'ใหญ่ (8-10 ซม.)',
            'อายุ' => '4-6 เดือน',
            'เพศ' => 'ตัวผู้',
            'แหล่งที่มา' => 'นำเข้าจากญี่ปุ่น',
            'การดูแล' => 'ต้องมีประสบการณ์'
        ]
    ],
    'p3' => [
        'id' => 3,
        'name' => 'ด้วงกว่างคอเคซัส',
        'price' => 1050,
        'stock' => 30,
        'image' => 'img/3.jpg',
        'description' => 'ด้วงกว่างคอเคซัส พันธุ์ดุร้าย มีเขาสามแฉกที่แข็งแรง สีดำเข้ม ขนาดใหญ่ เหมาะสำหรับการแข่งขัน',
        'details' => [
            'ขนาด' => 'ใหญ่ (7-9 ซม.)',
            'อายุ' => '3-5 เดือน',
            'เพศ' => 'ตัวผู้และตัวเมีย',
            'แหล่งที่มา' => 'ฟาร์มในประเทศ',
            'การดูแล' => 'ปานกลาง'
        ]
    ],
    // เพิ่มสินค้าอื่นๆ ตามต้องการ...
];

// รับ ID สินค้าจาก URL
$product_id = isset($_GET['id']) ? $_GET['id'] : '';

// ตรวจสอบว่ามีสินค้านี้หรือไม่
if (!isset($products[$product_id])) {
    header('Location: index.php');
    exit();
}

$product = $products[$product_id];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> | ร้านด้วงกว่าง พาเพลิน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        .navbar {
            background: linear-gradient(135deg, #5d7a54 0%, #7a9871 100%);
            padding: 1.5rem 0;
            box-shadow: 0 4px 20px rgba(93, 122, 84, 0.3);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: white;
            color: #7a9871;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
        }

        .product-detail {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
        }

        .product-image {
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
        }

        .badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            font-weight: 600;
        }

        .stock-badge {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
        }

        .product-info h1 {
            font-size: 2.5rem;
            color: #7a9871;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 2rem;
            color: #e74c3c;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .description {
            background: #faf8f4;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            line-height: 1.8;
            color: #555;
        }

        .details-table {
            width: 100%;
            margin-bottom: 2rem;
        }

        .details-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #d4c9b0;
        }

        .details-table td:first-child {
            font-weight: 600;
            color: #7a9871;
            width: 35%;
        }

        .quantity-selector {
            margin-bottom: 2rem;
        }

        .quantity-selector label {
            font-weight: 600;
            margin-bottom: 0.8rem;
            display: block;
            color: #3d3d3d;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .qty-btn {
            width: 45px;
            height: 45px;
            border: 2px solid #7a9871;
            background: white;
            color: #7a9871;
            font-size: 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: #7a9871;
            color: white;
        }

        .qty-input {
            width: 80px;
            height: 45px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
            border: 2px solid #d4c9b0;
            border-radius: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            flex: 1;
            padding: 1.2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #7a9871, #5d7a54);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(39, 174, 96, 0.4);
        }

        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
                padding: 2rem;
                gap: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <span>🪲</span>
                <span>ด้วงกว่าง พาเพลิน</span>
            </a>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>กลับไปหน้าหลัก</span>
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="product-detail">
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <div class="badge">ขายดี</div>
                <div class="stock-badge">
                    <i class="fas fa-check-circle"></i> เหลือ <?php echo $product['stock']; ?> ตัว
                </div>
            </div>

            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="price"><?php echo number_format($product['price']); ?> บาท</div>

                <div class="description">
                    <i class="fas fa-info-circle"></i> 
                    <?php echo htmlspecialchars($product['description']); ?>
                </div>

                <table class="details-table">
                    <?php foreach ($product['details'] as $key => $value): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($key); ?></td>
                        <td><?php echo htmlspecialchars($value); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <div class="quantity-selector">
                    <label>จำนวน:</label>
                    <div class="quantity-controls">
                        <button class="qty-btn" onclick="decreaseQty()">-</button>
                        <input type="number" class="qty-input" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                        <button class="qty-btn" onclick="increaseQty()">+</button>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="addToCart()">
                        <i class="fas fa-shopping-cart"></i>
                        เพิ่มลงตะกร้า
                    </button>
                    <button class="btn btn-secondary" onclick="buyNow()">
                        <i class="fas fa-bolt"></i>
                        ซื้อเลย
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const maxStock = <?php echo $product['stock']; ?>;
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        
        function increaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) < maxStock) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        async function addToCart() {
            <?php if(!isset($_SESSION['user_id'])): ?>
                if(confirm('กรุณาเข้าสู่ระบบก่อนเพิ่มสินค้าลงตะกร้า\nคุณต้องการไปหน้าเข้าสู่ระบบหรือไม่?')) {
                    window.location.href = 'login.php';
                }
                return;
            <?php endif; ?>

            const quantity = parseInt(document.getElementById('quantity').value);
            const product = {
                id: <?php echo $product['id']; ?>,
                name: '<?php echo addslashes($product['name']); ?>',
                price: <?php echo $product['price']; ?>,
                image: '<?php echo $product['image']; ?>',
                stock: <?php echo $product['stock']; ?>,
                quantity: quantity
            };

            if (isLoggedIn) {
                // ถ้า login แล้ว ใช้ API
                try {
                    const response = await fetch('cartapi.php?action=add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(product)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        alert('เพิ่ม ' + product.name + ' จำนวน ' + quantity + ' ตัว ลงตะกร้าแล้ว!');
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถเพิ่มสินค้าได้'));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการเพิ่มสินค้า');
                }
            } else {
                // ถ้ายังไม่ login ใช้ localStorage
                let cart = localStorage.getItem('beetleCart');
                let items = cart ? JSON.parse(cart) : [];
                
                let found = false;
                for (let i = 0; i < items.length; i++) {
                    if (items[i].id == product.id) {
                        items[i].quantity += quantity;
                        found = true;
                        break;
                    }
                }
                
                if (!found) {
                    items.push(product);
                }
                
                localStorage.setItem('beetleCart', JSON.stringify(items));
                alert('เพิ่ม ' + product.name + ' จำนวน ' + quantity + ' ตัว ลงตะกร้าแล้ว!');
            }
        }

        async function buyNow() {
            <?php if(!isset($_SESSION['user_id'])): ?>
                if(confirm('กรุณาเข้าสู่ระบบก่อนซื้อสินค้า\nคุณต้องการไปหน้าเข้าสู่ระบบหรือไม่?')) {
                    window.location.href = 'login.php';
                }
                return;
            <?php endif; ?>

            await addToCart();
            // รอ 500ms ให้ข้อมูลบันทึกเสร็จก่อนไปหน้าตะกร้า
            setTimeout(() => {
                window.location.href = 'cart.php';
            }, 500);
        }
    </script>

</body>
</html>