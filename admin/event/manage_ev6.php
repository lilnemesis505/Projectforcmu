<?php
require_once __DIR__ . '/../include/config.php'; 

// 2. เรียก Auth Check (หลัง Config)
require_once ADMIN_ROOT . '/auth/auth_check.php';

// 3. เรียก Config ของ ImageKit (หน้านี้ต้องใช้)
require_once PROJECT_ROOT . '/include/config.php';
// 1. (สำคัญ) ตั้งค่า
$page_title = 'Manage Events'; // (เพื่อให้ Sidebar "จัดการข่าวสาร" Active)
$table_name = 'ev6';           // (ตารางสำหรับรูปภาพ)
$meta_key = 'ev6';             // (Key สำหรับตาราง event_meta)
$folder_path = '/event/ev6/';
$file_prefix = 'ev6_';

require_once ADMIN_ROOT . '/include/header.php';
$message = ''; 

// --- (ฟังก์ชัน) จัดการลบ ---
function handleDelete($pdo, $imageKit, $tableName, $db_id, $file_id) {
    // ... (โค้ดฟังก์ชันของคุณเหมือนเดิม) ...
    $imageKit->deleteFile($file_id);
    $sql = "DELETE FROM $tableName WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$db_id]);
    return "<div class='alert alert-warning'>ลบรูปภาพสำเร็จ</div>";
}

// --- LOGIC: ตรวจจับการ POST ---
try {
    // [เพิ่ม!] A. อัปเดตรายละเอียด (Title/Description)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_details'])) {
        $sql_update_meta = "UPDATE event_meta SET event_title = ?, event_description = ? WHERE event_key = ?";
        $stmt_update_meta = $pdo->prepare($sql_update_meta);
        $stmt_update_meta->execute([
            $_POST['event_title'], 
            $_POST['event_description'], 
            $meta_key
        ]);
        $message = "<div class='alert alert-success'>อัปเดตรายละเอียดสำเร็จ!</div>";
    }

    // B. ลบรูป
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $message = handleDelete($pdo, $imageKit, $table_name, $_POST['delete_id'], $_POST['delete_file_id']);
    }
    
    // C. อัปโหลดรูปใหม่
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['event_image'])) {
        // ... (โค้ดอัปโหลดรูปของคุณเหมือนเดิม) ...
        if ($_FILES['event_image']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
        }
        $fileTempPath = $_FILES['event_image']['tmp_name'];
        $fileExtension = pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION);
        $pdo->beginTransaction();
        $fileStream = null; 
        try {
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
            $message = "<div class='alert alert-success'>อัปโหลดสำเร็จ! (ID: {$db_id})</div>";
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
    $message = "<div class='alert alert-danger'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</div>";
}

// --- 5. LOGIC: ดึงข้อมูล (SELECT) ---
// [เพิ่ม!] ดึงข้อมูล Meta (Title/Description)
$stmt_meta = $pdo->prepare("SELECT * FROM event_meta WHERE event_key = ?");
$stmt_meta->execute([$meta_key]);
$meta = $stmt_meta->fetch(PDO::FETCH_ASSOC);

// (ของเดิม) ดึงข้อมูลรูปภาพ
$current_photos = $pdo->query("SELECT * FROM $table_name ORDER BY id ASC")->fetchAll();

// [แก้ไข - ลบ!]
// 6. ลบการเรียก Sidebar ซ้ำซ้อน (เพราะ Header เรียกให้แล้ว)
// require_once '../include/sidebar.php'; 
?>

<?php if ($message): ?>
    <?php echo $message; ?>
<?php endif; ?>

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

<?php 
// [แก้ไข]
// 7. เรียก Footer โดยใช้ ADMIN_ROOT
require_once ADMIN_ROOT . '/include/footer.php'; 
?>