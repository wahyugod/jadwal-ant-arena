<?php
require_once 'config.php';
requireLogin();

// Konfigurasi
$assetsDir = __DIR__ . DIRECTORY_SEPARATOR . 'assets/galeri' . DIRECTORY_SEPARATOR;
$webAssetsPrefix = 'assets/galeri/';
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

$pageTitle = 'Kelola Galeri';
$pageBreadcrumb = 'Galeri';
include 'header.php';
?>
<div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: var(--text-primary);"><i class="bi bi-upload"></i> Tambah Foto
                        </h5>
                        <p class="mb-0" style="color: var(--text-secondary); font-size: 0.9rem;">Format: JPG, PNG, GIF,
                            WEBP (maks 10MB per file)</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data" class="d-flex flex-column gap-3">
                        <input type="hidden" name="action" value="upload">
                        <div>
                            <input class="form-control" type="file" name="photos[]" accept="image/*" multiple required>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-cloud-arrow-up"></i>
                                Unggah</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1" style="color: var(--text-primary);"><i class="bi bi-images"></i> Daftar Foto
                        </h5>
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
                                <div
                                    style="aspect-ratio:1/1; background:#f6f7fb; display:flex; align-items:center; justify-content:center;">
                                    <img src="<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($filename) ?>"
                                        style="max-width:100%; max-height:100%; object-fit:cover;">
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="small text-muted mb-2" title="<?= htmlspecialchars($filename) ?>"
                                        style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= htmlspecialchars($filename) ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <span class="badge bg-light text-dark border"><?= $sizeKb ?> KB</span>
                                        <form method="post" onsubmit="return confirm('Hapus foto ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="filename"
                                                value="<?= htmlspecialchars($filename) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i
                                                    class="bi bi-trash"></i></button>
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
<?php include 'footer.php'; ?>

