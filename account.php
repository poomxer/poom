<?php if(isset($_SESSION['user_name'])): ?>
<div class="account-menu">
    <h2>บัญชีของฉัน</h2>
    <ul>
        <li><a href="order_history.php"><i class="fas fa-history"></i> ประวัติการสั่งซื้อ</a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> ข้อมูลส่วนตัว</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a></li>
    </ul>
</div>
<?php endif; ?>