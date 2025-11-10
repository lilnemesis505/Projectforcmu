<?php
// 1. (สำคัญ) ตรวจสอบสิทธิ์ก่อนทุกอย่าง
// (Path นี้ถูกต้อง เพราะไฟล์นี้จะถูกเรียกใช้จาก dashboard.php)
require_once 'auth/auth_check.php'; 

// 2. (เผื่อใช้) เรียก DB
// (Path นี้ถูกต้อง เพราะไฟล์นี้จะถูกเรียกใช้จาก dashboard.php)
require_once '../include/db.php'; 
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS พื้นฐานสำหรับ Admin Layout */
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .wrapper {
            display: flex;
            width: 100%;
            flex: 1; /* ทำให้ยืดเต็มความสูง */
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #212529; /* สี Dark เข้ม */
            color: #fff;
            transition: all 0.3s;
        }
        #sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 20px;
            font-size: 0.95rem;
        }
        #sidebar .nav-link:hover, 
        #sidebar .nav-link.active {
            color: #fff;
            background: #495057;
        }
        #content {
            width: 100%;
            padding: 25px;
            background: #f8f9fa; /* สีพื้นหลังอ่อน */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-gear-fill me-2"></i> Admin Panel
        </a>
        
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="bi bi-house-door-fill me-1"></i> กลับหน้าบ้าน
                </a>
            </li>
        </ul>
        <div class="ms-auto">
            <span class="navbar-text me-3 text-white">
                สวัสดี, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </span>
            <a class="btn btn-danger btn-sm" href="auth/logout.php">
                <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
            </a>
        </div>
    </div>
</nav>

<div class="wrapper">