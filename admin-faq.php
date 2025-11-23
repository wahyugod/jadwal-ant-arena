<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertanyaan` varchar(255) NOT NULL,
  `jawaban` text NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Fetch data
$res = $conn->query("SELECT * FROM faqs ORDER BY urutan ASC, id DESC");
$rows = [];
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } }

$pageTitle = 'Kelola FAQ';
$pageBreadcrumb = 'FAQ';
include 'header.php';
?>
<div class="row mb-4">
                <div class="col-12">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-circle"></i> Tambah FAQ
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar FAQ</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pertanyaan</th>
                                    <th>Jawaban</th>
                                    <th>Urutan</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($rows)): foreach ($rows as $r): ?>
                                <tr>
                                    <td><?php echo $r['id']; ?></td>
                                    <td><?php echo htmlspecialchars($r['pertanyaan']); ?></td>
                                    <td
                                        style="max-width:400px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?php echo htmlspecialchars($r['jawaban']); ?></td>
                                    <td><?php echo (int)$r['urutan']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1 action-btn"
                                            onclick='editFAQ(<?php echo json_encode($r); ?>)'><i
                                                class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-danger action-btn"
                                            onclick='hapusFAQ(<?php echo (int)$r['id']; ?>)'><i
                                                class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data FAQ</td>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-faq-actions.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pertanyaan</label>
                            <input type="text" class="form-control" name="pertanyaan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jawaban</label>
                            <textarea class="form-control" name="jawaban" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" class="form-control" name="urutan" value="0" required>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-faq-actions.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pertanyaan</label>
                            <input type="text" class="form-control" name="pertanyaan" id="edit_pertanyaan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jawaban</label>
                            <textarea class="form-control" name="jawaban" id="edit_jawaban" rows="4"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" class="form-control" name="urutan" id="edit_urutan" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-faq-actions.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus item FAQ ini?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
<?php include 'footer.php'; ?>

