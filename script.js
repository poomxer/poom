// โค้ดสำหรับสไลด์โชว์
let slides = document.querySelectorAll('.hero img');
let index = 0;
setInterval(() => {
    slides[index].classList.remove('active');
    index = (index + 1) % slides.length;
    slides[index].classList.add('active');
}, 3000);

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่ (ต้องส่งค่ามาจาก PHP)
let isUserLoggedIn = false; // จะถูก override จาก PHP

// ฟังก์ชันเพิ่มสินค้าลงตะกร้า
async function addToCart(productId, productName, productPrice, productImage) {
    const product = {
        id: productId,
        name: productName,
        price: productPrice,
        image: productImage,
        quantity: 1
    };

    if (isUserLoggedIn) {
        // ถ้า login แล้ว บันทึกลงฐานข้อมูล
        try {
            const response = await fetch('cart_api.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(product)
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert(`${productName} ถูกเพิ่มลงในตะกร้าแล้ว!`);
                updateCartCount();
            } else {
                if (data.message === 'Please login first') {
                    alert('กรุณาเข้าสู่ระบบก่อนเพิ่มสินค้าลงตะกร้า');
                    window.location.href = 'login.php';
                } else {
                    alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
                }
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    } else {
        // ถ้ายังไม่ login ใช้ localStorage
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existingProduct = cart.find(item => item.id === productId);

        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            cart.push(product);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        alert(`${productName} ถูกเพิ่มลงในตะกร้าแล้ว!`);
        updateCartCount();
    }
}

// ฟังก์ชันอัพเดทจำนวนสินค้าในไอคอนตะกร้า
async function updateCartCount() {
    let count = 0;
    
    if (isUserLoggedIn) {
        try {
            const response = await fetch('cart_api.php?action=get');
            const data = await response.json();
            if (data.success) {
                count = data.cart.reduce((sum, item) => sum + item.quantity, 0);
            }
        } catch (error) {
            console.error('Error getting cart count:', error);
        }
    } else {
        const cart = JSON.parse(localStorage.getItem('cart') || '[]');
        count = cart.reduce((sum, item) => sum + item.quantity, 0);
    }
    
    // อัพเดท badge ถ้ามี element
    const badge = document.getElementById('cartBadge');
    if (badge) {
        badge.textContent = count;
    }
}

// โค้ดทั้งหมดที่เกี่ยวข้องกับการจัดการกับ DOM
document.addEventListener('DOMContentLoaded', function() {
    // อัพเดทจำนวนตะกร้าเมื่อโหลดหน้า
    updateCartCount();

    // โค้ดสำหรับปุ่ม "ย้อนกลับ"
    const backButton = document.getElementById('backButton');
    if (backButton) {
        backButton.addEventListener('click', function() {
            history.back();
        });
    }

    // โค้ดสำหรับปุ่ม "เพิ่มลงตะกร้า" ที่มีอยู่แล้วในหน้า
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = parseInt(this.dataset.id);
            const productName = this.dataset.name;
            const productPrice = parseFloat(this.dataset.price);
            const productImage = this.dataset.image;

            addToCart(productId, productName, productPrice, productImage);
        });
    });
});

// ฟังก์ชัน เปิด/ปิด การมองเห็นรหัสผ่าน
function toggleView(inputId, icon) {
    const passwordInput = document.getElementById(inputId);
    
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

// ฟังก์ชันตรวจสอบว่ารหัสผ่านตรงกันหรือไม่ (ใส่ในตอนกดสมัคร)
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        const pass = document.getElementById('password').value;
        const confirmPass = document.getElementById('confirm_password').value;

        if (pass !== confirmPass) {
            e.preventDefault();
            alert("รหัสผ่านทั้งสองช่องไม่ตรงกัน กรุณาตรวจสอบอีกครั้ง!");
        }
    });
}