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
                <a class="nav-link <?php echo ($page_title == 'Manage Slides') ? 'active' : ''; ?>" href="manage_slides.php">
                    <i class="bi bi-images me-2"></i> จัดการสไลด์
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($page_title == 'Manage Banners') ? 'active' : ''; ?>" href="manage_banners.php">
                    <i class="bi bi-aspect-ratio-fill me-2"></i> จัดการแบนเนอร์ข้าง
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="bi bi-newspaper me-2"></i> จัดการข่าวสาร
                </a>
            </li>
        </ul>
    </div>
</nav>

<main id="content">