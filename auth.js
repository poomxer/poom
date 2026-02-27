document.addEventListener('DOMContentLoaded', function() {
    const authLink = document.getElementById('auth-link');
    const isLoggedIn = localStorage.getItem('isLoggedIn'); // เช็คสถานะ

    if (isLoggedIn === 'true') {
        // ถ้าล็อกอินแล้ว ให้เปลี่ยนเป็นปุ่ม Logout
        authLink.textContent = 'ออกจากระบบ';
        authLink.href = '#'; // ไม่ต้องไปหน้าไหน
        authLink.style.color = 'red'; // แต่งสีให้ต่างออกไป

        authLink.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('คุณต้องการออกจากระบบใช่หรือไม่?')) {
                localStorage.removeItem('isLoggedIn');
                localStorage.removeItem('userEmail'); // ลบข้อมูลที่จำไว้ (ถ้ามี)
                alert('ออกจากระบบเรียบร้อย');
                window.location.reload(); // รีเฟรชหน้าเพื่อกลับเป็นสถานะเดิม
            }
        });
    }
});