<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Filter status jika ada
$status_filter = $_GET['status'] ?? 'all';
$where_clause = $status_filter != 'all' ? "WHERE status = '" . $conn->real_escape_string($status_filter) . "'" : "";

// Ambil semua reservasi
$sql = "SELECT * FROM reservasi {$where_clause} ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Reservasi - Ant Arena</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
    /* Extend status block to visually match two action buttons width */
    td.action-cell {
        width: 260px;
    }

    .status-wide {
        display: block;
        width: 100%;
        padding: 10px 16px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        text-align: center;
        letter-spacing: 0.25px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .status-wide.status-approved {
        background: linear-gradient(90deg, #05c985, #08e29a);
        color: #fff;
    }

    .status-wide.status-rejected {
        background: #dc3545;
        color: #fff;
    }

    .status-wide.status-pending {
        background: #ffc107;
        color: #212529;
    }

    /* Improve button alignment consistency */
    td.action-cell .btn {
        min-width: 108px;
    }

    @media (max-width: 992px) {
        td.action-cell {
            min-width: 180px;
        }
    }
    </style>
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
            <a class="nav-link active" href="admin-reservasi.php">
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
            <a class="nav-link" href="admin-fasilitas.php">
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
                        Reservasi</div>
                    <h1 class="m-0" style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">Kelola
                        Reservasi</h1>
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

            <!-- Filter Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" class="d-flex gap-3 align-items-center">
                                <label for="status" class="form-label mb-0">Filter Status:</label>
                                <select name="status" id="status" class="form-select" style="width: 200px;"
                                    onchange="this.form.submit()">
                                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Semua
                                        Status</option>
                                    <option value="pending"
                                        <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved"
                                        <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Disetujui</option>
                                    <option value="rejected"
                                        <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Reservasi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Tim</th>
                                    <th>Kontak</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Pesan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_tim']); ?></td>
                                    <td>
                                        <div>Email: <?php echo htmlspecialchars($row['email']); ?></div>
                                        <div>Telp: <?php echo htmlspecialchars($row['no_telepon']); ?></div>
                                    </td>
                                    <td>
                                        <div>Hari: <?php echo ucfirst(htmlspecialchars($row['hari'])); ?></div>
                                        <div>Jam: <?php echo htmlspecialchars($row['jam']); ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            $badge_class = [
                                                'pending' => 'bg-warning',
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger'
                                            ];
                                            $status_text = [
                                                'pending' => 'Menunggu',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak'
                                            ];
                                            ?>
                                        <span class="badge <?php echo $badge_class[$row['status']]; ?>">
                                            <?php echo $status_text[$row['status']]; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_mulai'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['pesan']); ?></td>
                                    <td class="action-cell">
                                        <?php if ($row['status'] == 'pending'): ?>
                                        <button class="btn btn-sm btn-success mb-1"
                                            onclick="approveReservation(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </button>
                                        <button class="btn btn-sm btn-danger mb-1"
                                            onclick="rejectReservation(<?php echo $row['id']; ?>)">
                                            <i class="bi bi-x-circle"></i> Tolak
                                        </button>
                                        <?php else: ?>
                                        <?php if ($row['status'] == 'approved'): ?>
                                        <div class="status-wide status-approved">Disetujui</div>
                                        <?php elseif ($row['status'] == 'rejected'): ?>
                                        <div class="status-wide status-rejected">Ditolak</div>
                                        <?php else: ?>
                                        <div class="status-wide status-pending">Menunggu</div>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile;
                                else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data reservasi</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="modalConfirm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage"></p>
                    <form id="reservationForm" action="admin-reservasi-actions.php" method="POST">
                        <input type="hidden" name="id" id="reservationId">
                        <input type="hidden" name="action" id="reservationAction">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn" id="confirmButton"
                        onclick="document.getElementById('reservationForm').submit();">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('show');
    });

    function showConfirmationModal(id, action, title, message, buttonClass) {
        const modal = new bootstrap.Modal(document.getElementById('modalConfirm'));
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        document.getElementById('reservationId').value = id;
        document.getElementById('reservationAction').value = action;
        document.getElementById('confirmButton').className = 'btn ' + buttonClass;
        modal.show();
    }

    function approveReservation(id) {
        showConfirmationModal(
            id,
            'approve',
            'Konfirmasi Persetujuan',
            'Apakah Anda yakin ingin menyetujui reservasi ini?',
            'btn-success'
        );
    }

    function rejectReservation(id) {
        showConfirmationModal(
            id,
            'reject',
            'Konfirmasi Penolakan',
            'Apakah Anda yakin ingin menolak reservasi ini?',
            'btn-danger'
        );
    }
    </script>
</body>

</html>