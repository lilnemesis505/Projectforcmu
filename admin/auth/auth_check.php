<?php
session_start();

// 1. ตรวจสอบ Session (ถ้ามีคือจบ, ล็อกอินอยู่แล้ว)
if (isset($_SESSION['admin_id'])) {
    return; // จบการทำงาน, ไปต่อได้
}

// 2. ถ้าไม่มี Session, ตรวจสอบ Cookie "Remember Me"
if (!isset($_COOKIE['remember_me'])) {
    // ไม่มีทั้ง Session และ Cookie -> ไปหน้า Login
    
    // (แก้ไขจุดที่ 1) ต้องชี้ไปที่โฟลเดอร์ auth
    header('Location: auth/login.php'); 
    exit;
}

// 3. มี Cookie, พยายามยืนยันตัวตนจาก Cookie

// (แก้ไขจุดที่ 2) ต้องถอย 2 ขั้น (../../)
require_once __DIR__ . '/../../include/db.php'; 

list($selector, $validator) = explode(':', $_COOKIE['remember_me'], 2);

if (empty($selector) || empty($validator)) {
    // (แก้ไขจุดที่ 1)
    header('Location: auth/login.php');
    exit;
}

// 4. ค้นหา Selector ใน DB
$stmt = $pdo->prepare("SELECT * FROM auth_tokens WHERE selector = ? AND expires >= NOW()");
$stmt->execute([$selector]);
$token = $stmt->fetch();

if (!$token) {
    // ไม่พบ Selector หรือหมดอายุ
    setcookie('remember_me', '', time() - 3600, '/'); // ล้าง Cookie ทิ้ง
    
    // (แก้ไขจุดที่ 1)
    header('Location: auth/login.php');
    exit;
}

// 5. ตรวจสอบ Validator (Hashed)
if (password_verify($validator, $token['hashed_validator'])) {
    // สำเร็จ! สร้าง Session ให้
    session_regenerate_id(true);
    
    // ดึงข้อมูล Admin
    $stmt_user = $pdo->prepare("SELECT id, username FROM admin WHERE id = ?");
    $stmt_user->execute([$token['admin_id']]);
    $user = $stmt_user->fetch();

    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];

    // (Security: สร้าง Token ใหม่สำหรับ Cookie)
    $new_validator = bin2hex(random_bytes(32));
    $new_hashed_validator = password_hash($new_validator, PASSWORD_DEFAULT);
    $new_expires = time() + (86400 * 30); // 30 วัน

    $stmt_update = $pdo->prepare("UPDATE auth_tokens SET hashed_validator = ?, expires = ? WHERE selector = ?");
    $stmt_update->execute([$new_hashed_validator, date('Y-m-d H:i:s', $new_expires), $selector]);
    
    setcookie('remember_me', $selector . ':' . $new_validator, $new_expires, '/', '', false, true); // httpOnly

} else {
    // Hashed Validator ไม่ตรง (อาจถูกขโมย)
    // ลบ Token นี้ทิ้งจาก DB
    $stmt_delete = $pdo->prepare("DELETE FROM auth_tokens WHERE selector = ?");
    $stmt_delete->execute([$selector]);
    
    setcookie('remember_me', '', time() - 3600, '/');
    
    // (แก้ไขจุดที่ 1)
    header('Location: auth/login.php');
    exit;
}
?>