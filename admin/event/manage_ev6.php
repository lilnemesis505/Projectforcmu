<?php
// 1. (สำคัญ) ตั้งค่า
// (ส่วนนี้ถูกต้อง)
require_once __DIR__ . '/../config.php'; // (แก้ Path ชี้ไปที่ admin/config.php)

// 2. เรียก Auth Check (หลัง Config)
require_once ADMIN_ROOT . '/auth/auth_check.php';

// 3. เรียก Config ของ ImageKit (หน้านี้ต้องใช้)
// (ส่วนนี้ถูกต้อง)
require_once PROJECT_ROOT . '/include/imagekit.php'; // (แก้เป็น imagekit.php)

$page_title = 'Manage Events'; // (เพื่อให้ Sidebar "จัดการข่าวสาร" Active)
$table_name = 'ev6';           // (ตารางสำหรับรูปภาพ)
$meta_key = 'ev6';             // (Key สำหรับตาราง event_meta)
$folder_path = '/event/ev6/';
$file_prefix = 'ev6_';

// --- (ฟังก์ชัน) จัดการลบ ---
// (ฟังก์ชันนี้เหมือนเดิม ไม่ต้องแก้)
function handleDelete($pdo, $imageKit, $tableName, $db_id, $file_id) {
    $imageKit->deleteFile($file_id);
    $sql = "DELETE FROM $tableName WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$db_id]);
    return true; // (เปลี่ยนเป็น return true พอ)
}

