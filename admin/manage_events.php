<?php
// 1. เรียก config.php ที่อยู่ในโฟลเดอร์เดียวกัน (admin)
require_once __DIR__ . '/config.php'; // (ได้ $pdo จากไฟล์นี้)

// 2. (สำคัญ) หน้านี้ไม่จำเป็นต้องใช้ ImageKit
// (ลบบรรทัดที่เรียก include/config.php หรือ imagekit.php ออก)

// 3. ตรวจสอบสิทธิ์ (ตอนนี้ $pdo พร้อมใช้งานแล้ว)
require_once ADMIN_ROOT . '/auth/auth_check.php';

// 4. ตั้งชื่อหน้า
$page_title = 'Manage Events';

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
<?php
// --- (นี่คือโค้ดจาก sidebar.php ที่คุณขอให้รวมเข้ามา) ---
?>
<nav id="sidebar">
    <div class="p-3">
        <h5 class="text-white-50 small text-uppercase mb-3">เมนูหลัก</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($page_title == 'Dashboard') ? 'active' : ''; ?>" href="dashboard.php">
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
// --- (นี่คือเนื้อหาเดิมของ manage_events.php) ---
?>
<h1 class="mb-4">จัดการข่าวสารและกิจกรรม (Events)</h1>
<p class="text-muted">กรุณาเลือก Event ที่ต้องการแก้ไขอัลบั้มรูปภาพ</p>

<div class="row row-cols-1 row-cols-md-3 g-4">
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event-fill display-4 text-primary"></i>
                <h5 class="card-title mt-3">Event 1 (ev1)</h5>
                <p class="card-text small">จัดการอัลบั้มรูปภาพสำหรับหน้า ev1</p>
                <a href="event/manage_ev1.php" class="btn btn-primary">ไปที่หน้าจัดการ</a>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event-fill display-4 text-primary"></i>
                <h5 class="card-title mt-3">Event 2 (ev2)</h5>
                <p class="card-text small">จัดการอัลบั้มรูปภาพสำหรับหน้า ev2</p>
                <a href="event/manage_ev2.php" class="btn btn-primary">ไปที่หน้าจัดการ</a>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event-fill display-4 text-primary"></i>
                <h5 class="card-title mt-3">Event 3 (ev3)</h5>
                <p class="card-text small">จัดการอัลบั้มรูปภาพสำหรับหน้า ev3</p>
                <a href="event/manage_ev3.php" class="btn btn-primary">ไปที่หน้าจัดการ</a>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event-fill display-4 text-primary"></i>
                <h5 class="card-title mt-3">Event 4 (ev4)</h5>
                <p class="card-text small">จัดการอัลบั้มรูปภาพสำหรับหน้า ev4</p>
                <a href="event/manage_ev4.php" class="btn btn-primary">ไปที่หน้าจัดการ</a>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
               <i class="bi bi-calendar-event-fill display-4 text-primary"></i>
                <h5 class="card-title mt-3">Event 5 (ev5)</h5>
                <p class="card-text small">จัดการอัลบั้มรูปภาพสำหรับหน้า ev5</p>
                <a href="event/manage_ev5.php" class="btn btn-primary">ไปที่หน้าจัดการ</a>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-calendar-event-fill display-4 text-primary"></i>
                <h5 class="card-title mt-3">Event 6 (ev6)</h5>
                <p class="card-text small">จัดการอัลบั้มรูปภาพสำหรับหน้า ev6</p>
                <a href="event/manage_ev6.php" class="btn btn-primary">ไปที่หน้าจัดการ</a>
            </div>
        </div>
    </div>
    
</div>

<?php
// --- (นี่คือโค้ดจาก footer.php ที่คุณขอให้รวมเข้ามา) ---
?>
</main> </div> <footer class="bg-dark text-center text-white-50 p-3" style="font-size: 0.9rem;">
    &copy; <?php echo date("Y"); ?> CMU X ACADEMY - Admin Panel
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>