<?php
require_once 'config.php';
requireLogin();

// Konfigurasi
$assetsDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
$webAssetsPrefix = 'assets/';
$allowedExt = ['jpg','jpeg','png','gif','webp','JPG','JPEG','PNG','GIF','WEBP'];
$maxSize = 10 * 1024 * 1024; // 10MB per file

function sanitizeFileName($name) {
    // Hanya huruf, angka, dash, underscore dan titik
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
    // Hindari nama kosong atau hanya titik
    if ($name === '' || $name === '.' || $name === '..') {
        $name = 'file';
    }
    return $name;
}

function isAllowedExt($filename, $allowedExt) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    return in_array($ext, $allowedExt, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Upload
    if (isset($_POST['action']) && $_POST['action'] === 'upload' && isset($_FILES['photos'])) {
        $files = $_FILES['photos'];
        $count = is_array($files['name']) ? count($files['name']) : 0;
        $uploaded = 0;
        $errors = [];
        for ($i = 0; $i < $count; $i++) {
            $origName = $files['name'][$i];
            $tmpName = $files['tmp_name'][$i];
            $size = $files['size'][$i];
            $err  = $files['error'][$i];

            if ($err !== UPLOAD_ERR_OK) { $errors[] = "$origName: gagal upload (error $err)"; continue; }
            if ($size <= 0 || $size > $maxSize) { $errors[] = "$origName: ukuran tidak valid (maks 10MB)"; continue; }
            if (!isAllowedExt($origName, $allowedExt)) { $errors[] = "$origName: tipe file tidak diizinkan"; continue; }

            $safeBase = sanitizeFileName(pathinfo($origName, PATHINFO_FILENAME));
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            // Buat nama unik
            $unique = $safeBase . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.' . $ext;
            $dest = $assetsDir . $unique;

            if (!move_uploaded_file($tmpName, $dest)) {
                $errors[] = "$origName: gagal memindahkan file";
                continue;
            }
            $uploaded++;
        }
        if ($uploaded > 0) {
            $_SESSION['success_message'] = "$uploaded file berhasil diunggah";
        }
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode(' | ', $errors);
        }
        header('Location: admin-galeri.php');
        exit();
    }

    // Hapus
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['filename'])) {
        $basename = basename($_POST['filename']);
        $file = $assetsDir . $basename;
        // Validasi ekstensi & pastikan file ada di folder assets
        if (isAllowedExt($basename, $allowedExt) && is_file($file) && str_starts_with(realpath($file), realpath($assetsDir))) {
            if (@unlink($file)) {
                $_SESSION['success_message'] = "Berhasil menghapus $basename";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus $basename";
            }
        } else {
            $_SESSION['error_message'] = 'File tidak valid';
        }
        header('Location: admin-galeri.php');
        exit();
    }
}

// Ambil daftar gambar
$images = glob($assetsDir . '*.{jpg,jpeg,png,webp,gif,JPG,JPEG,PNG,WEBP,GIF}', GLOB_BRACE) ?: [];
// Urutkan terbaru di atas (berdasarkan waktu modifikasi)
usort($images, function($a, $b) { return filemtime($b) <=> filemtime($a); });
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - Ant Arena</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-calendar3"></i>
            <span>ANT ARENA</span>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="admin-dashboard.php">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
            <a class="nav-link" href="admin-dashboard.php#edit-jadwal">
                <i class="bi bi-calendar-week"></i>
                <span>Penjadwalan</span>
            </a>
            <a class="nav-link active" href="admin-galeri.php">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>
            <a class="nav-link" href="admin-transaksi.php">
                <i class="bi bi-cash-coin"></i>
                <span>Transaksi</span>
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 30px;">
            <a class="nav-link" href="index.php" target="_blank">
                <i class="bi bi-eye"></i>
                <span>Lihat Jadwal Publik</span>
            </a>
            <a class="nav-link" href="admin-logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn mobile-toggle me-3" id="sidebarToggle">
                    <i class="bi bi-list" style="font-size: 1.5rem;"></i>
                </button>
                <div>
                    <div id="pageBreadcrumb" style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Halaman / <span id="pageSectionName">Galeri</span></div>
                    <h1 id="pageTitle" class="m-0" style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">Galeri</h1>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2" style="background: white; padding: 10px 16px; border-radius: 12px; box-shadow: 0 4px 12px rgba(112, 144, 176, 0.08);">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--primary-gradient-start);"></i>
                    <span style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="container-fluid" style="padding: 30px;">
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); endif; ?>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: var(--text-primary);"><i class="bi bi-upload"></i> Tambah Foto</h5>
                        <p class="mb-0" style="color: var(--text-secondary); font-size: 0.9rem;">Format: JPG, PNG, GIF, WEBP (maks 10MB per file)</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                        <input type="hidden" name="action" value="upload">
                        <div>
                            <input class="form-control" type="file" name="photos[]" accept="image/*" multiple required>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i> Unggah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: var(--text-primary);"><i class="bi bi-images"></i> Daftar Foto</h5>
                        <p class="mb-0" style="color: var(--text-secondary); font-size: 0.9rem;">Terbaru di atas</p>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($images)): ?>
                        <div class="text-center text-muted">Belum ada foto di galeri.</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($images as $imgPath): 
                                $filename = basename($imgPath);
                                $url = $webAssetsPrefix . $filename;
                                $size = @filesize($imgPath);
                                $sizeKb = $size ? round($size/1024) : 0;
                            ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100" style="overflow:hidden; border-radius:16px;">
                                    <div style="aspect-ratio:1/1; background:#f6f7fb; display:flex; align-items:center; justify-content:center;">
                                        <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($filename) ?>" style="max-width:100%; max-height:100%; object-fit:cover;">
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="small text-muted mb-2" title="<?= htmlspecialchars($filename) ?>" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?= htmlspecialchars($filename) ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <span class="badge bg-light text-dark border"><?= $sizeKb ?> KB</span>
                                            <form method="post" onsubmit="return confirm('Hapus foto ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="filename" value="<?= htmlspecialchars($filename) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    });
    </script>
</body>
</html>
