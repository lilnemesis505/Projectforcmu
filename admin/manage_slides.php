<?php
// [แก้ไข]
// 1. เรียก Config หลักของ Admin ก่อน (สำคัญที่สุด)
require_once __DIR__ . '/config.php'; 

// 2. ตรวจสอบสิทธิ์ (ใช้ $pdo จากข้อ 1)
require_once ADMIN_ROOT . '/auth/auth_check.php';

// [!!! แก้ไข !!!]
// 3. เรียกไฟล์ ImageKit ที่เราตั้งชื่อใหม่
require_once PROJECT_ROOT . '/include/imagekit.php';

// 4. ตั้งชื่อหน้า
$page_title = 'Manage Banners';

// 5. (ไม่ต้องเรียก Header)

$message = ''; // สำหรับแสดงข้อความ feedback

// --- (ฟังก์ชัน) จัดการอัปโหลด 2-Step ---
function handleUpload($pdo, $imageKit, $fileInfo, $tableName, $fileNamePrefix, $folderPath) {
    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
    }
    
    $fileTempPath = $fileInfo['tmp_name'];
    $fileExtension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);
    
    $pdo->beginTransaction();
    $fileStream = null;
    try {
        $sql_insert = "INSERT INTO $tableName (image_url, file_id) VALUES ('pending...', 'pending...')";
        $pdo->exec($sql_insert);
        $db_id = $pdo->lastInsertId();
        
        $new_filename = $fileNamePrefix . $db_id . '.' . $fileExtension;

        // (โค้ดนี้ถูกต้องแล้ว: ใช้ fopen)
        $fileStream = fopen($fileTempPath, 'r');
        if (!$fileStream) {
            throw new Exception('ไม่สามารถเปิดไฟล์สตรีมได้');
        }

        $uploadResult = $imageKit->upload([
            'file' => $fileStream, // (โค้ดนี้ถูกต้องแล้ว)
            'fileName' => $new_filename,
            'folder' => $folderPath,
            'useUniqueFileName' => false,
            'overwrite' => true
        ]);

      $sql_update = "UPDATE $tableName SET image_url = ?, file_id = ? WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([
            $uploadResult->result->url, // (โค้ดนี้ถูกต้องแล้ว)
            $uploadResult->result->fileId, // (โค้ดนี้ถูกต้องแล้ว)
            $db_id
        ]);

        $pdo->commit();
        return "<div class='alert alert-success'>อัปโหลดสำเร็จ! (ID: {$db_id}, Filename: {$new_filename})</div>";
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    } finally {
        if ($fileStream && is_resource($fileStream)) {
            fclose($fileStream);
        }
    }
}

// --- (ฟังก์ชัน) จัดการลบ ---
function handleDelete($pdo, $imageKit, $tableName, $db_id, $file_id) {
    $imageKit->deleteFile($file_id);
    $sql = "DELETE FROM $tableName WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$db_id]);
    return "<div class='alert alert-warning'>ลบรูปภาพสำเร็จ</div>";
}


// --- 5. (สำคัญ) LOGIC: ดึงข้อมูล (SELECT) ทั้ง 2 ตาราง *ก่อน* ---
// (เราต้องนับ $banner_count ก่อนที่จะประมวลผล POST)
$current_slides = $pdo->query("SELECT * FROM slide ORDER BY id ASC")->fetchAll();
$current_banners = $pdo->query("SELECT * FROM banner ORDER BY id ASC")->fetchAll();
$banner_count = count($current_banners); // <-- ***(นี่คือจุดที่แก้ไข)***


// --- 4. LOGIC: ตรวจจับการ POST ทั้งหมด ---
try {
    // A. ลบ SLIDE (ตาราง slide)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_slide_id'])) {
        $message = handleDelete($pdo, $imageKit, 'slide', $_POST['delete_slide_id'], $_POST['delete_file_id']);
        // โหลดข้อมูลใหม่หลังลบ
        $current_slides = $pdo->query("SELECT * FROM slide ORDER BY id ASC")->fetchAll();
    }
    // B. ลบ BANNER (ตาราง banner)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_banner_id'])) {
        $message = handleDelete($pdo, $imageKit, 'banner', $_POST['delete_banner_id'], $_POST['delete_file_id']);
        // โหลดข้อมูลใหม่หลังลบ
        $current_banners = $pdo->query("SELECT * FROM banner ORDER BY id ASC")->fetchAll();
        $banner_count = count($current_banners); // อัปเดตตัวนับ
    }
    // C. อัปโหลด SLIDE (ตาราง slide)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['slide_image'])) {
        $message = handleUpload($pdo, $imageKit, $_FILES['slide_image'], 'slide', 'slide_', '/main/slide/');
        // โหลดข้อมูลใหม่หลังเพิ่ม
        $current_slides = $pdo->query("SELECT * FROM slide ORDER BY id ASC")->fetchAll();
    }
    
    // D. (แก้ไข) อัปโหลด BANNER (ตาราง banner)
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner_image'])) {
        
        // (นี่คือ Logic ล็อค 2 รูป ที่เพิ่มในฝั่ง Server)
        if ($banner_count >= 2) {
            $message = "<div class='alert alert-danger'>อัปโหลดล้มเหลว! มีแบนเนอร์ข้างครบ 2 รูปแล้ว กรุณาลบของเก่าออกก่อน</div>";
        } else {
            // (ถ้าไม่เกิน 2 รูป ก็อัปโหลดตามปกติ)
            $message = handleUpload($pdo, $imageKit, $_FILES['banner_image'], 'banner', 'bannerdown_', '/main/');
            // โหลดข้อมูลใหม่หลังเพิ่ม
            $current_banners = $pdo->query("SELECT * FROM banner ORDER BY id ASC")->fetchAll();
            $banner_count = count($current_banners); // อัปเดตตัวนับ
        }
    }
} catch (Exception $e) {
    $message = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
}
// --- (นี่คือโค้ดจาก header.php ที่คุณขอให้รวมเข้ามา) ---
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
                <a class="nav-link" href="index.php" target="_blank">
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
// --- (นี่คือเนื้อหาเดิมของ manage_slides.php) ---
?>
<?php if ($message): ?>
    <?php echo $message; ?>
