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
$instagram = $footer['instagram'] ?? 'https://instagram.com/ntsarena';
$facebook = $footer['facebook'] ?? '#';
$twitter = $footer['twitter'] ?? '#';
$linkedin = $footer['linkedin'] ?? '#';
$hours_weekday = $footer['hours_weekday'] ?? 'Senin-Jumat: 8 Pagi - 11 Malam';
$hours_weekend = $footer['hours_weekend'] ?? 'Sabtu-Minggu: 8 Pagi - 11 Malam';

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
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea class="form-control" name="address" rows="3"
                            required><?= htmlspecialchars($address) ?></textarea>
                        <div class="form-text">Alamat lengkap yang ditampilkan di footer.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Telepon</label>
                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($phone) ?>"
                                required>
                            <div class="form-text">Format: +62 812-3456-7890</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="<?= htmlspecialchars($email) ?>" required>
                            <div class="form-text">Email untuk kontak.</div>
                        </div>
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
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-2"></i>Simpan
                            Perubahan</button>
                        <a href="admin-dashboard.php" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm"
            style="border-radius:16px; background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white;">
            <div class="card-body p-4">
                <h6 class="mb-3" style="font-weight:700;"><i class="bi bi-eye me-2"></i>Preview Footer</h6>
                <div class="mb-3">
                    <small class="text-white-50 d-block mb-1">ALAMAT</small>
                    <div style="font-size:0.9rem;"><?= nl2br(htmlspecialchars($address)) ?></div>
                </div>
                <div class="mb-3">
                    <small class="text-white-50 d-block mb-1">KONTAK</small>
                    <div style="font-size:0.9rem;">
                        <strong>Telepon:</strong> <?= htmlspecialchars($phone) ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($email) ?>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-white-50 d-block mb-1">JAM OPERASIONAL</small>
                    <div style="font-size:0.9rem;">
                        <?= htmlspecialchars($hours_weekday) ?><br>
                        <?= htmlspecialchars($hours_weekend) ?>
                    </div>
                </div>
                <div>
                    <small class="text-white-50 d-block mb-2">MEDIA SOSIAL</small>
                    <div class="d-flex gap-2">
                        <?php if ($instagram && $instagram !== '#'): ?><a href="<?= htmlspecialchars($instagram) ?>"
                            class="btn btn-sm btn-light" target="_blank"><i
                                class="bi bi-instagram"></i></a><?php endif; ?>
                        <?php if ($facebook && $facebook !== '#'): ?><a href="<?= htmlspecialchars($facebook) ?>"
                            class="btn btn-sm btn-light" target="_blank"><i
                                class="bi bi-facebook"></i></a><?php endif; ?>
                        <?php if ($twitter && $twitter !== '#'): ?><a href="<?= htmlspecialchars($twitter) ?>"
                            class="btn btn-sm btn-light" target="_blank"><i
                                class="bi bi-twitter-x"></i></a><?php endif; ?>
                        <?php if ($linkedin && $linkedin !== '#'): ?><a href="<?= htmlspecialchars($linkedin) ?>"
                            class="btn btn-sm btn-light" target="_blank"><i
                                class="bi bi-linkedin"></i></a><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>