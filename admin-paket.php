<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

$res = $conn->query('SELECT * FROM paket ORDER BY urutan ASC, id ASC');
$rows = [];
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } $res->free_result(); }
$conn->close();

$pageTitle = 'Kelola Paket';
$pageBreadcrumb = 'Paket';
include 'header.php';
?>
<div class="mb-4 d-flex justify-content-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i
                        class="bi bi-plus-circle"></i> Tambah Paket</button>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Paket</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Slug</th>
                                    <th>Harga</th>
                                    <th>Period</th>
                                    <th>Urutan</th>
                                    <th>Fitur</th>
                                    <th width="160">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($rows): foreach ($rows as $p): ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><?php echo htmlspecialchars($p['nama']); ?><br><small
                                            class="text-muted"><?php echo htmlspecialchars($p['subtitle']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($p['slug']); ?></td>
                                    <td>Rp <?php echo number_format($p['price'],0,',','.'); ?></td>
                                    <td><?php echo htmlspecialchars($p['period']); ?></td>
                                    <td><?php echo $p['urutan']; ?></td>
                                    <td style="white-space:pre-line; font-size:.8rem;">
                                        <?php 
                    $fts = preg_split('/\n/', $p['features']);
                    foreach ($fts as $f) {
                      $f = trim($f);
                      if ($f === '') continue;
                      $disabled = str_starts_with($f,'!');
                      $label = $disabled ? substr($f,1) : $f;
                      echo $disabled ? '<span class="text-muted"><i class="bi bi-x-circle"></i> '.htmlspecialchars($label)."</span>\n" : '<span class="text-success"><i class="bi bi-check-circle-fill"></i> '.htmlspecialchars($label)."</span>\n";
                    }
                  ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1 action-btn"
                                            onclick='editPaket(<?php echo json_encode($p, JSON_HEX_APOS|JSON_HEX_TAG|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE); ?>)'><i
                                                class="bi bi-pencil"></i></button>
                                        <form action="admin-paket-actions.php" method="POST" class="d-inline"
                                            onsubmit="return confirm('Hapus paket ini?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                            <button class="btn btn-sm btn-danger action-btn"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada paket</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Paket</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-paket-actions.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug (unik, misal: jam, bulanan)</label>
                                <input type="text" class="form-control" name="slug" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" name="subtitle" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Harga (angka)</label>
                                <input type="number" class="form-control" name="price" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Period (contoh: Per Jam)</label>
                                <input type="text" class="form-control" name="period" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Urutan</label>
                                <input type="number" class="form-control" name="urutan" min="0" value="0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Fitur (satu per baris, awali ! untuk fitur tidak
                                    termasuk)</label>
                                <textarea class="form-control" name="features" rows="6"
                                    placeholder="Akses Lapangan\nMinimal Sewa 3 Jam\n!Diskon Member"
                                    required></textarea>
                                <small class="text-muted">Baris dengan awalan ! akan muncul sebagai fitur tidak
                                    termasuk.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Paket</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-paket-actions.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" name="nama" id="edit_nama" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control" name="slug" id="edit_slug" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" name="subtitle" id="edit_subtitle" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Harga</label>
                                <input type="number" class="form-control" name="price" id="edit_price" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Period</label>
                                <input type="text" class="form-control" name="period" id="edit_period" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Urutan</label>
                                <input type="number" class="form-control" name="urutan" id="edit_urutan" min="0"
                                    required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Fitur</label>
                                <textarea class="form-control" name="features" id="edit_features" rows="6"
                                    required></textarea>
                                <small class="text-muted">Gunakan awalan ! untuk fitur tidak termasuk.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
<?php include 'footer.php'; ?>

