<?php
require_once 'includes/header.php';

// Ambil data dari database
$stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmt->fetch();

$skills = $pdo->query("SELECT * FROM skills ORDER BY id")->fetchAll();
?>
<style>
    .hero-section {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    }
</style>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 order-md-2 text-center">
                <?php
                $photo = !empty($settings['profile_photo']) ? $settings['profile_photo'] : 'default.jpg';
                ?>
                <img src="uploads/<?php echo htmlspecialchars($photo); ?>" alt="Profile" class="profile-img mb-4">
            </div>
            <div class="col-md-6 order-md-1">
                <h1 class="display-4 fw-bold">Halo, Saya <span class="text-gradient"><?php echo htmlspecialchars($settings['profile_name']); ?></span> 👋</h1>
                <p class="lead mt-3"><?php echo nl2br(htmlspecialchars($settings['profile_description'])); ?></p>
                <a href="#about" class="btn btn-primary mt-3">Tentang Saya <i class="bi bi-arrow-down"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <h2 class="section-title">About Me</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="bi bi-person-fill me-2 text-primary"></i> <strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($settings['profile_name']); ?></p>
                            <p><i class="bi bi-calendar-fill me-2 text-primary"></i> <strong>Umur:</strong> <?php echo htmlspecialchars($settings['age']); ?> tahun</p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="bi bi-building-fill me-2 text-primary"></i> <strong>Sekolah:</strong> <?php echo htmlspecialchars($settings['school']); ?></p>
                            <p><i class="bi bi-laptop-fill me-2 text-primary"></i> <strong>Jurusan:</strong> <?php echo htmlspecialchars($settings['major']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section id="skills" class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">Skills Programming</h2>
        <div class="row g-4">
            <?php foreach ($skills as $skill): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="skill-card">
                        <i class="bi bi-<?php echo htmlspecialchars($skill['icon']); ?>"></i>
                        <h5 class="mt-3"><?php echo htmlspecialchars($skill['name']); ?></h5>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5">
    <div class="container">
        <h2 class="section-title">Kontak Saya</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 text-center">
                    <p><i class="bi bi-envelope-fill fs-1 text-primary"></i><br><strong>Email:</strong> <?php echo htmlspecialchars($settings['contact_email']); ?></p>
                    <p><i class="bi bi-telephone-fill fs-1 text-primary"></i><br><strong>Telepon:</strong> <?php echo htmlspecialchars($settings['contact_phone']); ?></p>
                    <p><i class="bi bi-geo-alt-fill fs-1 text-primary"></i><br><strong>Alamat:</strong> <?php echo htmlspecialchars($settings['contact_address']); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>