<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Ambil data settings untuk semua halaman
$stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmt->fetch();
if (!$settings) {
    // Data default jika belum ada
    $settings = [
        'profile_name' => 'My Portfolio',
        'profile_photo' => 'default.jpg',
        'profile_description' => 'Selamat datang di portofolio saya.',
        'age' => 20,
        'school' => 'SMK Negeri 1 Depok',
        'major' => 'Pengembangan perangkat lunak dan gim',
        'contact_email' => 'geraldmarentek08@gmail.com'
        'contact_phone' => '081292221003',
        'contact_address' => 'Depok'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | <?php echo htmlspecialchars($settings['profile_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">MyPortfolio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#skills">Skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="guestbook.php">Guest Book</a></li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-outline-primary ms-2 px-3" href="admin/login.php" style="border-radius: 50px;">
                            <i class="bi bi-shield-lock"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main>
