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

    // --- 6. สร้างตาราง 'auth_tokens' ---
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

    // --- 7. สร้างตาราง 'page_views' ---
    $sql_views = "
    CREATE TABLE IF NOT EXISTS `page_views` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `page_url` VARCHAR(255) NOT NULL,
        `user_agent` TEXT
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_views);

    // --- 8. (แก้ไข) สร้างตาราง Event 1-6 ---
    
    $sql_ev1 = "
    CREATE TABLE IF NOT EXISTS `ev1` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `image_url` VARCHAR(255) NOT NULL,
        `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"; // (ลบ comma ออก)
    $pdo->exec($sql_ev1);

     $sql_ev2 = "
    CREATE TABLE IF NOT EXISTS `ev2` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `image_url` VARCHAR(255) NOT NULL,
        `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"; // (ลบ comma ออก)
    $pdo->exec($sql_ev2);

     $sql_ev3 = "
    CREATE TABLE IF NOT EXISTS `ev3` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `image_url` VARCHAR(255) NOT NULL,
        `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"; // (ลบ comma ออก)
    $pdo->exec($sql_ev3);

     $sql_ev4 = "
    CREATE TABLE IF NOT EXISTS `ev4` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `image_url` VARCHAR(255) NOT NULL,
        `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"; // (ลบ comma ออก)
    $pdo->exec($sql_ev4);
    
     $sql_ev5 = "
    CREATE TABLE IF NOT EXISTS `ev5` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `image_url` VARCHAR(255) NOT NULL,
        `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"; // (ลบ comma ออก)
    $pdo->exec($sql_ev5);

     $sql_ev6 = "
    CREATE TABLE IF NOT EXISTS `ev6` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `image_url` VARCHAR(255) NOT NULL,
        `file_id` VARCHAR(100) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"; // (ลบ comma ออก)
    $pdo->exec($sql_ev6);
    $sql_meta = "
    CREATE TABLE IF NOT EXISTS `event_meta` (
      `event_key` VARCHAR(50) PRIMARY KEY,
      `event_title` VARCHAR(255) NULL,
      `event_description` TEXT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql_meta);

    // =======================================================
    // [เพิ่ม] 10. เพิ่มข้อมูลเริ่มต้นให้ 'event_meta' (ถ้ายังไม่มี)
    // =======================================================
    $sql_meta_data = "
    INSERT IGNORE INTO `event_meta` (`event_key`, `event_title`, `event_description`) 
    VALUES 
    ('ev1', 'หัวข้อ Event 1', 'รายละเอียด Event 1...'),
    ('ev2', 'หัวข้อ Event 2', 'รายละเอียด Event 2...'),
    ('ev3', 'หัวข้อ Event 3', 'รายละเอียด Event 3...'),
    ('ev4', 'หัวข้อ Event 4', 'รายละเอียด Event 4...'),
    ('ev5', 'หัวข้อ Event 5', 'รายละเอียด Event 5...'),
    ('ev6', 'หัวข้อ Event 6', 'รายละเอียด Event 6...');
    ";
    $pdo->exec($sql_meta_data);

} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int) $e->getCode());
}
?>