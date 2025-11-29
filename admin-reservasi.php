<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Filter status jika ada
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'created_at_DESC';

$where_clauses = [];

// Status filter
if ($status_filter != 'all') {
    $where_clauses[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
}

// Search filter (cari di nama_tim, email, no_telepon)
if (!empty($search)) {
    $search_escaped = $conn->real_escape_string($search);
    $where_clauses[] = "(nama_tim LIKE '%{$search_escaped}%' OR email LIKE '%{$search_escaped}%' OR no_telepon LIKE '%{$search_escaped}%')";
}

$where_clause = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Sorting
$sort_options = [
    'id|DESC' => 'Terbaru',
    'id|ASC' => 'Terlama',
    'nama_tim|ASC' => 'Nama Tim (A-Z)',
    'nama_tim|DESC' => 'Nama Tim (Z-A)',
    'tanggal_mulai|ASC' => 'Tanggal Mulai (Awal)',
    'tanggal_mulai|DESC' => 'Tanggal Mulai (Akhir)'
];

// Parse sort parameter dengan delimiter pipe (|) untuk menghindari konflik dengan underscore di nama kolom
$sort_parts = explode('|', $sort);
$sort_column = $sort_parts[0] ?? 'id';
$sort_direction = $sort_parts[1] ?? 'DESC';

// Whitelist untuk kolom yang diizinkan
$allowed_columns = ['id', 'nama_tim', 'tanggal_mulai', 'status'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

// Validasi sort direction
if (!in_array($sort_direction, ['ASC', 'DESC'])) {
    $sort_direction = 'DESC';
}

$sql = "SELECT * FROM reservasi {$where_clause} ORDER BY {$sort_column} {$sort_direction}";
$result = $conn->query($sql);

$pageTitle = 'Kelola Reservasi';
$pageBreadcrumb = 'Reservasi';
include 'header.php';
?>
<!-- Filter Status -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="get" class="row g-3 align-items-end">
                                <!-- Search Input -->
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Cari:</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                        placeholder="Cari nama tim, email, telepon..." 
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <!-- Status Filter -->
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Filter Status:</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Semua Status</option>
                                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Disetujui</option>
                                        <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
                                    </select>
                                </div>

                                <!-- Sorting -->
                                <div class="col-md-3">
                                    <label for="sort" class="form-label">Sortir:</label>
                                    <select name="sort" id="sort" class="form-select">
                                        <?php foreach ($sort_options as $key => $label): ?>
                                            <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $sort == $key ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="bi bi-search"></i> Cari
                                    </button>
                                    <a href="admin-reservasi.php" class="btn btn-danger flex-fill">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </a>
                                </div>
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
                                    <th>Paket</th>
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
                                    <td><?php echo htmlspecialchars($row['paket'] ?? ''); ?></td>
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
<?php include 'footer.php'; ?>

