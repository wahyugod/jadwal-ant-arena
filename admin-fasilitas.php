<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Ambil semua fasilitas
$sql = "SELECT * FROM fasilitas ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Fasilitas - Ant Arena</title>
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
            <a class="nav-link" href="admin-reservasi.php">
                <i class="bi bi-calendar-check"></i>
                <span>Reservasi</span>
            </a>
            <a class="nav-link" href="admin-galeri.php">
                <i class="bi bi-images"></i>
                <span>Galeri</span>
            </a>
            <a class="nav-link" href="admin-testimoni.php">
                <i class="bi bi-chat-quote"></i>
                <span>Testimoni</span>
            </a>
            <a class="nav-link" href="admin-faq.php">
                <i class="bi bi-question-circle"></i>
                <span>FAQ</span>
            </a>
            <a class="nav-link active" href="admin-fasilitas.php">
                <i class="bi bi-list-check"></i>
                <span>Fasilitas</span>
            </a>
            <a class="nav-link" href="admin-transaksi.php">
                <i class="bi bi-cash-coin"></i>
                <span>Transaksi</span>
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 30px;">
            <a class="nav-link" href="index.php" target="_blank">
                <i class="bi bi-eye"></i>
                <span>Halaman Publik</span>
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
                    <div style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Halaman /
                        Fasilitas</div>
                    <h1 class="m-0" style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">Kelola
                        Fasilitas</h1>
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
        <!-- Content Area -->
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
                        <i class="bi bi-plus-circle"></i> Tambah Fasilitas
                    </button>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Fasilitas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Foto</th>
                                    <th>Nama Fasilitas</th>
                                    <th>Deskripsi</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td><img src='assets/fasilitas/" . htmlspecialchars($row['foto']) . "' alt='Foto Fasilitas' style='width: 100px; height: 100px; object-fit: cover;'></td>";
                                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-sm btn-warning me-1 action-btn' onclick='editFasilitas(" . json_encode($row) . ")'><i class='bi bi-pencil'></i></button>";
                                        echo "<button class='btn btn-sm btn-danger action-btn' onclick='hapusFasilitas(" . $row['id'] . ")'><i class='bi bi-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>Belum ada data fasilitas</td></tr>";
                                }
                                $conn->close();
                                ?>
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
                    <h5 class="modal-title">Tambah Fasilitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-fasilitas-actions.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Fasilitas</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto Fasilitas</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                            <small class="form-text text-muted">Format yang diperbolehkan: JPG, JPEG, PNG, GIF. Maks:
                                5MB</small>
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
                    <h5 class="modal-title">Edit Fasilitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-fasilitas-actions.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama Fasilitas</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="2"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_foto" class="form-label">Foto Fasilitas</label>
                            <input type="file" class="form-control" id="edit_foto" name="foto" accept="image/*">
                            <small class="form-text text-muted">Format yang diperbolehkan: JPG, JPEG, PNG, GIF. Maks:
                                5MB. Kosongkan jika tidak ingin mengubah foto.</small>
                            <div id="current_photo" class="mt-2"></div>
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
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: none;">
                    <h5 class="modal-title" style="font-weight: 700; color: var(--text-primary);">
                        <i class="bi bi-trash3 text-danger me-2"></i>Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="color: var(--text-secondary);">
                    Apakah Anda yakin ingin menghapus fasilitas ini? Tindakan ini tidak dapat dibatalkan.
                </div>
                <div class="modal-footer" style="border-top: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <form id="formDeleteFasilitas" action="admin-fasilitas-actions.php" method="POST" class="d-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_id" value="">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });

    function editFasilitas(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nama').value = data.nama;
        document.getElementById('edit_deskripsi').value = data.deskripsi;
        document.getElementById('current_photo').innerHTML = data.foto ?
            `<img src="assets/fasilitas/${data.foto}" alt="Foto Saat Ini" style="max-width: 200px; max-height: 200px; object-fit: contain;">` :
            'Tidak ada foto';
        const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
        modal.show();
    }

    function hapusFasilitas(id) {
        document.getElementById('delete_id').value = id;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmDelete'));
        modal.show();
    }
    </script>
</body>

</html>