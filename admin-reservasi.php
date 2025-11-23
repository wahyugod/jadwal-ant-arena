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

$pageTitle = 'Kelola Reservasi';
$pageBreadcrumb = 'Reservasi';
include 'header.php';
?>
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

