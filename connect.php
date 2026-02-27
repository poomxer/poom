<?php
// ไฟล์เชื่อมต่อฐานข้อมูล
// ตรวจสอบว่าข้อมูลตรงกับของคุณหรือไม่

$host = 'localhost';
$username = 'root';  // ชื่อผู้ใช้ MySQL ของคุณ
$password = '';      // รหัสผ่าน MySQL ของคุณ
$database = 'beetle_shop2';  // ชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = mysqli_connect($host, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ตั้งค่า charset เป็น UTF-8 เพื่อรองรับภาษาไทย
mysqli_set_charset($conn, "utf8mb4");
?>