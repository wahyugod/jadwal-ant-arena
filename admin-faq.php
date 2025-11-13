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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola FAQ - Ant Arena</title>
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
            <a class="nav-link" href="admin-dashboard.php"><i class="bi bi-house-door"></i><span>Beranda</span></a>
            <a class="nav-link" href="admin-dashboard.php#edit-jadwal"><i
                    class="bi bi-calendar-week"></i><span>Penjadwalan</span></a>
            <a class="nav-link" href="admin-reservasi.php"><i
                    class="bi bi-calendar-check"></i><span>Reservasi</span></a>
            <a class="nav-link" href="admin-galeri.php"><i class="bi bi-images"></i><span>Galeri</span></a>
            <a class="nav-link" href="admin-testimoni.php"><i class="bi bi-chat-quote"></i><span>Testimoni</span></a>
            <a class="nav-link active" href="admin-faq.php"><i class="bi bi-question-circle"></i><span>FAQ</span></a>
            <a class="nav-link" href="admin-fasilitas.php"><i class="bi bi-list-check"></i><span>Fasilitas</span></a>

            <a class="nav-link" href="admin-transaksi.php"><i class="bi bi-cash-coin"></i><span>Transaksi</span></a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 30px;">
            <a class="nav-link" href="index.php" target="_blank"><i class="bi bi-eye"></i><span>Halaman
                    Publik</span></a>
            <a class="nav-link" href="admin-logout.php"><i class="bi bi-box-arrow-right"></i><span>Keluar</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn mobile-toggle me-3" id="sidebarToggle"><i class="bi bi-list"
                        style="font-size: 1.5rem;"></i></button>
                <div>
                    <div style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Halaman / FAQ
                    </div>
                    <h1 class="m-0" style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">Kelola FAQ
                    </h1>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2"
                    style="background: white; padding: 10px 16px; border-radius: 12px; box-shadow: 0 4px 12px rgba(112, 144, 176, 0.08);">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--primary-gradient-start);"></i>
                    <span
                        style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="padding: 30px;">
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); endif; ?>

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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });

    function editFAQ(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_pertanyaan').value = data.pertanyaan;
        document.getElementById('edit_jawaban').value = data.jawaban;
        document.getElementById('edit_urutan').value = data.urutan;
        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    function hapusFAQ(id) {
        document.getElementById('delete_id').value = id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }
    </script>
</body>

</html>