<?php
require_once 'config.php';
requireLogin();

// Ambil parameter filter
$filterTanggalDari = $_GET['tanggal_dari'] ?? '';
$filterTanggalSampai = $_GET['tanggal_sampai'] ?? '';
$filterKategori = $_GET['kategori'] ?? '';

// Sorting parameters
$sortBy = $_GET['sort_by'] ?? 'tanggal';
$sortOrder = $_GET['sort_order'] ?? 'DESC';

// Validate sort parameters
$allowedSortColumns = ['tanggal', 'deskripsi', 'kategori', 'nominal', 'id'];
if (!in_array($sortBy, $allowedSortColumns)) {
    $sortBy = 'tanggal';
}
if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) {
    $sortOrder = 'DESC';
}

// Pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build query dengan filter
$conn = getConnection();
$sqlWhere = "WHERE 1=1";
$conditions = [];
$params = [];
$types = '';

if (!empty($filterTanggalDari)) {
    $conditions[] = "tanggal >= ?";
    $params[] = $filterTanggalDari;
    $types .= 's';
}

if (!empty($filterTanggalSampai)) {
    $conditions[] = "tanggal <= ?";
    $params[] = $filterTanggalSampai;
    $types .= 's';
}

if (!empty($filterKategori)) {
    $conditions[] = "kategori = ?";
    $params[] = $filterKategori;
    $types .= 's';
}

if (!empty($conditions)) {
    $sqlWhere .= " AND " . implode(" AND ", $conditions);
}

// Count total records
$sqlCount = "SELECT COUNT(*) as total FROM transaksi " . $sqlWhere;
if (!empty($params)) {
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param($types, ...$params);
    $stmtCount->execute();
    $totalRecords = $stmtCount->get_result()->fetch_assoc()['total'];
    $stmtCount->close();
} else {
    $totalRecords = $conn->query($sqlCount)->fetch_assoc()['total'];
}

$totalPages = ceil($totalRecords / $perPage);

// Build ORDER BY clause with secondary sort
$orderBy = "$sortBy $sortOrder";
if ($sortBy !== 'id') {
    $orderBy .= ", id DESC"; // Secondary sort by ID for consistency
}

// Get paginated data
$sql = "SELECT * FROM transaksi " . $sqlWhere . " ORDER BY " . $orderBy . " LIMIT ? OFFSET ?";
$paramsWithLimit = $params;
$paramsWithLimit[] = $perPage;
$paramsWithLimit[] = $offset;
$typesWithLimit = $types . 'ii';

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($typesWithLimit, ...$paramsWithLimit);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $perPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Helper function to build URL with all parameters
function buildUrl($newParams = []) {
    global $filterTanggalDari, $filterTanggalSampai, $filterKategori, $sortBy, $sortOrder, $page;
    
    $params = [
        'tanggal_dari' => $filterTanggalDari,
        'tanggal_sampai' => $filterTanggalSampai,
        'kategori' => $filterKategori,
        'sort_by' => $sortBy,
        'sort_order' => $sortOrder,
        'page' => $page
    ];
    
    // Override with new params
    $params = array_merge($params, $newParams);
    
    // Remove empty values
    $params = array_filter($params, function($value) {
        return $value !== '' && $value !== null;
    });
    
    return '?' . http_build_query($params);
}

// Helper function to get sort icon
function getSortIcon($column) {
    global $sortBy, $sortOrder;
    if ($sortBy === $column) {
        return $sortOrder === 'ASC' ? 'bi-sort-up' : 'bi-sort-down';
    }
    return 'bi-arrow-down-up';
}

// Helper function to get next sort order
function getNextSortOrder($column) {
    global $sortBy, $sortOrder;
    if ($sortBy === $column) {
        return $sortOrder === 'ASC' ? 'DESC' : 'ASC';
    }
    return 'DESC';
}

