<?php
// 1. ตั้งชื่อ Title (สำหรับ <title> และ Sidebar Active)
$page_title = 'Dashboard'; 

// 2. เรียก Header (ซึ่งจะรวม Navbar + auth_check)
require_once 'include/header.php';

// 3. เรียก Sidebar
require_once 'include/sidebar.php'; 

// 4. (โค้ด PHP เดิมของหน้านี้) ดึงข้อมูลสถิติ
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

<h3>10 การเข้าชมล่าสุด (index.php)</h3>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th>User Agent (Browser)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_views)): ?>
                    <tr><td colspan="1" class="text-center">ยังไม่มีข้อมูล</td></tr>
                <?php endif; ?>
                
                <?php foreach ($recent_views as $view): ?>
                <tr>
                    <td><small class="text-muted"><?php echo htmlspecialchars($view['user_agent']); ?></small></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<?php require_once 'include/footer.php'; ?>