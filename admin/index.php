<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Ambil data settings
$stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
$settings = $stmt->fetch();

// Proses update profile
if (isset($_POST['update_profile'])) {
    $profile_name = $_POST['profile_name'];
    $profile_description = $_POST['profile_description'];
    $age = $_POST['age'];
    $school = $_POST['school'];
    $major = $_POST['major'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    $contact_address = $_POST['contact_address'];

    // Handle file upload dengan lebih aman
    $photo = $settings['profile_photo']; // retain existing
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "../uploads/";
        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = strtolower(pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($file_extension, $allowed_extensions)) {
            $file_name = time() . "_" . uniqid() . "." . $file_extension;
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                // Hapus foto lama jika bukan default
                if (!empty($settings['profile_photo']) && $settings['profile_photo'] != 'default.jpg' && file_exists($target_dir . $settings['profile_photo'])) {
                    unlink($target_dir . $settings['profile_photo']);
                }
                $photo = $file_name;
            } else {
                // error upload
                $_SESSION['upload_error'] = "Gagal mengupload file.";
            }
        } else {
            $_SESSION['upload_error'] = "Ekstensi file tidak diizinkan (jpg, jpeg, png, gif, webp).";
        }
    }

    $stmt = $pdo->prepare("UPDATE settings SET profile_name=?, profile_photo=?, profile_description=?, age=?, school=?, major=?, contact_email=?, contact_phone=?, contact_address=? WHERE id=1");
    $stmt->execute([$profile_name, $photo, $profile_description, $age, $school, $major, $contact_email, $contact_phone, $contact_address]);

    header("Location: index.php?msg=profile_updated");
    exit;
}

// CRUD Skills
if (isset($_POST['add_skill'])) {
    $name = $_POST['skill_name'];
    $icon = $_POST['skill_icon'];
    $stmt = $pdo->prepare("INSERT INTO skills (name, icon) VALUES (?, ?)");
    $stmt->execute([$name, $icon]);
    header("Location: index.php?msg=skill_added");
    exit;
}

if (isset($_GET['delete_skill'])) {
    $id = $_GET['delete_skill'];
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=skill_deleted");
    exit;
}

if (isset($_POST['edit_skill'])) {
    $id = $_POST['skill_id'];
    $name = $_POST['skill_name'];
    $icon = $_POST['skill_icon'];
    $stmt = $pdo->prepare("UPDATE skills SET name=?, icon=? WHERE id=?");
    $stmt->execute([$name, $icon, $id]);
    header("Location: index.php?msg=skill_updated");
    exit;
}