$pageTitle = 'Kelola Transaksi';
$pageBreadcrumb = 'Transaksi';
include 'header.php';
?>
<div class="row mb-4">
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-circle"></i> Tambah Transaksi
                    </button>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Transaksi</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control"
                                value="<?php echo htmlspecialchars($filterTanggalDari); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control"
                                value="<?php echo htmlspecialchars($filterTanggalSampai); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="">Semua Kategori</option>
                                <option value="pemasukan"
                                    <?php echo $filterKategori === 'pemasukan' ? 'selected' : ''; ?>>Pemasukan</option>
                                <option value="pengeluaran"
                                    <?php echo $filterKategori === 'pengeluaran' ? 'selected' : ''; ?>>Pengeluaran
                                </option>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Terapkan Filter
                            </button>
                            <a href="admin-transaksi.php" class="btn btn-danger">
                                <i class="bi bi-x-circle"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Transaksi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="<?php echo buildUrl(['sort_by' => 'id', 'sort_order' => getNextSortOrder('id'), 'page' => 1]); ?>"
                                            class="text-decoration-none text-dark d-flex align-items-center gap-1"
                                            style="<?php echo $sortBy === 'id' ? 'color: var(--primary-gradient-start) !important; font-weight: 600;' : ''; ?>">
                                            ID
                                            <i class="bi <?php echo getSortIcon('id'); ?>"
                                                style="font-size: 0.875rem;"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?php echo buildUrl(['sort_by' => 'tanggal', 'sort_order' => getNextSortOrder('tanggal'), 'page' => 1]); ?>"
                                            class="text-decoration-none text-dark d-flex align-items-center gap-1"
                                            style="<?php echo $sortBy === 'tanggal' ? 'color: var(--primary-gradient-start) !important; font-weight: 600;' : ''; ?>">
                                            Tanggal
                                            <i class="bi <?php echo getSortIcon('tanggal'); ?>"
                                                style="font-size: 0.875rem;"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?php echo buildUrl(['sort_by' => 'deskripsi', 'sort_order' => getNextSortOrder('deskripsi'), 'page' => 1]); ?>"
                                            class="text-decoration-none text-dark d-flex align-items-center gap-1"
                                            style="<?php echo $sortBy === 'deskripsi' ? 'color: var(--primary-gradient-start) !important; font-weight: 600;' : ''; ?>">
                                            Deskripsi
                                            <i class="bi <?php echo getSortIcon('deskripsi'); ?>"
                                                style="font-size: 0.875rem;"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?php echo buildUrl(['sort_by' => 'kategori', 'sort_order' => getNextSortOrder('kategori'), 'page' => 1]); ?>"
                                            class="text-decoration-none text-dark d-flex align-items-center gap-1"
                                            style="<?php echo $sortBy === 'kategori' ? 'color: var(--primary-gradient-start) !important; font-weight: 600;' : ''; ?>">
                                            Kategori
                                            <i class="bi <?php echo getSortIcon('kategori'); ?>"
                                                style="font-size: 0.875rem;"></i>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="<?php echo buildUrl(['sort_by' => 'nominal', 'sort_order' => getNextSortOrder('nominal'), 'page' => 1]); ?>"
                                            class="text-decoration-none text-dark d-flex align-items-center gap-1"
                                            style="<?php echo $sortBy === 'nominal' ? 'color: var(--primary-gradient-start) !important; font-weight: 600;' : ''; ?>">
                                            Nominal
                                            <i class="bi <?php echo getSortIcon('nominal'); ?>"
                                                style="font-size: 0.875rem;"></i>
                                        </a>
                                    </th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $badgeClass = $row['kategori'] == 'pemasukan' ? 'bg-success' : 'bg-danger';
                                        $nominalFormatted = 'Rp ' . number_format($row['nominal'], 0, ',', '.');
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                        echo "<td><span class='badge {$badgeClass}'>" . ucfirst($row['kategori']) . "</span></td>";
                                        echo "<td><strong>" . $nominalFormatted . "</strong></td>";
                                        echo "<td>";
                                        echo "<button class='btn btn-sm btn-warning me-1 action-btn' onclick='editTransaksi(" . json_encode($row) . ")'><i class='bi bi-pencil'></i></button>";
                                        echo "<button class='btn btn-sm btn-danger action-btn' onclick='hapusTransaksi(" . $row['id'] . ")'><i class='bi bi-trash'></i></button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Belum ada data transaksi</td></tr>";
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Info & Controls -->
                    <?php if ($totalRecords > 0): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
                        <div style="color: var(--text-secondary); font-size: 0.875rem;">
                            Menampilkan <?php echo min($offset + 1, $totalRecords); ?> -
                            <?php echo min($offset + $perPage, $totalRecords); ?> dari <?php echo $totalRecords; ?>
                            transaksi
                        </div>

                        <?php if ($totalPages > 1): ?>
                        <nav>
                            <ul class="pagination mb-0">
                                <!-- Previous Button -->
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo buildUrl(['page' => $page - 1]); ?>">Sebelumnya</a>
                                </li>

                                <?php
                                // Show page numbers
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . buildUrl(['page' => 1]) . '">1</a></li>';
                                    if ($startPage > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    $active = $i == $page ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '">';
                                    echo '<a class="page-link" href="' . buildUrl(['page' => $i]) . '">' . $i . '</a>';
                                    echo '</li>';
                                }
                                
                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . buildUrl(['page' => $totalPages]) . '">' . $totalPages . '</a></li>';
                                }
                                ?>

                                <!-- Next Button -->
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                        href="<?php echo buildUrl(['page' => $page + 1]); ?>">Selanjutnya</a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-transaksi-actions.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="deskripsi" name="deskripsi"
                                placeholder="Contoh: Pembayaran sewa lapangan" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal (Rp)</label>
                            <input type="number" class="form-control" id="nominal" name="nominal"
                                placeholder="Contoh: 500000" required>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-transaksi-actions.php" method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="edit_deskripsi" name="deskripsi" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="edit_kategori" name="kategori" required>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nominal" class="form-label">Nominal (Rp)</label>
                            <input type="number" class="form-control" id="edit_nominal" name="nominal" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Ubah</button>
                    </div>
                </form>
            </div>
<?php include 'footer.php'; ?>

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
                            Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.
                        </div>
                        <div class="modal-footer" style="border-top: none;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </button>
                            <form id="formDeleteTransaksi" action="admin-transaksi-actions.php" method="POST" class="d-inline">
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

