<?php
session_start();

// ลบ session ของ admin เท่านั้น
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_role']);

// Redirect กลับหน้า admin login
echo "<script>
    alert('ออกจากระบบผู้ดูแลเรียบร้อยแล้ว');
    window.location.href = 'admin_login.php';
</script>";
exit();
?>