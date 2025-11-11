<?php
    require_once 'include/db.php';

    // --- (ส่วนบันทึก Traffic เหมือนเดิม) ---
    try {
        $page = 'index.php';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        $sql_track = "INSERT INTO page_views (page_url, user_agent) 
                      VALUES (?, ?)";
        $stmt_track = $pdo->prepare($sql_track);
        $stmt_track->execute([$page, $agent]);
    } catch (Exception $e) { /* (ignore) */ }


    // --- 1. ดึงข้อมูลสไลด์โชว์หลัก (ตาราง 'slide') ---
    $slides = []; 
    try {
        $stmt_slide = $pdo->query("SELECT image_url FROM slide ORDER BY id ASC");
        $slides = $stmt_slide->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { /* Error */ }

    // --- 2. ดึงข้อมูลแบนเนอร์ด้านข้าง (ตาราง 'banner') ---
    $banners = [];
    try {
        $stmt_banner = $pdo->query("SELECT image_url FROM banner ORDER BY id ASC LIMIT 2");
        $banners = $stmt_banner->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) { /* Error */ }

    // --- 3. เตรียม URL ให้พร้อมใช้งาน ---
    $banner_1_url = $banners[0]['image_url'] ?? 'https://via.placeholder.com/400x200.png?text=Side+Banner+1';
    $banner_2_url = $banners[1]['image_url'] ?? 'https://via.placeholder.com/400x200.png?text=Side+Banner+2';

    // =======================================================
    // [เพิ่ม] 4. ดึงข้อมูล Event ทั้ง 6 (สำหรับกล่องข่าว)
    // =======================================================

    // 4.1 ดึง "หัวข้อ" ทั้งหมดจาก event_meta
    $event_metas = [];
    try {
        $stmt_meta = $pdo->query("SELECT event_key, event_title FROM event_meta WHERE event_key IN ('ev1', 'ev2', 'ev3', 'ev4', 'ev5', 'ev6')");
        // จัดเรียงข้อมูลใหม่ให้ใช้ง่าย (เช่น $event_metas['ev1'] = 'หัวข้อ...')
        foreach ($stmt_meta->fetchAll(PDO::FETCH_ASSOC) as $meta) {
            $event_metas[$meta['event_key']] = $meta['event_title'];
        }
    } catch (Exception $e) { /* Error */ }

    // 4.2 ดึง "รูปปก" (รูปแรกสุด) ของแต่ละ Event
    $event_covers = [];
    try {
        // ใช้ UNION ALL เพื่อรวมผลลัพธ์จาก 6 ตารางใน query เดียว (ประสิทธิภาพดี)
        $sql_covers = "
            (SELECT 'ev1' as event_key, image_url FROM ev1 ORDER BY id ASC LIMIT 1)
            UNION ALL
            (SELECT 'ev2' as event_key, image_url FROM ev2 ORDER BY id ASC LIMIT 1)
            UNION ALL
            (SELECT 'ev3' as event_key, image_url FROM ev3 ORDER BY id ASC LIMIT 1)
            UNION ALL
            (SELECT 'ev4' as event_key, image_url FROM ev4 ORDER BY id ASC LIMIT 1)
            UNION ALL
            (SELECT 'ev5' as event_key, image_url FROM ev5 ORDER BY id ASC LIMIT 1)
            UNION ALL
            (SELECT 'ev6' as event_key, image_url FROM ev6 ORDER BY id ASC LIMIT 1)
        ";
        $stmt_covers = $pdo->query($sql_covers);
        // จัดเรียงข้อมูลใหม่ให้ใช้ง่าย (เช่น $event_covers['ev1'] = 'url...')
        foreach ($stmt_covers->fetchAll(PDO::FETCH_ASSOC) as $cover) {
            $event_covers[$cover['event_key']] = $cover['image_url'];
        }
    } catch (Exception $e) { /* Error */ }

?>
<?php
    // (เพิ่มบรรทัดนี้)
    $path_prefix = ''; 
    require_once 'include/header.php'
?>

    <section class="banner-section">
            
            <div class="main-banner">
                <div id="mainBannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        <?php if (empty($slides)): ?> <div class="carousel-item active">
                                <img src="https://via.placeholder.com/800x400.png?text=No+slides+found" class="d-block w-100" alt="Default Banner"> </div>
                        <?php else: ?>
                            <?php foreach ($slides as $index => $slide): ?> <div class="carousel-item <?php echo ($index == 0) ? 'active' : ''; ?>">
                                    
                                    <img src="<?php echo htmlspecialchars($slide['image_url']); ?>?tr=w-800,h-400,c-auto" 
                                         class="d-block w-100" 
                                         alt="Slide <?php echo $index + 1; ?>"> </div>
                                    <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
            
            <div class="side-banners"> 
                <img src="<?php echo htmlspecialchars($banner_1_url); ?>?tr=w-400,h-200,c-auto" alt="Side Banner 1">
                <img src="<?php echo htmlspecialchars($banner_2_url); ?>?tr=w-400,h-200,c-auto" alt="Side Banner 2">
                </div>
            </section>
        <hr class="my-5">

    <section class="members-section my-4">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img src="https://ik.imagekit.io/cmuxacademy/member?updatedAt=1762759404324&tr=w-400,h-200,c-auto" class="img-fluid rounded shadow-sm" alt="รูปภาพสมาชิก">
                </div>
                <div class="col-md-7">
                    <h4>สิทธิประโยชน์สำหรับสมาชิก</h4>
                    <p>รายละเอียดเกี่ยวกับสิทธิประโยชน์ต่างๆ ที่สมาชิกจะได้รับ เช่น ส่วนลดพิเศษ, การเข้าร่วมกิจกรรม, และอื่นๆ อีกมากมาย...</p>
                    <a href="member.php" class="btn btn-primary btn-sm">ดูรายละเอียดเพิ่มเติม</a>
                </div>
            </div>
        </section>
        
        <hr class="my-4">

        <section class="members-section my-4">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img src="https://ik.imagekit.io/cmuxacademy/board?updatedAt=1762759204491&tr=w-400,h-200,c-auto" class="img-fluid rounded shadow-sm" alt="รูปภาพสมาชิก">
                </div>
                <div class="col-md-7">
                    <h4>สิทธิประโยชน์สำหรับผู้ถือหุ้นสหกรณ์</h4>
                    <p>เมื่อผู้สมัครสมาชิก จ่ายเงิน 100 บาท เพื่อสมัครเป็นสมาชิกแล้วนั้น จะมีสิทธิได้รับหุ้นจำนวน 1 ตัว เป็นเงิน 20 บาท และได้รับของรางวัลที่แจกประจำเดือน</p>
                    
                    <a href="board.php" class="btn btn-primary btn-sm">ดูรายละเอียดเพิ่มเติม</a>
                </div>
            </div>
        </section>

        <hr class="my-5">

    <section class="news-section">
        <h2>ข่าวสารและกิจกรรม X-CADEMY</h2>
            
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">

        <?php
            // กำหนด Key ของ Event ทั้ง 6
            $event_keys = ['ev1', 'ev2', 'ev3', 'ev4', 'ev5', 'ev6'];
            
            // กำหนดรูปภาพสำรอง (เผื่อ Event นั้นยังไม่มีรูป)
            $placeholder_img = 'https://via.placeholder.com/400x225.png?text=Event+Cover';

            // วน Loop สร้างการ์ด 6 ใบ
            foreach ($event_keys as $key):
                
                // 1. ดึง "หัวข้อ" (จาก $event_metas ที่เราเตรียมไว้)
                $title = $event_metas[$key] ?? 'หัวข้อ ' . $key; // (?? '...A...' หมายถึง ถ้าไม่มีข้อมูล ให้ใช้ '...A...')

                // 2. ดึง "รูปปก" (จาก $event_covers ที่เราเตรียมไว้)
                $cover_image = $event_covers[$key] ?? $placeholder_img;
                
                // 3. สร้าง "ลิงก์"
                $link = 'event/' . $key . '.php';
        ?>
            
            <div class="col">
                <div class="card shadow-sm">
                    
                    <img src="<?php echo htmlspecialchars($cover_image); ?><?php echo ($cover_image != $placeholder_img) ? '?tr=w-400,h-225,c-auto' : ''; ?>" 
                         class="bd-placeholder-img card-img-top" width="100%" height="225" 
                         alt="หน้าปก <?php echo $key; ?>">
                    
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($title); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="<?php echo $link; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                            </div>
                            </div>
                    </div>
                </div>
            </div>
            <?php endforeach; // จบ Loop ?>

        </div> 
    </section>
    <?php 
    require_once 'include/footer.php'; 
    ?>