// CRUD Portfolio
if (isset($_POST['add_portfolio'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $content_detail = $_POST['content_detail'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO portfolio_items (title, category, description, content_detail, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $category, $description, $content_detail, $image]);
    header("Location: index.php?msg=portfolio_added");
    exit;
}

if (isset($_GET['delete_portfolio'])) {
    $id = $_GET['delete_portfolio'];
    $stmt = $pdo->prepare("DELETE FROM portfolio_items WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=portfolio_deleted");
    exit;
}

if (isset($_POST['edit_portfolio'])) {
    $id = $_POST['portfolio_id'];
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $content_detail = $_POST['content_detail'];

    $image = $_POST['existing_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $file_name;
        }
    }

    $stmt = $pdo->prepare("UPDATE portfolio_items SET title=?, category=?, description=?, content_detail=?, image=? WHERE id=?");
    $stmt->execute([$title, $category, $description, $content_detail, $image, $id]);
    header("Location: index.php?msg=portfolio_updated");
    exit;
}

// Hapus guestbook message
if (isset($_GET['delete_message'])) {
    $id = $_GET['delete_message'];
    $stmt = $pdo->prepare("DELETE FROM guestbook_messages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php?msg=message_deleted");
    exit;
}

// Ambil data
$skills = $pdo->query("SELECT * FROM skills ORDER BY id")->fetchAll();
$portfolio_items = $pdo->query("SELECT * FROM portfolio_items ORDER BY created_at DESC")->fetchAll();
$guestbook = $pdo->query("SELECT * FROM guestbook_messages ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/admin-style.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <h4 class="text-white mb-4">Admin Panel</h4>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#profile">Edit Profile</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#skills">Manage Skills</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#portfolio">Manage Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#guestbook">Guestbook</a></li>
                </ul>
                <hr class="bg-light">
                <a href="logout.php" class="btn btn-danger w-100">Logout</a>
            </div>

            <div class="col-md-9 content">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        $msg = $_GET['msg'];
                        if ($msg == 'profile_updated') echo "Profile berhasil diupdate!";
                        elseif ($msg == 'skill_added') echo "Skill berhasil ditambahkan!";
                        elseif ($msg == 'skill_deleted') echo "Skill berhasil dihapus!";
                        elseif ($msg == 'skill_updated') echo "Skill berhasil diupdate!";
                        elseif ($msg == 'portfolio_added') echo "Portfolio berhasil ditambahkan!";
                        elseif ($msg == 'portfolio_deleted') echo "Portfolio berhasil dihapus!";
                        elseif ($msg == 'portfolio_updated') echo "Portfolio berhasil diupdate!";
                        elseif ($msg == 'message_deleted') echo "Pesan berhasil dihapus!";
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <h3>Edit Profile & Biodata</h3>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" name="profile_name" class="form-control" value="<?php echo htmlspecialchars($settings['profile_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Foto Profil</label>
                                <input type="file" name="profile_photo" class="form-control" accept="image/*">
                                <small>Kosongkan jika tidak ingin mengganti</small>
                            </div>
                            <div class="mb-3">
                                <label>Deskripsi Singkat</label>
                                <textarea name="profile_description" rows="3" class="form-control" required><?php echo htmlspecialchars($settings['profile_description']); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Umur</label>
                                    <input type="number" name="age" class="form-control" value="<?php echo $settings['age']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Sekolah</label>
                                    <input type="text" name="school" class="form-control" value="<?php echo htmlspecialchars($settings['school']); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Jurusan</label>
                                <input type="text" name="major" class="form-control" value="<?php echo htmlspecialchars($settings['major']); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Email Kontak</label>
                                <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Telepon</label>
                                <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                            </div>
                            <div class="mb-3">
                                <label>Alamat</label>
                                <textarea name="contact_address" rows="2" class="form-control"><?php echo htmlspecialchars($settings['contact_address']); ?></textarea>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>

                    <!-- Skills Tab -->
                    <div class="tab-pane fade" id="skills">
                        <h3>Manage Skills</h3>
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addSkillModal">Tambah Skill</button>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Skill</th>
                                    <th>Icon</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($skills as $skill): ?>
                                    <tr>
                                        <td><?php echo $skill['id']; ?></td>
                                        <td><?php echo htmlspecialchars($skill['name']); ?></td>
                                        <td><i class="bi bi-<?php echo $skill['icon']; ?>"></i> <?php echo $skill['icon']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSkillModal<?php echo $skill['id']; ?>">Edit</button>
                                            <a href="?delete_skill=<?php echo $skill['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editSkillModal<?php echo $skill['id']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <div class="modal-header">
                                                        <h5>Edit Skill</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="skill_id" value="<?php echo $skill['id']; ?>">
                                                        <div class="mb-3"><label>Nama Skill</label><input type="text" name="skill_name" class="form-control" value="<?php echo htmlspecialchars($skill['name']); ?>" required></div>
                                                        <div class="mb-3"><label>Icon (contoh: code-slash, filetype-html)</label><input type="text" name="skill_icon" class="form-control" value="<?php echo $skill['icon']; ?>" required></div>
                                                    </div>
                                                    <div class="modal-footer"><button type="submit" name="edit_skill" class="btn btn-primary">Update</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Portfolio Tab -->
                    <div class="tab-pane fade" id="portfolio">
                        <h3>Manage Portfolio</h3>
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPortfolioModal">Tambah Portfolio</button>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($portfolio_items as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo $item['category']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPortfolioModal<?php echo $item['id']; ?>">Edit</button>
                                            <a href="?delete_portfolio=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                                        </td>
                                    </tr>

                                    <!-- Edit Portfolio Modal -->
                                    <div class="modal fade" id="editPortfolioModal<?php echo $item['id']; ?>">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form method="POST" enctype="multipart/form-data">
                                                    <div class="modal-header">
                                                        <h5>Edit Portfolio</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="portfolio_id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="existing_image" value="<?php echo $item['image']; ?>">
                                                        <div class="mb-3"><label>Judul</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($item['title']); ?>" required></div>
                                                        <div class="mb-3">
                                                            <label>Kategori</label>
                                                            <select name="category" class="form-control" required>
                                                                <option value="project" <?php echo $item['category'] == 'project' ? 'selected' : ''; ?>>Project</option>
                                                                <option value="prestasi" <?php echo $item['category'] == 'prestasi' ? 'selected' : ''; ?>>Prestasi</option>
                                                                <option value="pengalaman" <?php echo $item['category'] == 'pengalaman' ? 'selected' : ''; ?>>Pengalaman</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3"><label>Deskripsi Singkat</label><textarea name="description" class="form-control" required><?php echo htmlspecialchars($item['description']); ?></textarea></div>
                                                        <div class="mb-3"><label>Konten Detail</label><textarea name="content_detail" rows="5" class="form-control" required><?php echo htmlspecialchars($item['content_detail']); ?></textarea></div>
                                                        <div class="mb-3"><label>Gambar</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                                                    </div>
                                                    <div class="modal-footer"><button type="submit" name="edit_portfolio" class="btn btn-primary">Update</button></div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Guestbook Tab -->
                    <div class="tab-pane fade" id="guestbook">
                        <h3>Guestbook Messages</h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Pesan</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($guestbook as $msg): ?>
                                    <tr>
                                        <td><?php echo $msg['id']; ?></td>
                                        <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                        <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                        <td><?php echo substr(htmlspecialchars($msg['message']), 0, 50); ?>...</td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?></td>
                                        <td><a href="?delete_message=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus pesan ini?')">Hapus</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Skill -->
    <div class="modal fade" id="addSkillModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5>Tambah Skill</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Nama Skill</label><input type="text" name="skill_name" class="form-control" required></div>
                        <div class="mb-3"><label>Icon (contoh: code-slash, filetype-html)</label><input type="text" name="skill_icon" class="form-control" value="code-slash" required></div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="add_skill" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Add Portfolio -->
    <div class="modal fade" id="addPortfolioModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5>Tambah Portfolio</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label>Judul</label><input type="text" name="title" class="form-control" required></div>
                        <div class="mb-3"><label>Kategori</label><select name="category" class="form-control" required>
                                <option value="project">Project</option>
                                <option value="prestasi">Prestasi</option>
                                <option value="pengalaman">Pengalaman</option>
                            </select></div>
                        <div class="mb-3"><label>Deskripsi Singkat</label><textarea name="description" class="form-control" required></textarea></div>
                        <div class="mb-3"><label>Konten Detail</label><textarea name="content_detail" rows="5" class="form-control" required></textarea></div>
                        <div class="mb-3"><label>Gambar</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                    </div>
                    <div class="modal-footer"><button type="submit" name="add_portfolio" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>