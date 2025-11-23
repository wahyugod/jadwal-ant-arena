<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

// Ambil data about (record pertama)
$about = null;
$res = $conn->query('SELECT * FROM about ORDER BY id ASC LIMIT 1');
if ($res) { $about = $res->fetch_assoc(); $res->free_result(); }
$conn->close();

$imagePath = $about['image_path'] ?? 'assets/img/about.jpeg';
$paragraph1 = $about['paragraph_1'] ?? '';
$paragraph2 = $about['paragraph_2'] ?? '';
$paragraph3 = $about['paragraph_3'] ?? '';

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
                                    <label for="image" class="form-label">Gambar (opsional)</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti. Format: JPG,
                                        PNG, GIF, WEBP. Maks 5MB.</small>
                                </div>
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
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan
                                    Perubahan</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Pratinjau Gambar Saat Ini</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="About"
                                style="width:100%; max-height:300px; object-fit:cover; border-radius:8px;">
                            <p class="mt-2 mb-0"><small class="text-muted">Path:
                                    <?php echo htmlspecialchars($imagePath); ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
<?php include 'footer.php'; ?>

