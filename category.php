<?php
require_once 'includes/header.php';

$cat = isset($_GET['cat']) ? $_GET['cat'] : 'project';
$allowed = ['project', 'prestasi', 'pengalaman'];
if (!in_array($cat, $allowed)) $cat = 'project';

$category_name = '';
if ($cat == 'project') $category_name = 'Projek';
elseif ($cat == 'prestasi') $category_name = 'Prestasi';
else $category_name = 'Pengalaman';

$stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE category = ? ORDER BY created_at DESC");
$stmt->execute([$cat]);
$items = $stmt->fetchAll();
?>

<section class="py-5">
    <div class="container">
        <h2 class="section-title"><?php echo $category_name; ?></h2>
        <div class="text-center mb-5">
            <a href="portfolio.php" class="btn btn-outline-primary">&larr; Kembali ke Portfolio</a>
        </div>

        <div class="row">
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="portfolio-card" onclick="window.location.href='item.php?id=<?php echo $item['id']; ?>'">
                            <img src="uploads/<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'default.jpg'; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <div class="p-3">
                                <h5><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p><?php echo substr(htmlspecialchars($item['description']), 0, 100); ?>...</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Belum ada data di kategori ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>