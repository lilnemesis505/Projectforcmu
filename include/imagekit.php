<?php
// 1. เรียกใช้ Autoloader ของ Composer
//    ไฟล์นี้จะโหลด SDK ของ ImageKit และ Library อื่นๆ อัตโนมัติ
require_once __DIR__ . '/../vendor/autoload.php';


// 3. Import Class ของ ImageKit
use ImageKit\ImageKit;

// 4. ตั้งค่าการเชื่อมต่อ ImageKit
//    (ไปที่ Dashboard ของ ImageKit เพื่อดูค่าเหล่านี้)
$publicKey = 'public_lie1wCIccnDe/6GyDv+OQR4EMiQ=';
$privateKey = 'private_LcCmMfHn8YmT86LwCf++yq3q/1w=';
$urlEndpoint = 'https://ik.imagekit.io/cmuxacademy';

// สร้าง Instance ของ ImageKit
// เราจะใช้ตัวแปร $imageKit นี้ในไฟล์อื่นๆ
$imageKit = new ImageKit(
    $publicKey,
    $privateKey,
    $urlEndpoint
);
?>