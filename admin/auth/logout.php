<?php
session_start();
require_once '../../include/db.php';

// 1. (ใหม่) ลบ "Remember Me" Cookie ออกจาก DB
if (isset($_COOKIE['remember_me'])) {
    list($selector, $validator) = explode(':', $_COOKIE['remember_me'], 2);
    
    $stmt = $pdo->prepare("DELETE FROM auth_tokens WHERE selector = ?");
    $stmt->execute([$selector]);
    
    // 2. ลบ Cookie ออกจากเครื่องผู้ใช้
    setcookie('remember_me', '', time() - 3600, '/');
}

// 3. ลบ Session
session_unset();
session_destroy();

// 4. กลับไปหน้า Login
header('Location: login.php');
exit;
?>