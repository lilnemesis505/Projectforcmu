<?php
// 1. เรียก config.php ที่อยู่ในโฟลเดอร์เดียวกัน (admin)
require_once __DIR__ . '/config.php'; // (ได้ $pdo จากไฟล์นี้)

// 2. เรียก Auth Check (หลัง Config)
require_once ADMIN_ROOT . '/auth/auth_check.php';

// 3. (ถูกต้อง) หน้านี้ไม่จำเป็นต้องใช้ ImageKit จึงไม่ต้องเรียก imagekit.php

// 4. ตั้งชื่อหน้า
$page_title = 'Dashboard';

// 5. --- (นี่คือโค้ดจาก header.php ที่คุณขอให้รวมเข้ามา) ---
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
        /* (CSS ของคุณ) */
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .wrapper {
            display: flex;
            width: 100%;
            flex: 1;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #212529;
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
            background: #f8f9fa;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo ADMIN_BASE_URL; ?>dashboard.php">
            <i class="bi bi-gear-fill me-2"></i> Admin Panel
        </a>
        
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?php echo PROJECT_URL; ?>index.php" target="_blank">
                    <i class="bi bi-house-door-fill me-1"></i> กลับหน้าบ้าน
                </a>
            </li>
        </ul>
        
        <div class="ms-auto">
            <span class="navbar-text me-3 text-white">
                สวัสดี, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </span>
            <a class="btn btn-danger btn-sm" href="<?php echo ADMIN_BASE_URL; ?>auth/logout.php">
                <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
            </a>
        </div>
    </div>
</nav>

<div class="wrapper">
<?php
// --- (นี่คือโค้ดจาก sidebar.php ที่คุณขอให้รวมเข้ามา) ---
?>
<nav id="sidebar">
    <div class="p-3">
        <h5 class="text-white-50 small text-uppercase mb-3">เมนูหลัก</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($page_title == 'Dashboard') ? 'active' : ''; ?>" href="<?php echo ADMIN_BASE_URL; ?>dashboard.php">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($page_title == 'Manage Banners') ? 'active' : ''; ?>" href="manage_slides.php">
                    <i class="bi bi-images me-2"></i> จัดการรูป Banner
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($page_title == 'Manage Events') ? 'active' : ''; ?>" href="manage_events.php">
                    <i class="bi bi-newspaper me-2"></i> จัดการข่าวสาร/Event
                </a>
                </li>
        </ul>
    </div>
</nav>

<main id="content">
<?php
// --- (สิ้นสุดส่วนที่รวมเข้ามา) ---
?>

<?php
// 6. (โค้ด PHP เดิมของหน้านี้) ดึงข้อมูลสถิติ
// [แก้ไขคอมเมนต์] (โค้ดส่วนนี้ทำงานได้เพราะ $pdo ถูกโหลดมาจาก admin/config.php แล้ว)
try {
    $total_views = $pdo->query("SELECT COUNT(id) FROM page_views WHERE page_url = 'index.php'")->fetchColumn();
    $recent_views = $pdo->query("SELECT user_agent FROM page_views WHERE page_url = 'index.php' ORDER BY id DESC LIMIT 10")->fetchAll();
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<h2>Dashboard: <span class="text-primary">Traffic (index.php)</span></h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php else: ?>

<div class="row my-4 justify-content-center">
    <div class="col-md-8">
        <div class="card stat-card bg-primary text-white mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">ยอดวิวทั้งหมด (Total Page Loads)</h5>
                <p class="display-4"><?php echo $total_views; ?></p>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php 
// 7. --- (นี่คือโค้ดจาก footer.php ที่คุณขอให้รวมเข้ามา) ---
?>
</main> </div> <footer class="bg-dark text-center text-white-50 p-3" style="font-size: 0.9rem;">
    &copy; <?php echo date("Y"); ?> CMU X ACADEMY - Admin Panel
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>