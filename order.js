document.addEventListener('DOMContentLoaded', function() {
    const orderItemsList = document.getElementById('order-items');
    const orderTotalSpan = document.getElementById('order-total');

    function displayCart() {
        // แก้ไข: ใช้ 'beetleCart' แทน 'cart'
        const cart = JSON.parse(localStorage.getItem('beetleCart')) || [];
        let total = 0;
        orderItemsList.innerHTML = ''; 

        if (cart.length === 0) {
            orderItemsList.innerHTML = '<li style="text-align:center; padding:20px;">ไม่มีสินค้าในตะกร้า</li>';
            orderTotalSpan.textContent = '0';
            return;
        }

        cart.forEach((item, index) => {
            const li = document.createElement('li');
            li.style.display = 'flex';
            li.style.alignItems = 'center';
            li.style.gap = '15px';
            li.style.marginBottom = '15px';
            li.style.paddingBottom = '10px';
            li.style.borderBottom = '1px solid #eee';

            li.innerHTML = `
                <img src="${item.image}" alt="${item.name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                <div style="flex-grow: 1;">
                    <div style="font-weight: bold;">${item.name}</div>
                    <div>จำนวน: ${item.quantity} ชิ้น</div>
                </div>
                <div style="font-weight: bold; color: #27ae60;">${(item.price * item.quantity).toLocaleString()} ฿</div>
            `;
            
            orderItemsList.appendChild(li);
            total += item.price * item.quantity;
        });
        
        orderTotalSpan.textContent = total.toLocaleString();
    }

    displayCart();
    
    // ⭐ แก้ไขส่วนนี้ - ส่งข้อมูลจริงไปยัง checkout_updated.php
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // ดึงข้อมูลจากฟอร์ม
            const name = document.getElementById('customer-name')?.value || 
                        document.getElementById('customer_name')?.value || '';
            const phone = document.getElementById('customer-phone')?.value || 
                         document.getElementById('customer_phone')?.value || '';
            const address = document.getElementById('customer-address')?.value || 
                           document.getElementById('customer_address')?.value || '';
            const paymentMethod = document.querySelector('input[name="payment"]:checked')?.value || 
                                 document.getElementById('payment_method')?.value || '';
            const notes = document.getElementById('notes')?.value || '';
            
            // ดึงตะกร้าสินค้า
            const cart = JSON.parse(localStorage.getItem('beetleCart')) || [];
            
            // ตรวจสอบข้อมูล
            if (!name || !phone || !address) {
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                return;
            }
            
            if (!paymentMethod) {
                alert('กรุณาเลือกวิธีการชำระเงิน');
                return;
            }
            
            if (cart.length === 0) {
                alert('ไม่มีสินค้าในตะกร้า');
                return;
            }
            
            // เตรียมข้อมูลสำหรับส่ง
            const orderData = {
                name: name,
                phone: phone,
                address: address,
                payment_method: paymentMethod,
                notes: notes,
                cart: cart
            };
            
            console.log('Sending order data:', orderData);
            
            try {
                // ส่งข้อมูลไปยัง checkout_updated.php
                const response = await fetch('checkout_updated.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                console.log('Order result:', result);
                
                if (result.success) {
                    alert('สั่งซื้อสำเร็จ! หมายเลขคำสั่งซื้อ: ' + result.order_id);
                    localStorage.removeItem('beetleCart'); // ล้างตะกร้า
                    window.location.href = 'order_history.php'; // ไปหน้าประวัติการสั่งซื้อ
                } else {
                    alert('เกิดข้อผิดพลาด: ' + result.message);
                    console.error('Error details:', result);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการสั่งซื้อ กรุณาลองใหม่อีกครั้ง');
            }
        });
    }
    
    // ฟังก์ชันสำหรับสลับการแสดงผล โอนเงิน / เก็บเงินปลายทาง
    window.togglePayment = function(method) {
        const bankInfo = document.getElementById('bank-info');
        const slipSection = document.getElementById('slip-section');
        const slipInput = document.getElementById('slip-file');

        if (method === 'cod') {
            if (bankInfo) bankInfo.style.display = 'none';      // ซ่อนเลขบัญชี
            if (slipSection) slipSection.style.display = 'none';   // ซ่อนที่แนบสลิป
            if (slipInput) slipInput.removeAttribute('required'); // ไม่บังคับแนบรูป
        } else {
            if (bankInfo) bankInfo.style.display = 'block';     // โชว์เลขบัญชี
            if (slipSection) slipSection.style.display = 'block';  // โชว์ที่แนบสลิป
            if (slipInput) slipInput.setAttribute('required', 'required'); // บังคับแนบรูป
        }
    }
});