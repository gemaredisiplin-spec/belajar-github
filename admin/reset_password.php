<?php
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new_password !== $confirm) {
        $error = "Password baru tidak cocok!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($old_password, $admin['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE admin SET password = ? WHERE username = ?");
            $update->execute([$new_hash, $username]);
            $success = "Password berhasil diubah! Silakan login dengan password baru.";
        } else {
            $error = "Username atau password lama salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Reset Password Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .reset-box {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="reset-box">
                    <h3 class="text-center mb-4">Reset Password Admin</h3>
                    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                        <a href="login.php" class="btn btn-primary w-100">Login Sekarang</a>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                            <div class="mb-3"><label>Password Lama</label><input type="password" name="old_password" class="form-control" required></div>
                            <div class="mb-3"><label>Password Baru</label><input type="password" name="new_password" class="form-control" required></div>
                            <div class="mb-3"><label>Konfirmasi Password Baru</label><input type="password" name="confirm_password" class="form-control" required></div>
                            <button type="submit" class="btn btn-primary w-100">Ubah Password</button>
                        </form>
                    <?php endif; ?>
                    <div class="text-center mt-3"><a href="login.php">Kembali ke Login</a></div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>