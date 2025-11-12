<?php
// 1. (เพิ่มใหม่) ตั้งค่า prefix path เริ่มต้น
// ถ้าไฟล์ที่เรียกใช้ไม่ได้กำหนด $path_prefix มา ให้ใช้ค่าว่าง (สำหรับ index.php)
if (!isset($path_prefix)) {
    $path_prefix = '';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMU X ACADEMY</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="/Projectforcmu/assets/css/style.css">

</head>
<body>

    <nav class="navbar">
        <div class="logo">
            <a href="<?php echo $path_prefix; ?>index.php" style="text-decoration: none; color: inherit;">CMU X ACADEMY</a>
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo $path_prefix; ?>index.php">หน้าหลัก</a></li>
            <li><a href="<?php echo $path_prefix; ?>member/member.php">สมาชิก</a></li>
            <li><a href="<?php echo $path_prefix; ?>board/board.php">ผู้ถือหุ้น</a></li>
            <li><a href="#">อื่นๆ</a></li>
        </ul>
    </nav>

    <div class="container">