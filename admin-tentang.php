<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

// Ambil data about (record pertama)
$about = null;
$res = $conn->query('SELECT * FROM about ORDER BY id ASC LIMIT 1');
if ($res) { $about = $res->fetch_assoc(); $res->free_result(); }
$conn->close();

$paragraph1 = $about['paragraph_1'] ?? '';
$paragraph2 = $about['paragraph_2'] ?? '';
$paragraph3 = $about['paragraph_3'] ?? '';

// Ambil gambar tentang dari folder
$tentangImages = glob(__DIR__ . '/assets/tentang/*.{jpg,jpeg,png,webp,gif,JPG,JPEG,PNG,WEBP,GIF}', GLOB_BRACE) ?: [];
// Urutkan terbaru dulu
usort($tentangImages, function($a,$b){ return filemtime($b) <=> filemtime($a); });

$pageTitle = 'Kelola Tentang';
$pageBreadcrumb = 'Tentang';
include 'header.php';
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Form Tentang Kami</h5>
            </div>
            <div class="card-body">
                <form action="admin-tentang-actions.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="paragraph_1" class="form-label">Paragraf 1</label>
                        <textarea class="form-control" id="paragraph_1" name="paragraph_1" rows="4"
                            required><?php echo htmlspecialchars($paragraph1); ?></textarea>
                        <small class="text-muted">Gunakan &lt;strong&gt; untuk teks tebal.</small>
                    </div>
                    <div class="mb-3">
                        <label for="paragraph_2" class="form-label">Paragraf 2</label>
                        <textarea class="form-control" id="paragraph_2" name="paragraph_2" rows="4"
                            required><?php echo htmlspecialchars($paragraph2); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="paragraph_3" class="form-label">Paragraf 3</label>
                        <textarea class="form-control" id="paragraph_3" name="paragraph_3" rows="4"
                            required><?php echo htmlspecialchars($paragraph3); ?></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="action" value="update_text" class="btn btn-primary"><i
                            class="bi bi-save"></i> Simpan
                        Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload Gambar Slider -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gambar Slider Tentang</h5>
                <span class="badge bg-info"><?= count($tentangImages) ?> gambar</span>
            </div>
            <div class="card-body">
                <form action="admin-tentang-actions.php" method="POST" enctype="multipart/form-data" class="mb-4">
                    <div class="mb-3">
                        <label for="images" class="form-label">Upload Gambar (dapat pilih banyak)</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple
                            required>
                        <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Maks 5MB per file. Dapat upload banyak
                            sekaligus.</small>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="action" value="upload_images" class="btn btn-success"><i
                            class="bi bi-upload"></i> Upload Gambar</button>
                    </div>
                </form>

                <div class="row g-3">
                    <?php if (!empty($tentangImages)): ?>
                    <?php foreach ($tentangImages as $imgPath): 
                                        $filename = basename($imgPath);
                                        $relPath = 'assets/tentang/' . $filename;
                                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($relPath) ?>" class="card-img-top" alt="Tentang"
                                style="height:150px; object-fit:cover;">
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate"
                                    title="<?= htmlspecialchars($filename) ?>"><?= htmlspecialchars($filename) ?></small>
                                <form action="admin-tentang-actions.php" method="POST" class="mt-2"
                                    onsubmit="return confirm('Yakin hapus gambar ini?')">
                                    <input type="hidden" name="action" value="delete_image">
                                    <input type="hidden" name="filename" value="<?= htmlspecialchars($filename) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm w-100"><i
                                            class="bi bi-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Belum ada gambar. Upload gambar pertama Anda.
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Pratinjau Slider</h5>
            </div>
            <div class="card-body text-center">
                <?php if (!empty($tentangImages)): ?>
                <div id="carouselPreview" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($tentangImages as $idx => $imgPath): 
                                            $relPath = 'assets/tentang/' . basename($imgPath);
                                        ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                            <img src="<?= htmlspecialchars($relPath) ?>" class="d-block w-100" alt="Preview"
                                style="max-height:300px; object-fit:cover; border-radius:8px;">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($tentangImages) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPreview"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselPreview"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <p class="text-muted">Belum ada gambar untuk ditampilkan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>