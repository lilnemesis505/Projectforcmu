<?php
// (ไฟล์นี้จะถูกเรียกโดยไฟล์อื่นที่ Start Session แล้ว)

if (isset($_SESSION['flash_message'])) {
    
    // 1. ดึงข้อมูล Noti ออกมาจาก Session
    $flash = $_SESSION['flash_message'];
    
    // 2. กำหนดค่า Default
    $alert_class = 'alert-info'; // สีฟ้า (ค่าเริ่มต้น)
    $message = $flash['message'] ?? 'มีบางอย่างเกิดขึ้น...';

    // 3. เปลี่ยนสีตาม Type ที่ส่งมา
    if (isset($flash['type'])) {
        if ($flash['type'] === 'success') {
            $alert_class = 'alert-success'; // สีเขียว
        } elseif ($flash['type'] === 'error') {
            $alert_class = 'alert-danger';  // สีแดง
        } elseif ($flash['type'] === 'warning') {
            $alert_class = 'alert-warning'; // สีเหลือง
        }
    }
    
    // 4. แสดงผล Noti (แบบ Alert ที่ปิดได้)
    echo '<div class="alert ' . $alert_class . ' alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($message);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    
    // 5. (สำคัญ) ลบ Message ออกจาก Session ทันที
    unset($_SESSION['flash_message']);
}
?>