// --- [!!! แก้ไข Logic POST ทั้งหมด !!!] ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // A. อัปเดตรายละเอียด (Title/Description)
        if (isset($_POST['update_details'])) {
            $sql_update_meta = "UPDATE event_meta SET event_title = ?, event_description = ? WHERE event_key = ?";
            $stmt_update_meta = $pdo->prepare($sql_update_meta);
            $stmt_update_meta->execute([
                $_POST['event_title'], 
                $_POST['event_description'], 
                $meta_key
            ]);
            
            // [!!! แก้ไข !!!] เปลี่ยนจาก $message เป็น set_flash_message()
            set_flash_message('success', 'บันทึกรายละเอียด Event 1 สำเร็จ!');
        }

        // B. ลบรูป
        elseif (isset($_POST['delete_id'])) {
            handleDelete($pdo, $imageKit, $table_name, $_POST['delete_id'], $_POST['delete_file_id']);
            
            // [!!! แก้ไข !!!] เปลี่ยนจาก $message เป็น set_flash_message()
            set_flash_message('warning', 'ลบรูปภาพสำเร็จ');
        }
        
        // C. อัปโหลดรูปใหม่
        elseif (isset($_FILES['event_image'])) {
            // ... (โค้ดอัปโหลดรูปของคุณเหมือนเดิม) ...
            if ($_FILES['event_image']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
            }
            $fileTempPath = $_FILES['event_image']['tmp_name'];
            $fileExtension = pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION);
            $pdo->beginTransaction();
            $fileStream = null;
            try {
                // ... (Logic อัปโหลด... $pdo->commit()) ...
                $sql_insert = "INSERT INTO $table_name (image_url, file_id) VALUES ('pending...', 'pending...')";
                $pdo->exec($sql_insert);
                $db_id = $pdo->lastInsertId();
                $new_filename = $file_prefix . $db_id . '.' . $fileExtension;
                $fileStream = fopen($fileTempPath, 'r');
                if (!$fileStream) throw new Exception('ไม่สามารถเปิดไฟล์สตรีมได้');
                $uploadResult = $imageKit->upload([
                    'file' => $fileStream,
                    'fileName' => $new_filename,
                    'folder' => $folder_path,
                    'useUniqueFileName' => false,
                    'overwrite' => true
                ]);
                $sql_update = "UPDATE $table_name SET image_url = ?, file_id = ? WHERE id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([
                    $uploadResult->result->url,
                    $uploadResult->result->fileId,
                    $db_id
                ]);
                $pdo->commit();
                
                // [!!! แก้ไข !!!] เปลี่ยนจาก $message เป็น set_flash_message()
                set_flash_message('success', "อัปโหลดรูปสำหรับ Event 1 สำเร็จ! (ID: {$db_id})");
            
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            } finally {
                if ($fileStream && is_resource($fileStream)) {
                    fclose($fileStream);
                }
            }
        }
    } catch (Exception $e) {
        // [!!! แก้ไข !!!] เปลี่ยนจาก $message เป็น set_flash_message()
        set_flash_message('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }

    // --- [!!! เพิ่ม Redirect !!!] ---
    // (หลังจากประมวลผล POST เสร็จ ให้โหลดหน้าใหม่ทันที)
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
// --- (สิ้นสุดการแก้ไข Logic POST) ---


// --- 5. LOGIC: ดึงข้อมูล (SELECT) ---
// (ส่วนนี้ถูกต้อง)
$stmt_meta = $pdo->prepare("SELECT * FROM event_meta WHERE event_key = ?");
$stmt_meta->execute([$meta_key]);
$meta = $stmt_meta->fetch(PDO::FETCH_ASSOC);

$current_photos = $pdo->query("SELECT * FROM $table_name ORDER BY id ASC")->fetchAll();

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
                <a class="nav-link" href="<?php echo ADMIN_BASE_URL; ?>dashboard.php">
                    <i class="bi bi-grid-fill me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo ADMIN_BASE_URL; ?>manage_slides.php">
                    <i class="bi bi-images me-2"></i> จัดการรูป Banner
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?php echo ADMIN_BASE_URL; ?>manage_events.php">
                    <i class="bi bi-newspaper me-2"></i> จัดการข่าวสาร/Event
                </a>
                </li>
        </ul>
    </div>
</nav>

<main id="content">
<?php
require_once ADMIN_ROOT . '/include/notification.php';
?>


<a href="<?php echo ADMIN_BASE_URL; ?>manage_events.php" class="btn btn-outline-secondary btn-sm mb-3">
    &laquo; กลับไปหน้ารวม Events
</a>

<h1 class="mb-4">จัดการรายละเอียด (Event: <?php echo $meta_key; ?>)</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h3>แก้ไขชื่อและรายละเอียด</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="update_details" value="1">
            <div class="mb-3">
                <label for="event_title" class="form-label">หัวข้อกิจกรรม:</label>
                <input type="text" class="form-control" name="event_title" id="event_title" value="<?php echo htmlspecialchars($meta['event_title'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="event_description" class="form-label">รายละเอียด (Details):</label>
                <textarea class="form-control" name="event_description" id="event_description" rows="5"><?php echo htmlspecialchars($meta['event_description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> บันทึกรายละเอียด</button>
        </form>
    </div>
</div>


<hr class="my-5">

<h1 class="mb-4">จัดการอัลบั้ม (ตาราง `<?php echo $table_name; ?>`)</h1>


<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h3>อัปโหลด (Insert) รูปภาพใหม่</h3>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="event_image" class="form-label">เลือกไฟล์รูปภาพ:</label>
                <input type="file" class="form-control" name="event_image" id="event_image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> อัปโหลด</button>
        </form>
    </div>
</div>


<div class="card shadow-sm">
    <div class="card-header">
        <h3>รูปภาพในอัลบั้ม (<?php echo count($current_photos); ?> รูป)</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <tbody>
                    <?php if (empty($current_photos)): ?>
                        <tr><td colspan="3" class="text-center">ยังไม่มีรูปภาพในอัลบั้มนี้</td></tr>
                    <?php endif; ?>
                    
                    <?php foreach ($current_photos as $photo): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($photo['image_url']); ?>?tr=w-200,h-100,c-auto" alt="Photo" class="img-thumbnail"></td>
                            <td><code class="small"><?php echo htmlspecialchars($photo['file_id']); ?></code></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('แน่ใจหรือไม่?');">
                                    <input type="hidden" name="delete_id" value="<?php echo $photo['id']; ?>">
                                    <input type="hidden" name="delete_file_id" value="<?php echo htmlspecialchars($photo['file_id']); ?>">
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
</main> </div> <footer class="bg-dark text-center text-white-50 p-3" style="font-size: 0.9rem;">
    &copy; <?php echo date("Y"); ?> CMU X ACADEMY - Admin Panel
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>