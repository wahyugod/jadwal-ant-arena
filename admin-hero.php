<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

// Ambil data hero (record pertama)
$hero = null;
$res = $conn->query('SELECT * FROM hero ORDER BY id ASC LIMIT 1');
if ($res) { $hero = $res->fetch_assoc(); $res->free_result(); }
$conn->close();
$heading = $hero['heading'] ?? 'Raih Kemenangan di Setiap Pukulan!';
$imagePath = $hero['image_path'] ?? 'assets/hero/home.jpeg';

$pageTitle = 'Kelola Hero';
$pageBreadcrumb = 'Hero';
include 'header.php';
?>
<div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Form Hero Beranda</h5>
                        </div>
                        <div class="card-body">
                            <form action="admin-hero-actions.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="heading" class="form-label">Judul / Heading</label>
                                    <input type="text" class="form-control" id="heading" name="heading"
                                        value="<?php echo htmlspecialchars($heading); ?>" required>
                                    <small class="text-muted">Teks ini akan muncul besar di beranda.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="images" class="form-label">Gambar Background (opsional - bisa pilih
                                        banyak)</label>
                                    <input type="file" class="form-control" id="images" name="images[]" accept="image/*"
                                        multiple>
                                    <small class="text-muted">Pilih satu atau lebih gambar. Akan otomatis jadi slider di
                                        beranda. Format: JPG, PNG, GIF, WEBP. Maks 5MB per file.</small>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan
                                    Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Gambar Hero Saat Ini</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $heroDir = __DIR__ . '/assets/hero/';
                            $heroFiles = glob($heroDir . '*.{jpg,jpeg,png,webp,gif,JPG,JPEG,PNG,WEBP,GIF}', GLOB_BRACE) ?: [];
                            usort($heroFiles, function($a,$b){ return filemtime($b) <=> filemtime($a); });
                            if (count($heroFiles) > 0) {
                                echo '<div class="row g-2">';
                                foreach ($heroFiles as $f) {
                                    $rel = 'assets/hero/' . basename($f);
                                    echo '<div class="col-6 mb-2">';
                                    echo '<img src="' . htmlspecialchars($rel) . '" alt="Hero" class="img-thumbnail" style="width:100%; height:100px; object-fit:cover;">';
                                    echo '<form method="POST" action="admin-hero-actions.php" class="mt-1" onsubmit="return confirm(\'Hapus gambar ini?\')">';
                                    echo '<input type="hidden" name="action" value="delete_image">';
                                    echo '<input type="hidden" name="filename" value="' . htmlspecialchars(basename($f)) . '">';
                                    echo '<button type="submit" class="btn btn-sm btn-danger w-100"><i class="bi bi-trash"></i> Hapus</button>';
                                    echo '</form>';
                                    echo '</div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<p class="text-muted">Belum ada gambar hero.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
<?php include 'footer.php'; ?>

