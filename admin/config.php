<?php
// 1. เริ่ม Session (ถ้ายังไม่เริ่ม)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. (สำคัญ) เรียก Config หลัก (ตัวที่เราเพิ่งสร้าง)
// (dirname(__DIR__) จะชี้ไปที่ Root ของโปรเจกต์)
require_once dirname(__DIR__) . '/config.php';

// 3. (สำคัญ) เรียกไฟล์เชื่อมต่อ DB
// (ใช้ PROJECT_ROOT ที่ Config หลักสร้างไว้)
require_once PROJECT_ROOT . '/include/db.php';

// 4. (ถูกต้อง) สร้างฟังก์ชัน Helper สำหรับเรียก Noti
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}
?>