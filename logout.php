<?php
session_start();

// ลบ session ทั้งหมด
session_unset();
session_destroy();

// ลบ cookie (ถ้ามี)
if (isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', '', time() - 3600, '/');
}

// Redirect กลับหน้าแรกพร้อมข้อความและลบ localStorage
echo "<script>
    // ลบตะกร้าจาก localStorage เมื่อออกจากระบบ
    localStorage.removeItem('cart');
    
    alert('ออกจากระบบเรียบร้อยแล้ว');
    window.location.href = 'index.php';
</script>";
exit();
?>