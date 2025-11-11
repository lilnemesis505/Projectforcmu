<?php 
    // 1. (สำคัญ) กำหนด path prefix
    $path_prefix = '../'; 
    require_once '../include/header.php'; 

    // (V V V) เรียก db.php เพื่อให้มี $pdo (V V V)
    require_once '../include/db.php'; 

    // --- (ส่วนที่ 1: แก้ไข) ดึงข้อมูล Meta (Title/Desc) ---
    $meta_key = 'ev4'; // (กำหนด Key สำหรับ Event นี้)
    $meta = [
        'event_title' => 'กำลังโหลดหัวข้อ...',
        'event_description' => 'กำลังโหลดรายละเอียด...'
    ]; // (ค่าเริ่มต้นเผื่อ DB ไม่มีข้อมูล)

    try {
        $stmt_meta = $pdo->prepare("SELECT event_title, event_description FROM event_meta WHERE event_key = ?");
        $stmt_meta->execute([$meta_key]);
        $data = $stmt_meta->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $meta = $data; // ถ้ามีข้อมูล ให้แทนที่ค่าเริ่มต้น
        }
    } catch (Exception $e) {
        // (จัดการ Error - อาจจะ log ไว้)
        error_log($e->getMessage());
    }

    // --- (ส่วนที่ 2: ของเดิม) ดึงข้อมูลอัลบั้มจาก DB ---
    $photos = [];
    try {
        $stmt_photos = $pdo->query("SELECT * FROM ev4 ORDER BY id ASC"); 
        $photos = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // (จัดการ Error)
        error_log($e->getMessage());
    }
?>

<div class="container my-5">
    
    <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
            
            <a href="../index.php" class="text-decoration-none text-muted mb-2 d-block">&laquo; กลับไปหน้าข่าวสาร</a>
            
            <h1><?php echo htmlspecialchars($meta['event_title']); ?></h1>
            
            <p class="lead text-muted">
                <?php echo nl2br(htmlspecialchars($meta['event_description'])); ?>
            </p>
            <hr>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <h3 class="mb-3">อัลบั้มรูปภาพ (<?php echo count($photos); ?> รูป)</h3>
            
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                
                <?php if (empty($photos)): ?>
                    <div class="col-12">
                        <p class="text-muted">ยังไม่มีรูปภาพในอัลบั้มนี้ (กรุณาอัปโหลดในหน้า Admin)</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($photos as $photo): ?>
                <div class="col">
                    <div class="card shadow-sm">
                        <img src="<?php echo htmlspecialchars($photo['image_url']); ?>?tr=w-600,h-400,c-auto" 
                             class="img-fluid" 
                             alt="Event Photo">
                    </div>
                </div>
                <?php endforeach; ?>
                
            </div>
            
        </div>
    </div>
    
</div>
<?php 
    require_once '../include/footer.php'; 
?>