<?php endif; ?>

<h1 class="mb-4">จัดการสไลด์โชว์หลัก (ตาราง `slide`)</h1>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h3>อัปโหลด (Insert) รูปภาพสไลด์ใหม่</h3>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="slide_image" class="form-label">เลือกไฟล์รูปภาพ:แนะนำ 1920x1080</label>
                <input type="file" class="form-control" name="slide_image" id="slide_image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> อัปโหลดสไลด์</button>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-header">
        <h3>สไลด์ที่มีในระบบ (<?php echo count($current_slides); ?> รูป)</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width: 30%;">รูปภาพ (ตัวอย่าง)</th>
                        <th>ImageKit File ID</th>
                        <th style="width: 15%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($current_slides)): ?>
                        <tr>
                            <td colspan="3" class="text-center">ยังไม่มีข้อมูล</td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($current_slides as $slide): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($slide['image_url']); ?>?tr=w-200,h-100,c-auto" 
                                     alt="Slide" class="img-thumbnail">
                                </td>
                            <td><code class="small"><?php echo htmlspecialchars($slide['file_id']); ?></code></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('แน่ใจหรือไม่?');">
                                    <input type="hidden" name="delete_slide_id" value="<?php echo $slide['id']; ?>">
                                    <input type="hidden" name="delete_file_id" value="<?php echo htmlspecialchars($slide['file_id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i> ลบ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<hr class="my-5">
<h1 class="mb-4">จัดการแบนเนอร์ข้าง (ตาราง `banner`)</h1>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h3>อัปโหลด (Insert) รูปภาพแบนเนอร์ข้างใหม่ (สูงสุด 2 รูป)</h3>
    </div>
    <div class="card-body">

        <?php 
        // (Logic นี้ถูกต้องแล้ว เพราะ $banner_count ถูกนับไว้ด้านบนแล้ว)
        if ($banner_count >= 2):
        ?>
            
            <div class="alert alert-warning" role="alert">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> ไม่สามารถอัปโหลดเพิ่มได้</h5>
                <p>คุณมีแบนเนอร์ข้างครบ 2 รูปแล้ว (ซึ่งเป็นจำนวนสูงสุด) หากต้องการอัปโหลดรูปใหม่ กรุณาลบรูปของเก่าในตารางด้านล่างออกก่อน</p>
            </div>

        <?php else: ?>
        <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="banner_image" class="form-label">เลือกไฟล์รูปภาพ:</label>
                    <input type="file" class="form-control" name="banner_image" id="banner_image" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-info"><i class="bi bi-upload me-1"></i> อัปโหลดแบนเนอร์ข้าง</button>
            </form>

        <?php endif; // สิ้นสุด if ($banner_count >= 2) ?>

    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h3>แบนเนอร์ข้างที่มีในระบบ (<?php echo $banner_count; ?> รูป)</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width: 30%;">รูปภาพ (ตัวอย่าง)</th>
                        <th>ImageKit File ID</th>
                        <th style="width: 15%;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($current_banners)): ?>
                        <tr>
                            <td colspan="3" class="text-center">ยังไม่มีข้อมูล</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($current_banners as $banner): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($banner['image_url']); ?>?tr=w-200,h-100,c-auto" 
                                     alt="Banner" class="img-thumbnail">
                            </td>
                            <td><code class="small"><?php echo htmlspecialchars($banner['file_id']); ?></code></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('แน่ใจหรือไม่?');">
                                    <input type="hidden" name="delete_banner_id" value="<?php echo $banner['id']; ?>">
                                    <input type="hidden" name="delete_file_id" value="<?php echo htmlspecialchars($banner['file_id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i> ลบ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <?php echo $message; ?>
<?php endif; ?>


<?php
// --- (นี่คือโค้ดจาก footer.php ที่คุณขอให้รวมเข้ามา) ---
?>
</main> </div> <footer class="bg-dark text-center text-white-50 p-3" style="font-size: 0.9rem;">
    &copy; <?php echo date("Y"); ?> CMU X ACADEMY - Admin Panel
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>