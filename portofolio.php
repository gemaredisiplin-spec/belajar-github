<?php
require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <h2 class="section-title">Portfolio Saya</h2>
        <div class="text-center mb-5">
            <a href="category.php?cat=project" class="category-btn">📁 Projek</a>
            <a href="category.php?cat=prestasi" class="category-btn">🏆 Prestasi</a>
            <a href="category.php?cat=pengalaman" class="category-btn">💼 Pengalaman</a>
        </div>

        <div class="row">
            <?php
            // Ambil semua portfolio items
            $stmt = $pdo->query("SELECT * FROM portfolio_items ORDER BY created_at DESC LIMIT 6");
            $items = $stmt->fetchAll();

            if (count($items) > 0):
                foreach ($items as $item):
                    $badge = ($item['category'] == 'project') ? 'bg-primary' : (($item['category'] == 'prestasi') ? 'bg-warning' : 'bg-success');
                    $category_name = ucfirst($item['category']);
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="portfolio-card" onclick="window.location.href='item.php?id=<?php echo $item['id']; ?>'">
                            <img src="uploads/<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="p-3">
                                <span class="badge <?php echo $badge; ?> mb-2"><?php echo $category_name; ?></span>
                                <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p class="text-muted"><?php echo substr(htmlspecialchars($item['description']), 0, 100); ?>...</p>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            else:
                ?>
                <div class="col-12 text-center">
                    <p>Belum ada portfolio. Silakan tambahkan melalui admin panel.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>