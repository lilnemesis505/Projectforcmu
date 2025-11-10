<?php
require_once 'include/db.php';
require_once 'include/config.php';

echo "<h1>Sync All ImageKit Data to Database</h1>";
echo "<hr>";

// ===================================================================
//  SECTION 1: SYNC SLIDESHOW (Table: slide)
// ===================================================================

echo "<h2>1. Syncing Slideshow (Source: /main/slide/)</h2>";
$pdo_slide = null; // (แยกตัวแปร PDO)

try {
    // 1a. ดึงจาก /main/slide/
    $path_slide = "/main/slide/";
    echo "<p>Fetching from: <code>{$path_slide}</code>...</p>";

    $fileList_slide = $imageKit->listFiles(['path' => $path_slide]);
    if (!$fileList_slide || !isset($fileList_slide->result)) {
        throw new Exception("Could not fetch file list from ImageKit.");
    }
    
    $files_slide = $fileList_slide->result;
    $fileCount_slide = count($files_slide);

    if ($fileCount_slide === 0) {
        echo "<p style='color: orange;'>⚠️ Found 0 files in path '{$path_slide}'. Sync skipped.</p>";
    } else {
        echo "<p style='color: blue;'>Found {$fileCount_slide} file(s).</p>";

        // (ใช้ $pdo จาก db.php)
        $pdo->beginTransaction();
        $pdo_slide = $pdo; // เก็บ reference ไว้เผื่อ rollback

        // 1b. ล้างตาราง 'slide'
        $pdo->exec("DELETE FROM slide;");
        echo "<p>Cleared 'slide' table.</p>";

        // 1c. INSERT ลง 'slide'
        $sql_slide = "INSERT INTO slide (image_url, file_id) VALUES (:url, :file_id)";
        $stmt_slide = $pdo->prepare($sql_slide);
        $insertedCount_slide = 0;
        
        foreach ($files_slide as $file) {
            $stmt_slide->execute(['url' => $file->url, 'file_id' => $file->fileId]);
            $insertedCount_slide++;
        }
        
        $pdo->commit();
        $pdo_slide = null; // สำเร็จ
        echo "<p style='color: green;'>✅ Successfully synced {$insertedCount_slide} record(s) to 'slide' table.</p>";
    }

} catch (Exception $e) {
    if ($pdo_slide && $pdo_slide->inTransaction()) {
        $pdo_slide->rollBack(); // Rollback เฉพาะส่วนนี้
    }
    echo "<h3 style='color: red;'>❌ ERROR (Slide Sync):</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

echo "<hr>";

// ===================================================================
//  SECTION 2: SYNC SIDE BANNERS (Table: banner)
// ===================================================================

echo "<h2>2. Syncing Side Banners (Source: /main/banner_*)</h2>";
$pdo_banner = null; // (แยกตัวแปร PDO)

try {
    // 2a. ดึงจาก /main/
    $path_banner = "/main/";
    echo "<p>Fetching from: <code>{$path_banner}</code>...</p>";
    
    $fileList_banner = $imageKit->listFiles(['path' => $path_banner]);
    if (!$fileList_banner || !isset($fileList_banner->result)) {
        throw new Exception("Could not fetch file list from ImageKit.");
    }

    // 2b. กรองไฟล์
    $allFiles_banner = $fileList_banner->result;
    $filteredFiles_banner = [];
    foreach ($allFiles_banner as $file) {
        if ($file->type === 'file' && strpos($file->name, 'banner_') === 0) {
            $filteredFiles_banner[] = $file;
        }
    }
    
    $fileCount_banner = count($filteredFiles_banner);
    if ($fileCount_banner === 0) {
        echo "<p style='color: orange;'>⚠️ Found 0 files matching 'banner_*' in path '{$path_banner}'. Sync skipped.</p>";
    } else {
        echo "<p style='color: blue;'>Found {$fileCount_banner} matching file(s).</p>";

        // (ใช้ $pdo จาก db.php)
        $pdo->beginTransaction();
        $pdo_banner = $pdo; // เก็บ reference ไว้เผื่อ rollback

        // 2c. ล้างตาราง 'banner'
        $pdo->exec("DELETE FROM banner;");
        echo "<p>Cleared 'banner' table.</p>";

        // 2d. INSERT ลง 'banner'
        $sql_banner = "INSERT INTO banner (image_url, file_id) VALUES (:url, :file_id)";
        $stmt_banner = $pdo->prepare($sql_banner);
        $insertedCount_banner = 0;
        
        foreach ($filteredFiles_banner as $file) {
            $stmt_banner->execute(['url' => $file->url, 'file_id' => $file->fileId]);
            $insertedCount_banner++;
        }
        
        $pdo->commit();
        $pdo_banner = null; // สำเร็จ
        echo "<p style='color: green;'>✅ Successfully synced {$insertedCount_banner} record(s) to 'banner' table.</p>";
    }

} catch (Exception $e) {
    if ($pdo_banner && $pdo_banner->inTransaction()) {
        $pdo_banner->rollBack(); // Rollback เฉพาะส่วนนี้
    }
    echo "<h3 style='color: red;'>❌ ERROR (Banner Sync):</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}

echo "<hr>";
echo "<h2>All sync tasks complete.</h2>";

?>