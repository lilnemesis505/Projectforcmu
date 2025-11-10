<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: ../dashboard.php');
    exit;
}

require_once '../../include/db.php';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    if (empty($username) || empty($password)) {
        $error_message = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // สำเร็จ!
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];

            // (ใหม่) ถ้าติ๊ก "จดจำฉัน"
            if ($remember_me) {
                // 1. สร้าง Tokens
                $selector = bin2hex(random_bytes(16));
                $validator = bin2hex(random_bytes(32));
                $hashed_validator = password_hash($validator, PASSWORD_DEFAULT);
                $expires = time() + (86400 * 30); // 30 วัน

                // 2. บันทึกลง DB
                $sql = "INSERT INTO auth_tokens (admin_id, selector, hashed_validator, expires) 
                        VALUES (?, ?, ?, ?)";
                $stmt_token = $pdo->prepare($sql);
                $stmt_token->execute([
                    $user['id'], 
                    $selector, 
                    $hashed_validator, 
                    date('Y-m-d H:i:s', $expires)
                ]);

                // 3. ตั้ง Cookie (httpOnly: true เพื่อความปลอดภัย)
                setcookie('remember_me', $selector . ':' . $validator, $expires, '/', '', false, true);
            }

            header('Location: ../dashboard.php');
            exit;
        } else {
            $error_message = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin-top: 100px; }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card shadow">
            <div class="card-body p-5">
                <h2 class="text-center mb-4">Admin Login</h2>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">รหัสผ่าน</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me">
                        <label class="form-check-label" for="remember_me">
                            จดจำฉันไว้ในระบบ
                        </label>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>