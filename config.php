<?php
// (ไฟล์นี้จะอยู่ที่ Projectforcmu/config.php)

// 1. (สำคัญ) ปิดการแสดง Error บน Host จริง
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// 2. (สำคัญ) กำหนด Path หลัก (สำหรับ PHP require)
define('PROJECT_ROOT', __DIR__);
define('ADMIN_ROOT', PROJECT_ROOT . '/admin');

// 3. (สำคัญ) กำหนด Base URL สำหรับลิงก์ (href)
// (ใช้ Logic แก้ Bug \ และ / ของ Windows ที่เราเคยทำ)
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$project_root_path = str_replace('\\', '/', PROJECT_ROOT);

// (หา Path ที่เป็น URL)
$base_url = str_replace($doc_root, '', $project_root_path);

// (กันเหนียว) ถ้าอยู่ root สุดๆ หรือ Path ว่าง
if (empty($base_url)) $base_url = '/';

// (สำคัญ) ต้องมี / ปิดท้ายเสมอ
if (substr($base_url, -1) != '/') $base_url .= '/';

define('PROJECT_URL', $base_url); // (ผลลัพธ์จะเป็น /Projectforcmu/ หรือ /)
define('ADMIN_BASE_URL', PROJECT_URL . 'admin/'); // (ผลลัพธ์จะเป็น /Projectforcmu/admin/ หรือ /admin/)
?>