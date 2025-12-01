<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Filter status jika ada
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'id|DESC';

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

// Count total records
$count_sql = "SELECT COUNT(*) as total FROM reservasi {$where_clause}";
$count_result = $conn->query($count_sql);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $perPage);

$sql = "SELECT * FROM reservasi {$where_clause} ORDER BY {$sort_column} {$sort_direction} LIMIT {$perPage} OFFSET {$offset}";
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
                            <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>Semua Status
                            </option>
                            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending
                            </option>
                            <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>
                                Disetujui</option>
                            <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>
                                Ditolak</option>
                        </select>
                    </div>

                    <!-- Sorting -->
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sortir:</label>
                        <select name="sort" id="sort" class="form-select">
                            <?php foreach ($sort_options as $key => $label): ?>
                            <option value="<?php echo htmlspecialchars($key); ?>"
                                <?php echo $sort == $key ? 'selected' : ''; ?>>
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
                        <th width="80">ID</th>
                        <th>Nama Tim</th>
                        <th>Jadwal</th>
                        <th>Tanggal Mulai</th>
                        <th>Status</th>
                        <th width="360">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['nama_tim']); ?></td>
                        <td>
                            <div><?php echo ucfirst(htmlspecialchars($row['hari'])); ?>,
                                <?php echo htmlspecialchars($row['jam']); ?></div>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal_mulai'])); ?></td>
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
                        <td>
                            <button class="btn btn-sm btn-info mb-1"
                                onclick="showDetail(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                            <button
                                class="btn btn-sm btn-success mb-1 <?php echo $row['status'] != 'pending' ? 'disabled' : ''; ?>"
                                onclick="<?php echo $row['status'] == 'pending' ? 'approveReservation(' . $row['id'] . ')' : 'return false;'; ?>"
                                <?php echo $row['status'] != 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-check-circle"></i> Setujui
                            </button>
                            <button
                                class="btn btn-sm btn-danger mb-1 <?php echo $row['status'] != 'pending' ? 'disabled' : ''; ?>"
                                onclick="<?php echo $row['status'] == 'pending' ? 'rejectReservation(' . $row['id'] . ')' : 'return false;'; ?>"
                                <?php echo $row['status'] != 'pending' ? 'disabled' : ''; ?>>
                                <i class="bi bi-x-circle"></i> Tolak
                            </button>
                        </td>
                        </td>
                    </tr>
                    <?php endwhile;
                                else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data reservasi</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- Previous Button -->
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="?page=<?= $page - 1 ?>&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"
                        aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php
                            // Calculate page range
                            $range = 2; // Show 2 pages before and after current page
                            $start = max(1, $page - $range);
                            $end = min($total_pages, $page + $range);
                            
                            // First page
                            if ($start > 1): ?>
                <li class="page-item">
                    <a class="page-link"
                        href="?page=1&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>">1</a>
                </li>
                <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link"
                        href="?page=<?= $i ?>&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>

                <!-- Last page -->
                <?php if ($end < $total_pages): ?>
                <?php if ($end < $total_pages - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link"
                        href="?page=<?= $total_pages ?>&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"><?= $total_pages ?></a>
                </li>
                <?php endif; ?>

                <!-- Next Button -->
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="?page=<?= $page + 1 ?>&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>"
                        aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted small">
                Halaman <?= $page ?> dari <?= $total_pages ?> (Total <?= $total_records ?> data)
            </div>
        </nav>
        <?php endif; ?>
    </div>
</div>
</div>
</div>

<!-- Modal Detail Reservasi -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 0.5rem; overflow: hidden;">
            <div class="modal-header bg-white border-bottom" style="border-radius: 0;">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detail Reservasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-hash me-1"></i>ID
                                Reservasi</label>
                            <p class="mb-0" id="detail-id"></p>
                        </div>
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-people me-1"></i>Nama
                                Tim</label>
                            <p class="mb-0" id="detail-nama"></p>
                        </div>
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i
                                    class="bi bi-envelope me-1"></i>Email</label>
                            <p class="mb-0" id="detail-email"></p>
                        </div>
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-telephone me-1"></i>No.
                                Telepon</label>
                            <p class="mb-0" id="detail-telepon"></p>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i
                                    class="bi bi-calendar-event me-1"></i>Jadwal</label>
                            <p class="mb-0" id="detail-jadwal"></p>
                        </div>
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i
                                    class="bi bi-box-seam me-1"></i>Paket</label>
                            <p class="mb-0" id="detail-paket"></p>
                        </div>
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i
                                    class="bi bi-calendar-date me-1"></i>Tanggal Mulai</label>
                            <p class="mb-0" id="detail-tanggal"></p>
                        </div>
                        <div class="mb-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i
                                    class="bi bi-check-circle me-1"></i>Status</label>
                            <p class="mb-0" id="detail-status"></p>
                        </div>
                    </div>

                    <!-- Pesan Full Width -->
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded">
                            <label class="form-label fw-bold text-dark mb-2"><i
                                    class="bi bi-chat-left-text me-1"></i>Pesan</label>
                            <p class="mb-0" id="detail-pesan" style="white-space: pre-wrap;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                        class="bi bi-x-circle me-1"></i>Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="modalConfirm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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

<script>
function showDetail(data) {
    // Populate modal dengan data reservasi
    document.getElementById('detail-id').textContent = data.id;
    document.getElementById('detail-nama').textContent = data.nama_tim;
    document.getElementById('detail-email').textContent = data.email;
    document.getElementById('detail-telepon').textContent = data.no_telepon;
    document.getElementById('detail-jadwal').textContent = data.hari.charAt(0).toUpperCase() + data.hari.slice(1) +
        ', ' + data.jam;
    document.getElementById('detail-paket').textContent = data.paket || '-';

    // Format tanggal
    const date = new Date(data.tanggal_mulai);
    const formattedDate = date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    document.getElementById('detail-tanggal').textContent = formattedDate;

    // Status dengan badge
    const statusText = {
        'pending': 'Menunggu',
        'approved': 'Disetujui',
        'rejected': 'Ditolak'
    };
    const statusClass = {
        'pending': 'bg-warning',
        'approved': 'bg-success',
        'rejected': 'bg-danger'
    };
    document.getElementById('detail-status').innerHTML =
        '<span class="badge ' + statusClass[data.status] + '">' + statusText[data.status] + '</span>';

    document.getElementById('detail-pesan').textContent = data.pesan || '-';

    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    modal.show();
}
</script>

<?php include 'footer.php'; ?>