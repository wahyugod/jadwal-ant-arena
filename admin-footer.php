<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

$footer = null;
$res = $conn->query('SELECT * FROM footer ORDER BY id ASC LIMIT 1');
if ($res) { $footer = $res->fetch_assoc(); $res->free_result(); }
$conn->close();

$address = $footer['address'] ?? 'Jl. Rejang Raya Gg Barokah, Bukit Pinang, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75131';
$phone = $footer['phone'] ?? '+62 812-3456-7890';
$email = $footer['email'] ?? 'info@nts-arena.com';
$description = $footer['description'] ?? '@nt\'s Arena adalah lapangan bulutangkis terbaik di Samarinda dengan fasilitas modern dan pelayanan profesional.';
$instagram = $footer['instagram'] ?? 'https://instagram.com/ntsarena';
$facebook = $footer['facebook'] ?? '#';
$twitter = $footer['twitter'] ?? '#';
$linkedin = $footer['linkedin'] ?? '#';
$hours_weekday = $footer['hours_weekday'] ?? 'Senin-Jumat: 8 Pagi - 11 Malam';
$hours_weekend = $footer['hours_weekend'] ?? 'Sabtu-Minggu: 8 Pagi - 11 Malam';
$copyright = $footer['copyright'] ?? '© 2025 @nt\'s Arena - Semua Hak Dilindungi';

$pageTitle = 'Kelola Footer';
$pageBreadcrumb = 'Footer';
include 'header.php';
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-body p-4">
                <h5 class="card-title mb-4" style="font-weight:700; color:var(--text-primary);">Edit
                    Informasi Footer</h5>
                <form action="admin-footer-actions.php" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Singkat</label>
                        <textarea class="form-control" name="description" rows="3"
                            required><?= htmlspecialchars($description) ?></textarea>
                        <div class="form-text">Deskripsi singkat tentang @nt's Arena yang ditampilkan di footer.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jam Operasional (Weekday)</label>
                            <input type="text" class="form-control" name="hours_weekday"
                                value="<?= htmlspecialchars($hours_weekday) ?>" required>
                            <div class="form-text">Contoh: Senin-Jumat: 8 Pagi - 11 Malam</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Jam Operasional (Weekend)</label>
                            <input type="text" class="form-control" name="hours_weekend"
                                value="<?= htmlspecialchars($hours_weekend) ?>" required>
                            <div class="form-text">Contoh: Sabtu-Minggu: 8 Pagi - 11 Malam</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teks Copyright</label>
                        <input type="text" class="form-control" name="copyright"
                            value="<?= htmlspecialchars($copyright) ?>" required>
                        <div class="form-text">Teks copyright yang ditampilkan di bagian bawah footer.</div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3" style="font-weight:600; color:var(--text-primary);">Media Sosial</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-instagram text-danger"></i>
                            Instagram</label>
                        <input type="url" class="form-control" name="instagram"
                            value="<?= htmlspecialchars($instagram) ?>" placeholder="https://instagram.com/username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-facebook text-primary"></i>
                            Facebook</label>
                        <input type="url" class="form-control" name="facebook"
                            value="<?= htmlspecialchars($facebook) ?>" placeholder="https://facebook.com/username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-twitter-x"></i>
                            Twitter/X</label>
                        <input type="url" class="form-control" name="twitter" value="<?= htmlspecialchars($twitter) ?>"
                            placeholder="https://twitter.com/username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-linkedin text-info"></i>
                            LinkedIn</label>
                        <input type="url" class="form-control" name="linkedin"
                            value="<?= htmlspecialchars($linkedin) ?>" placeholder="https://linkedin.com/company/name">
                    </div>
                    <div class="d-flex gap-2 mt-4 justify-content-end">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-2"></i>Simpan
                            Perubahan</button>
                        <a href="admin-dashboard.php" class="btn btn-danger px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-body p-4">
                <h6 class="card-title mb-3" style="font-weight:600;">Pratinjau Footer</h6>
                <div class="border rounded p-3" style="background:#f8f9fa; font-size:0.85rem;">
                    <div class="mb-3">
                        <strong>Deskripsi:</strong>
                        <p class="mb-0 mt-1 text-muted"><?= htmlspecialchars($description) ?></p>
                    </div>
                    <div class="mb-3">
                        <strong>Jam Operasional:</strong>
                        <p class="mb-0 mt-1 text-muted">
                            <?= htmlspecialchars($hours_weekday) ?><br>
                            <?= htmlspecialchars($hours_weekend) ?>
                        </p>
                    </div>
                    <div class="mb-3">
                        <strong>Copyright:</strong>
                        <p class="mb-0 mt-1 text-muted"><?= htmlspecialchars($copyright) ?></p>
                    </div>
                    <div>
                        <strong>Media Sosial:</strong>
                        <div class="mt-2">
                            <?php if($instagram !== '#'): ?>
                            <span class="badge bg-danger me-1"><i class="bi bi-instagram"></i> Instagram</span>
                            <?php endif; ?>
                            <?php if($facebook !== '#'): ?>
                            <span class="badge bg-primary me-1"><i class="bi bi-facebook"></i> Facebook</span>
                            <?php endif; ?>
                            <?php if($twitter !== '#'): ?>
                            <span class="badge bg-dark me-1"><i class="bi bi-twitter-x"></i> Twitter</span>
                            <?php endif; ?>
                            <?php if($linkedin !== '#'): ?>
                            <span class="badge bg-info me-1"><i class="bi bi-linkedin"></i> LinkedIn</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3" style="border-radius:16px;">
            <div class="card-body p-4">
                <h6 class="card-title mb-3" style="font-weight:600;"><i class="bi bi-info-circle me-2"></i>Informasi
                </h6>
                <ul class="small mb-0 ps-3">
                    <li class="mb-2">Menu cepat dibuat otomatis dari navigasi utama</li>
                    <li class="mb-2">Pastikan URL media sosial lengkap dengan https://</li>
                    <li class="mb-2">Gunakan # untuk menyembunyikan link media sosial</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>