<?php
// [แก้ไข]
// ตรวจสอบก่อนว่า Session ยังไม่เริ่ม ค่อยสั่งเริ่ม
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. ตรวจสอบ Session (ถ้ามีคือจบ, ล็อกอินอยู่แล้ว)
if (isset($_SESSION['admin_id'])) {
    return; // จบการทำงาน, ไปต่อได้
}

// 2. ถ้าไม่มี Session, ตรวจสอบ Cookie "Remember Me"
if (!isset($_COOKIE['remember_me'])) {
    // ไม่มีทั้ง Session และ Cookie -> ไปหน้า Login
    
    // [ยืนยัน] Path นี้ถูกต้อง (อิงจาก URL ที่ /admin/)
    header('Location: auth/login.php'); 
    exit;
}


list($selector, $validator) = explode(':', $_COOKIE['remember_me'], 2);

if (empty($selector) || empty($validator)) {
    // [ยืนยัน] Path นี้ถูกต้อง
    header('Location: auth/login.php');
    exit;
}

// 4. ค้นหา Selector ใน DB (ใช้ $pdo จาก config.php)
$stmt = $pdo->prepare("SELECT * FROM auth_tokens WHERE selector = ? AND expires >= NOW()");
$stmt->execute([$selector]);
$token = $stmt->fetch();

if (!$token) {
    // ไม่พบ Selector หรือหมดอายุ
    setcookie('remember_me', '', time() - 3600, '/'); // ล้าง Cookie ทิ้ง
    
    // [ยืนยัน] Path นี้ถูกต้อง
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

    // [แก้ไข]
    // ควรตั้ง Session ให้ครบถ้วนตามที่โค้ดเก่าของคุณอาจจะเคยใช้
    // (ไฟล์ก่อนหน้านี้คุณใช้ 'admin_logged_in')
    $_SESSION['admin_logged_in'] = true; 
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
    
    // [ยืนยัน] Path นี้ถูกต้อง
    header('Location: auth/login.php');
    exit;
}
?>