<?php require_once 'includes/header.php';

// Ambil semua pesan
$messages = $pdo->query("SELECT * FROM guestbook_messages ORDER BY created_at DESC")->fetchAll();
?>

<section class="py-5">
    <div class="container">
        <h2 class="section-title">Guest Book</h2>

        <div class="row">
            <div class="col-md-6">
                <div class="card p-4 mb-4">
                    <h4>Tulis Pesan</h4>
                    <form action="submit_guestbook.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Nama Anda" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email Anda" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="message" rows="4" class="form-control" placeholder="Pesan Anda..." required></textarea>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Kirim Pesan</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <h4>Pesan dari Pengunjung</h4>
                <?php foreach ($messages as $msg): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><?php echo htmlspecialchars($msg['name']); ?>
                                <small class="text-muted"><?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?></small>
                            </h6>
                            <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>