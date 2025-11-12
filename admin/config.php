<?php
// 1. เริ่ม Session (ถ้ายังไม่เริ่ม)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. (สำคัญ) กำหนด Path หลัก (สำหรับ PHP require)
define('ADMIN_ROOT', __DIR__);
define('PROJECT_ROOT', dirname(ADMIN_ROOT));

// --- (สำคัญมาก) 3. กำหนด Base URL สำหรับลิงก์ (href) ---

// [!!! แก้ไข !!!]
// (เราต้องแปลง \ เป็น / ให้เหมือนกันทั้งคู่ *ก่อน* ที่จะ replace)
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$admin_root_path = str_replace('\\', '/', ADMIN_ROOT);

// (หา Path ที่เป็น URL)
$base_path = str_replace($doc_root, '', $admin_root_path);


// (กันเหนียว) ถ้าอยู่ root สุดๆ
if (empty($base_path)) $base_path = '/';

// (สำคัญ) ต้องมี / ปิดท้ายเสมอ
if (substr($base_path, -1) != '/') $base_path .= '/';

define('ADMIN_BASE_URL', $base_path); // (ผลลัพธ์ควรเป็น /Projectforcmu/admin/)
define('PROJECT_URL', dirname($base_path) . '/'); // (ผลลัพธ์ควรเป็น /Projectforcmu/)

// 4. (สำคัญ) เรียกไฟล์เชื่อมต่อ DB
require_once PROJECT_ROOT . '/include/db.php';

// 5. (ถูกต้อง) สร้างฟังก์ชัน Helper สำหรับเรียก Noti
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

?>