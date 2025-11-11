<?php
// 1. เริ่ม Session (ถ้ายังไม่เริ่ม)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. (สำคัญ) กำหนด Path หลัก (สำหรับ PHP require)
// __DIR__ คือ Path เต็มของโฟลเดอร์ที่ไฟล์นี้อยู่ (คือ .../PROJECTFORCMU/admin)
define('ADMIN_ROOT', __DIR__);

// PROJECT_ROOT คือ Path ที่อยู่นอก admin 1 ชั้น (คือ .../PROJECTFORCMU)
define('PROJECT_ROOT', dirname(ADMIN_ROOT)); // หรือใช้ __DIR__ . '/..'

// --- (สำคัญมาก) 3. กำหนด Base URL สำหรับลิงก์ (href) ---
// (หา Path ที่เป็น URL จาก C:\xampp\htdocs\Projectforcmu\admin)
$base_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', ADMIN_ROOT);
$base_path = str_replace('\\', '/', $base_path); // (แก้สำหรับ Windows)

// (กันเหนียว) ถ้าอยู่ root สุดๆ
if (empty($base_path)) $base_path = '/'; 

// (สำคัญ) ต้องมี / ปิดท้ายเสมอ
if (substr($base_path, -1) != '/') $base_path .= '/';

define('ADMIN_BASE_URL', $base_path); // (ผลลัพธ์ควรเป็น /Projectforcmu/admin/)
define('PROJECT_URL', dirname($base_path) . '/'); // (ผลลัพธ์ควรเป็น /Projectforcmu/)

// 4. (สำคัญ) เรียกไฟล์เชื่อมต่อ DB
// (ใช้ PROJECT_ROOT เพราะ db.php อยู่ที่ /include/ ด้านนอก)
require_once PROJECT_ROOT . '/include/db.php'; 

?>