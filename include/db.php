<?php
// --- 1. ตั้งค่าการเชื่อมต่อ ---
$host = 'localhost';
$db_name = 'cmuxacademy';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // --- 2. เชื่อมต่อและสร้าง DB (ถ้ายังไม่มี) ---
    $dsn_server = "mysql:host=$host;charset=$charset";
    $pdo = new PDO($dsn_server, $username, $password, $options);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `$db_name`");
    
    // --- 3. สร้างตาราง 'admin' ---
    $sql_admin = "
    CREATE TABLE IF NOT EXISTS `admin` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `username` VARCHAR(50) NOT NULL UNIQUE,
      `password` VARCHAR(255) NOT NULL,
      `email` VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_admin);

    // --- 4. สร้างตาราง 'slide' ---
    $sql_slide = "
    CREATE TABLE IF NOT EXISTS `slide` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `image_url` VARCHAR(255) NOT NULL,
      `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_slide);

    // --- 5. สร้างตาราง 'banner' ---
    $sql_banner = "
    CREATE TABLE IF NOT EXISTS `banner` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `image_url` VARCHAR(255) NOT NULL,
      `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_banner);

    // --- 6. สร้างตาราง 'auth_tokens' (สำหรับ Remember Me) ---
    $sql_tokens = "
    CREATE TABLE IF NOT EXISTS `auth_tokens` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `admin_id` INT NOT NULL,
        `selector` VARCHAR(255) NOT NULL UNIQUE,
        `hashed_validator` VARCHAR(255) NOT NULL,
        `expires` DATETIME NOT NULL,
        FOREIGN KEY (`admin_id`) REFERENCES `admin`(`id`) ON DELETE CASCADE
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_tokens);

    // --- 7. (แก้ไข) สร้างตาราง 'page_views' ตามที่คุณขอ ---
    // (ลบ ip_address, referrer และแก้ , (comma) ที่เกินมา)
    $sql_views = "
    CREATE TABLE IF NOT EXISTS `page_views` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `page_url` VARCHAR(255) NOT NULL,
        `user_agent` TEXT
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_views);


} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>