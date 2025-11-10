<?php
    require_once 'include/db.php';

    // --- (แก้ไข) บันทึก TRAFFIC (แบบใหม่) ---
    try {
        $page = 'index.php'; // หน้าที่เรากำลังติดตาม
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

        // (แก้ไข SQL INSERT)
        $sql_track = "INSERT INTO page_views (page_url, user_agent) 
                      VALUES (?, ?)";
        $stmt_track = $pdo->prepare($sql_track);
        // (แก้ไข Execute)
        $stmt_track->execute([$page, $agent]);

    } catch (Exception $e) {
        // (ถ้าการบันทึก fail ก็ไม่เป็นไร อย่าให้หน้าเว็บล่ม)
    }
    $slides = []; // <-- เปลี่ยนชื่อ
    try {
        $stmt_slide = $pdo->query("SELECT image_url FROM slide ORDER BY id ASC"); // <-- เปลี่ยน
        $slides = $stmt_slide->fetchAll(PDO::FETCH_ASSOC); // <-- เปลี่ยน
    } catch (Exception $e) { /* Error */ }

    // --- 2. (แก้ไข) ดึงข้อมูลแบนเนอร์ด้านข้าง (จากตาราง 'banner') ---
    $banners = []; // <-- เปลี่ยนชื่อ
    try {
        // (เปลี่ยน) ดึงจาก 'banner' และเปลี่ยน ORDER BY name เป็น ORDER BY id
        $stmt_banner = $pdo->query("SELECT image_url FROM banner ORDER BY id ASC LIMIT 2"); // <-- เปลี่ยน
        $banners = $stmt_banner->fetchAll(PDO::FETCH_ASSOC); // <-- เปลี่ยน
    } catch (Exception $e) { /* Error */ }

    // --- 3. (แก้ไข) เตรียม URL ให้พร้อมใช้งาน (พร้อมรูป Default) ---
    $banner_1_url = $banners[0]['image_url'] ?? 'https://via.placeholder.com/400x200.png?text=Side+Banner+1'; // <-- เปลี่ยน
    $banner_2_url = $banners[1]['image_url'] ?? 'https://via.placeholder.com/400x200.png?text=Side+Banner+2'; // <-- เปลี่ยน

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตัวอย่าง Layout (พร้อม Navbar และส่วนข่าว)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

   <?php
   require_once 'include/navbar.php'
   ?>

    <div class="container">

       <section class="banner-section">
            
            <div class="main-banner">
                <div id="mainBannerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        <?php if (empty($slides)): ?> <div class="carousel-item active">
                                <img src="https://via.placeholder.com/800x400.png?text=No+slides+found" class="d-block w-100" alt="Default Banner"> </div>
                        <?php else: ?>
                            <?php foreach ($slides as $index => $slide): ?> <div class="carousel-item <?php echo ($index == 0) ? 'active' : ''; ?>">
                                    <img src="<?php echo htmlspecialchars($slide['image_url']); ?>?tr=w-800,h-400,c-at_max" 
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
                <img src="<?php echo htmlspecialchars($banner_1_url); ?>?tr=w-400,h-200,c-at_max" alt="Side Banner 1">
                <img src="<?php echo htmlspecialchars($banner_2_url); ?>?tr=w-400,h-200,c-at_max" alt="Side Banner 2">
            </div>
            </section>

        <hr class="my-5">
        <section class="members-section my-5">
            <h2 class="text-left mb-4">สมาชิก</h2>
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img src="https://via.placeholder.com/500x300.png?text=Member+Benefits" class="img-fluid rounded shadow-sm" alt="รูปภาพสมาชิก">
                </div>
                <div class="col-md-7">
                    <h3>สิทธิประโยชน์สำหรับสมาชิก</h3>
                    <p class="lead">รายละเอียดเกี่ยวกับสิทธิประโยชน์ต่างๆ ที่สมาชิกจะได้รับ เช่น ส่วนลดพิเศษ, การเข้าร่วมกิจกรรม, และอื่นๆ อีกมากมาย...</p>
                    <p>คุณสามารถใส่เนื้อหารายละเอียดเพิ่มเติมเกี่ยวกับสมาชิกได้ที่นี่ สามารถใส่ปุ่ม หรือ list รายการสิทธิประโยชน์ได้</p>
                    <a href="#" class="btn btn-primary">ดูรายละเอียดเพิ่มเติม</a>
                </div>
            </div>
        </section>
        
        <hr class="my-5">
        <section class="members-section my-5">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <img src="https://via.placeholder.com/500x300.png?text=Member+Benefits" class="img-fluid rounded shadow-sm" alt="รูปภาพสมาชิก">
                </div>
                <div class="col-md-7">
                    <h3>สิทธิประโยชน์สำหรับผู้ถือหุ้นสหกรณ์</h3>
                    <p class="lead">1.	เมื่อผู้สมัครสมาชิก จ่ายเงิน 100 บาท เพื่อสมัครเป็นสมาชิกแล้วนั้น จะมีสิทธิได้รับหุ้นจำนวน 1 ตัว เป็นเงิน 20 บาท และได้รับของรางวัลที่แจกประจำเดือน</p>
                    <a href="#" class="btn btn-primary">ดูรายละเอียดเพิ่มเติม</a>
                </div>
            </div>
        </section>

        <hr class="my-5">
        <section class="news-section">
            <h2>ข่าวสารและกิจกรรม</h2>
            
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                <div class="col">
                    <div class="card shadow-sm">
                        <svg aria-label="Placeholder: Thumbnail" class="bd-placeholder-img card-img-top" height="225" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                </div>
                                <small class="text-body-secondary">9 mins</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <svg aria-label="Placeholder: Thumbnail" class="bd-placeholder-img card-img-top" height="225" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                </div>
                                <small class="text-body-secondary">9 mins</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <svg aria-label="Placeholder: Thumbnail" class="bd-placeholder-img card-img-top" height="225" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                </div>
                                <small class="text-body-secondary">9 mins</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <svg aria-label="Placeholder: Thumbnail" class="bd-placeholder-img card-img-top" height="225" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                </div>
                                <small class="text-body-secondary">9 mins</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <svg aria-label="Placeholder: Thumbnail" class="bd-placeholder-img card-img-top" height="225" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                </div>
                                <small class="text-body-secondary">9 mins</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <svg aria-label="Placeholder: Thumbnail" class="bd-placeholder-img card-img-top" height="225" preserveAspectRatio="xMidYMid slice" role="img" width="100%" xmlns="http://www.w3.org/2000/svg"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                        <div class="card-body">
                            <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                                </div>
                                <small class="text-body-secondary">9 mins</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
       </section>

    <?php 
    require_once 'include/footer.php'; 
    ?>