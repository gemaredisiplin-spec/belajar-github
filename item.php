<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM portfolio_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item):
    header("Location: portfolio.php");
    exit;
endif;

$category_name = '';
if ($item['category'] == 'project') $category_name = 'Projek';
elseif ($item['category'] == 'prestasi') $category_name = 'Prestasi';
else $category_name = 'Pengalaman';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <?php if (!empty($item['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                    <p class="text-muted">Kategori: <?php echo $category_name; ?></p>
                    <hr>
                    <div><?php echo nl2br(htmlspecialchars($item['content_detail'])); ?></div>
                    <a href="portfolio.php" class="btn btn-primary mt-4">Kembali ke Portfolio</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>