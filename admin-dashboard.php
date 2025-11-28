<?php
require_once 'config.php';
requireLogin();

// Ambil data jadwal
$conn = getConnection();
$sql = "SELECT * FROM jadwal ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ant Arena</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
    .nav-dropdown {
        position: relative;
    }

    .nav-dropdown-toggle {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .nav-dropdown-toggle .bi-chevron-down {
        transition: transform 0.3s ease;
        font-size: 0.75rem;
    }

    .nav-dropdown.show .bi-chevron-down {
        transform: rotate(180deg);
    }

    .nav-dropdown-menu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        padding-left: 20px;
    }

    .nav-dropdown.show .nav-dropdown-menu {
        max-height: 500px;
    }

    .nav-dropdown-menu .nav-link {
        padding: 8px 20px;
        font-size: 0.9rem;
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
            <a class="nav-link active" href="#" data-section="statistik">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
            <a class="nav-link" href="#" data-section="edit-jadwal">
                <i class="bi bi-calendar-week"></i>
                <span>Penjadwalan</span>
            </a>
            <a class="nav-link" href="admin-reservasi.php">
                <i class="bi bi-calendar-check"></i>
                <span>Reservasi</span>
            </a>
            <a class="nav-link" href="admin-transaksi.php">
                <i class="bi bi-cash-coin"></i>
                <span>Transaksi</span>
            </a>

            <div class="nav-dropdown">
                <a class="nav-link nav-dropdown-toggle">
                    <div style="display: flex; align-items: center; flex: 1;">
                        <i class="bi bi-gear"></i>
                        <span>Pengaturan Web</span>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <div class="nav-dropdown-menu">
                    <a class="nav-link" href="admin-hero.php">
                        <i class="bi bi-lightning-charge"></i>
                        <span>Hero</span>
                    </a>
                    <a class="nav-link" href="admin-tentang.php">
                        <i class="bi bi-info-circle"></i>
                        <span>Tentang</span>
                    </a>
                    <a class="nav-link" href="admin-fasilitas.php">
                        <i class="bi bi-list-check"></i>
                        <span>Fasilitas</span>
                    </a>
                    <a class="nav-link" href="admin-galeri.php">
                        <i class="bi bi-images"></i>
                        <span>Galeri</span>
                    </a>
                    <a class="nav-link" href="admin-testimoni.php">
                        <i class="bi bi-chat-quote"></i>
                        <span>Testimoni</span>
                    </a>
                    <a class="nav-link" href="admin-paket.php">
                        <i class="bi bi-tags"></i>
                        <span>Paket</span>
                    </a>
                    <a class="nav-link" href="admin-faq.php">
                        <i class="bi bi-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                    <a class="nav-link" href="admin-kontak.php">
                        <i class="bi bi-envelope"></i>
                        <span>Kontak</span>
                    </a>
                    <a class="nav-link" href="admin-footer.php">
                        <i class="bi bi-layout-text-window-reverse"></i>
                        <span>Footer</span>
                    </a>
                </div>
            </div>

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
                    <div id="pageBreadcrumb"
                        style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Halaman / <span
                            id="pageSectionName">Beranda</span></div>
                    <h1 id="pageTitle" class="m-0"
                        style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">Beranda</h1>
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

            <!-- Section: Statistik -->
            <div class="section-content active" id="section-statistik">
                <h4 class="mb-4" style="color: var(--text-primary); font-weight: 700;">📊 Statistik</h4>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card primary">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="stat-label">Total Jadwal</div>
                                <div class="stat-value" id="stat-total-jadwal">-</div>
                                <div class="stat-change">
                                    <i class="bi bi-arrow-up"></i> +2.45%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card success">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="stat-label">Slot Terisi</div>
                                <div class="stat-value" id="stat-slot-terisi">-</div>
                                <div class="stat-change">
                                    <i class="bi bi-arrow-up"></i> +12.5%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card warning">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <div class="stat-label">Slot Tersedia</div>
                                <div class="stat-value" id="stat-slot-tersedia">-</div>
                                <div class="stat-change negative">
                                    <i class="bi bi-arrow-down"></i> -5.2%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card stat-card info">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-pie-chart"></i>
                                </div>
                                <div class="stat-label">Persentase Terisi</div>
                                <div class="stat-value" id="stat-persentase">-</div>
                                <div class="stat-change">
                                    <i class="bi bi-arrow-up"></i> +8.3%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keuangan Stats Cards -->
                <h5 class="mt-5 mb-4" style="color: var(--text-primary); font-weight: 700;">💰 Keuangan</h5>
                <div class="row">
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card success">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </div>
                                <div class="stat-label">Total Pemasukan</div>
                                <div class="stat-value" id="stat-pemasukan">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card warning">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </div>
                                <div class="stat-label">Total Pengeluaran</div>
                                <div class="stat-value" id="stat-pengeluaran">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="card stat-card info">
                            <div class="card-body">
                                <div class="stat-icon">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <div class="stat-label">Saldo</div>
                                <div class="stat-value" id="stat-saldo">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Chart -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1" style="color: var(--text-primary);"><i
                                                class="bi bi-bar-chart"></i> Pemasukan vs Pengeluaran</h5>
                                        <p class="mb-0" style="color: var(--text-secondary); font-size: 0.875rem;">
                                            Grafik Keuangan 6 Bulan Terakhir</p>
                                    </div>
                                    <div>
                                        <i class="bi bi-cash-stack"
                                            style="font-size: 1.5rem; color: var(--primary-gradient-start);"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="chartTransaksi" style="max-height: 350px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1" style="color: var(--text-primary);">Slot Minggu Ini</h5>
                                        <p class="mb-0" style="color: var(--text-secondary); font-size: 0.875rem;">
                                            Jumlah Slot Terisi per Hari</p>
                                    </div>
                                    <div>
                                        <i class="bi bi-bar-chart-line"
                                            style="font-size: 1.5rem; color: var(--primary-gradient-start);"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="chartPerHari" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1" style="color: var(--text-primary);">Tim Minggu Ini</h5>
                                        <p class="mb-0" style="color: var(--text-secondary); font-size: 0.875rem;">Tim
                                            Terbanyak</p>
                                    </div>
                                    <div>
                                        <i class="bi bi-pie-chart"
                                            style="font-size: 1.5rem; color: var(--primary-gradient-start);"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="chartTopTeams"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Edit Jadwal -->
            <div class="section-content" id="section-edit-jadwal">

                <h4 class="mb-4" style="color: var(--text-primary); font-weight: 700;">📅 Manajemen Jadwal</h4>

                <div class="mb-3 d-flex gap-2 justify-content-end">
                    <button class="btn btn-success" id="setSlotsAvailable">
                        <i class="bi bi-check-circle"></i> Sediakan Semua Slot
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" id="setDayAvailableButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-check-circle"></i> Sediakan Per Hari
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="setDayAvailableButton">
                            <li><button class="dropdown-item set-day-available" data-day="senin">Senin</button></li>
                            <li><button class="dropdown-item set-day-available" data-day="selasa">Selasa</button></li>
                            <li><button class="dropdown-item set-day-available" data-day="rabu">Rabu</button></li>
                            <li><button class="dropdown-item set-day-available" data-day="kamis">Kamis</button></li>
                            <li><button class="dropdown-item set-day-available" data-day="jumat">Jumat</button></li>
                            <li><button class="dropdown-item set-day-available" data-day="sabtu">Sabtu</button></li>
                            <li><button class="dropdown-item set-day-available" data-day="minggu">Minggu</button></li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Jam</th>
                                        <th>Senin</th>
                                        <th>Selasa</th>
                                        <th>Rabu</th>
                                        <th>Kamis</th>
                                        <th>Jumat</th>
                                        <th>Sabtu</th>
                                        <th>Minggu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result && $result->num_rows > 0) {
                                        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            echo "<td><strong>" . htmlspecialchars($row['jam']) . "</strong></td>";

                                            foreach ($days as $day) {
                                                $raw = trim($row[$day]);
                                                // Treat empty, dash or 'Tersedia' as empty/default and display 'Tersedia'
                                                if ($raw === '' || $raw === '-' || strcasecmp($raw, 'Tersedia') === 0) {
                                                    $display = '<span class="text-success">Tersedia</span>';
                                                    $dataValue = '';
                                                } else {
                                                    $display = htmlspecialchars($raw);
                                                    $dataValue = $raw;
                                                }

                                                echo "<td class='team-cell' data-id='" . $row['id'] . "' data-day='" . $day . "' data-value='" . htmlspecialchars($dataValue) . "' data-jam='" . htmlspecialchars($row['jam']) . "'>";
                                                echo $display; // Don't use htmlspecialchars here since we're using HTML span tag
                                                echo "</td>";
                                            }

                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9' class='text-center'>Tidak ada data jadwal</td></tr>";
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
    </div>

    <!-- Modal Edit Tim -->
    <div class="modal fade" id="editTeamModal" tabindex="-1" aria-labelledby="editTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTeamModalLabel">
                        <i class="bi bi-pencil-square"></i> Edit Jadwal
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Jam:</strong></label>
                        <p id="modalJam" class="text-muted"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Hari:</strong></label>
                        <p id="modalHari" class="text-muted text-capitalize"></p>
                    </div>
                    <div class="mb-3">
                        <label for="teamNameInput" class="form-label">Nama Tim:</label>
                        <input type="text" class="form-control" id="teamNameInput" placeholder="Masukkan nama tim">
                    </div>
                    <div id="modalAlert" class="alert" role="alert" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="button" class="btn btn-success" id="setAvailableBtn">
                            <i class="bi bi-check-circle"></i> Sediakan
                        </button>
                        <button type="button" class="btn btn-primary" id="saveTeamBtn">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast container -->
    <div class="toast-container" id="toastContainer" style="position: fixed; top: 1rem; right: 1rem; z-index: 1100;">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar navigation
        const navLinks = document.querySelectorAll('.sidebar .nav-link[data-section]');
        const sections = document.querySelectorAll('.section-content');

        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const targetSection = this.dataset.section;

                // Update active link
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Show target section
                sections.forEach(s => s.classList.remove('active'));
                document.getElementById('section-' + targetSection).classList.add('active');

                // Update page title and breadcrumb section name
                const sectionName = this.querySelector('span').textContent;
                document.getElementById('pageTitle').textContent = sectionName;
                const sectionNameElem = document.getElementById('pageSectionName');
                if (sectionNameElem) sectionNameElem.textContent = sectionName;

                // Load stats if statistik section
                if (targetSection === 'statistik') {
                    loadStatistics();
                }
            });
        });

        // Mobile sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Dropdown toggle untuk Pengaturan Web
        document.querySelectorAll('.nav-dropdown-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.closest('.nav-dropdown');
                dropdown.classList.toggle('show');
            });
        });

        // Honor URL hash so we can preserve the active section after actions/reloads
        // If a hash exists (e.g. #edit-jadwal or #statistik) activate that section.
        const initialHash = window.location.hash.replace('#', '');
        if (initialHash) {
            const targetLink = document.querySelector(`.sidebar .nav-link[data-section="${initialHash}"]`);
            if (targetLink) {
                // trigger the same handler as a real click to set active classes and load stats if needed
                targetLink.click();
            } else {
                // fallback: load statistics
                loadStatistics();
            }
        } else {
            // Default behavior: load statistics (sidebar default is Statistik)
            loadStatistics();
        }

        // Ensure breadcrumb/page title reflects the active sidebar link on initial load
        const activeLinkOnLoad = document.querySelector('.sidebar .nav-link.active[data-section]');
        if (activeLinkOnLoad) {
            const name = activeLinkOnLoad.querySelector('span').textContent;
            document.getElementById('pageTitle').textContent = name;
            const sectionNameElem = document.getElementById('pageSectionName');
            if (sectionNameElem) sectionNameElem.textContent = name;
        }

        // Statistics & Charts
        let chartPerHari, chartTopTeams, chartTransaksi;

        function loadStatistics() {
            fetch('admin-stats.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.data;

                        // Update stat cards
                        document.getElementById('stat-total-jadwal').textContent = data.total_jadwal;
                        document.getElementById('stat-slot-terisi').textContent = data.slot_terisi;
                        document.getElementById('stat-slot-tersedia').textContent = data.slot_tersedia;
                        document.getElementById('stat-persentase').textContent = data.persentase_terisi +
                            '%';

                        // Render charts
                        renderChartPerHari(data.per_hari);
                        renderChartTopTeams(data.top_teams);
                    }
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                });

            // Load transaksi statistics
            loadTransaksiStats();
        }

        function loadTransaksiStats() {
            fetch('admin-transaksi-stats.php')
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.data;

                        // Format currency
                        const formatRupiah = (number) => {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
                        };

                        // Update financial stat cards
                        document.getElementById('stat-pemasukan').textContent = formatRupiah(data
                            .total_pemasukan);
                        document.getElementById('stat-pengeluaran').textContent = formatRupiah(data
                            .total_pengeluaran);
                        document.getElementById('stat-saldo').textContent = formatRupiah(data.saldo);

                        // Render transaction chart
                        renderChartTransaksi(data.per_bulan);
                    }
                })
                .catch(error => {
                    console.error('Error loading transaction statistics:', error);
                });
        }

        function renderChartPerHari(perHari) {
            const ctx = document.getElementById('chartPerHari').getContext('2d');

            if (chartPerHari) chartPerHari.destroy();

            const days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            const labels = days.map(d => d.charAt(0).toUpperCase() + d.slice(1));
            const values = days.map(d => perHari[d] || 0);

            // Create gradient for bars
            const gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
            gradient1.addColorStop(0, '#4318FF');
            gradient1.addColorStop(1, '#7551FF');

            const gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
            gradient2.addColorStop(0, '#6AD2FF');
            gradient2.addColorStop(1, '#2BB0ED');

            chartPerHari = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Slot Terisi',
                        data: values,
                        backgroundColor: [
                            gradient1, gradient2, gradient1, gradient2,
                            gradient1, gradient2, gradient1
                        ],
                        borderRadius: 10,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1a1f37',
                            padding: 12,
                            borderRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: '600'
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#A3AED0',
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#E9EDF7',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                color: '#A3AED0',
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderChartTopTeams(topTeams) {
            const ctx = document.getElementById('chartTopTeams').getContext('2d');

            if (chartTopTeams) chartTopTeams.destroy();

            const labels = Object.keys(topTeams);
            const values = Object.values(topTeams);

            chartTopTeams = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#4318FF',
                            '#6AD2FF',
                            '#05CD99',
                            '#FFB547',
                            '#7551FF'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600',
                                    family: 'DM Sans'
                                },
                                color: '#1B2559',
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1a1f37',
                            padding: 12,
                            borderRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: '600'
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    }
                }
            });
        }

        // Render Transaction Chart
        function renderChartTransaksi(perBulan) {
            const canvas = document.getElementById('chartTransaksi');
            const ctx = canvas.getContext('2d');

            if (chartTransaksi) chartTransaksi.destroy();

            // Support two possible API shapes:
            // 1) per_bulan = [{bulan: 'Sep 2025', pemasukan: 1000, pengeluaran: 500}, ...]
            // 2) per_bulan = { labels: [...], pemasukan: [...], pengeluaran: [...] }
            let labels = [];
            let pemasukan = [];
            let pengeluaran = [];

            if (!perBulan) {
                labels = [];
            } else if (Array.isArray(perBulan)) {
                labels = perBulan.map(item => item.bulan || '');
                pemasukan = perBulan.map(item => parseFloat(item.pemasukan || 0));
                pengeluaran = perBulan.map(item => parseFloat(item.pengeluaran || 0));
            } else if (perBulan.labels && Array.isArray(perBulan.labels)) {
                labels = perBulan.labels;
                pemasukan = (perBulan.pemasukan || []).map(v => parseFloat(v || 0));
                pengeluaran = (perBulan.pengeluaran || []).map(v => parseFloat(v || 0));
            } else {
                // Fallback: try to convert object keyed by month
                try {
                    const tempLabels = [];
                    const tempP = [];
                    const tempQ = [];
                    for (const k in perBulan) {
                        const v = perBulan[k];
                        if (v && typeof v === 'object' && ('pemasukan' in v || 'pengeluaran' in v)) {
                            tempLabels.push(k);
                            tempP.push(parseFloat(v.pemasukan || 0));
                            tempQ.push(parseFloat(v.pengeluaran || 0));
                        }
                    }
                    labels = tempLabels;
                    pemasukan = tempP;
                    pengeluaran = tempQ;
                } catch (e) {
                    labels = [];
                }
            }

            // Handle empty/no-data state
            const sumP = pemasukan.reduce((s, x) => s + (isNaN(x) ? 0 : x), 0);
            const sumQ = pengeluaran.reduce((s, x) => s + (isNaN(x) ? 0 : x), 0);
            const container = canvas.parentElement;

            // Remove existing no-data message if any
            const existingMsg = container.querySelector('.chart-no-data');
            if (existingMsg) existingMsg.remove();

            if (labels.length === 0 || (sumP === 0 && sumQ === 0)) {
                // Hide canvas and show friendly message
                canvas.style.display = 'none';
                const msg = document.createElement('div');
                msg.className = 'chart-no-data';
                msg.textContent = 'Tidak ada data transaksi untuk periode ini.';
                msg.style.padding = '28px';
                msg.style.textAlign = 'center';
                msg.style.color = 'var(--text-secondary)';
                msg.style.fontFamily = 'DM Sans, sans-serif';
                msg.style.fontSize = '0.95rem';
                container.appendChild(msg);
                return;
            }

            // Ensure canvas visible
            canvas.style.display = '';

            chartTransaksi = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Pemasukan',
                            data: pemasukan,
                            backgroundColor: 'rgba(67, 24, 255, 0.8)',
                            borderColor: 'rgb(67, 24, 255)',
                            borderWidth: 2,
                            borderRadius: 8
                        },
                        {
                            label: 'Pengeluaran',
                            data: pengeluaran,
                            backgroundColor: 'rgba(255, 99, 71, 0.8)',
                            borderColor: 'rgb(255, 99, 71)',
                            borderWidth: 2,
                            borderRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'DM Sans',
                                    size: 12,
                                    weight: 600
                                },
                                padding: 15,
                                usePointStyle: true,
                                color: '#1B2559'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(26, 31, 55, 0.95)',
                            titleFont: {
                                family: 'DM Sans',
                                size: 13,
                                weight: 600
                            },
                            bodyFont: {
                                family: 'DM Sans',
                                size: 12
                            },
                            padding: 12,
                            borderColor: 'rgba(67, 24, 255, 0.3)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y
                                        .toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'DM Sans',
                                    size: 11,
                                    weight: 500
                                },
                                color: '#A3AED0'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(163, 174, 208, 0.1)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    family: 'DM Sans',
                                    size: 11,
                                    weight: 500
                                },
                                color: '#A3AED0',
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000) + 'k';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Modal & Edit functionality (existing code)
        const modal = new bootstrap.Modal(document.getElementById('editTeamModal'));
        let currentCell = null;

        document.querySelectorAll('.team-cell').forEach(cell => {
            cell.addEventListener('click', function() {
                currentCell = this;
                const jadwalId = this.dataset.id;
                const day = this.dataset.day;
                const currentValue = this.dataset.value;
                const jam = this.dataset.jam;

                document.getElementById('modalJam').textContent = jam;
                document.getElementById('modalHari').textContent = day;
                document.getElementById('teamNameInput').value = currentValue;

                const saveBtn = document.getElementById('saveTeamBtn');
                saveBtn.dataset.id = jadwalId;
                saveBtn.dataset.day = day;

                document.getElementById('modalAlert').style.display = 'none';
                modal.show();

                setTimeout(() => {
                    document.getElementById('teamNameInput').focus();
                    document.getElementById('teamNameInput').select();
                }, 300);
            });
        });

        document.getElementById('saveTeamBtn').addEventListener('click', function() {
            const jadwalId = this.dataset.id;
            const day = this.dataset.day;
            const newValue = document.getElementById('teamNameInput').value.trim();

            if (!newValue) {
                showModalAlert('Nama tim tidak boleh kosong!', 'danger');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch('admin-update-cell.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${jadwalId}&day=${day}&value=${encodeURIComponent(newValue)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Escape HTML entities for displaying team name
                        currentCell.textContent =
                            newValue; // Use textContent for security when showing team name
                        currentCell.dataset.value = newValue;

                        showModalAlert('Berhasil diupdate!', 'success');
                        showToast('Berhasil disimpan', 'success');

                        setTimeout(() => {
                            modal.hide();
                            loadStatistics(); // Refresh stats
                        }, 700);
                    } else {
                        showModalAlert('Gagal update: ' + (data.message || 'Unknown error'),
                            'danger');
                        showToast('Gagal update: ' + (data.message || ''), 'danger');
                    }
                })
                .catch(error => {
                    showModalAlert('Error: ' + error.message, 'danger');
                    showToast('Error: ' + error.message, 'danger');
                })
                .finally(() => {
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-save"></i> Simpan';
                });
        });

        // Handler untuk tombol Sediakan di dalam modal
        document.getElementById('setAvailableBtn').addEventListener('click', function() {
            const jadwalId = document.getElementById('saveTeamBtn').dataset.id;
            const day = document.getElementById('saveTeamBtn').dataset.day;
            const dayName = day.charAt(0).toUpperCase() + day.slice(1);
            const btn = this;

            btn.disabled = true;
            document.getElementById('saveTeamBtn').disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyediakan...';

            fetch('admin-update-cell.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${jadwalId}&day=${day}&value=Tersedia`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentCell.innerHTML = '<span class="text-success">Tersedia</span>';
                        // keep dataset empty so modal will show empty input for adding a team
                        currentCell.dataset.value = '';
                        document.getElementById('teamNameInput').value = '';

                        showModalAlert('Slot berhasil disediakan!', 'success');
                        showToast('Slot berhasil disediakan', 'success');

                        setTimeout(() => {
                            modal.hide();
                            loadStatistics(); // Refresh stats
                        }, 700);
                    } else {
                        showModalAlert('Gagal menyediakan: ' + (data.message || 'Unknown error'),
                            'danger');
                        showToast('Gagal menyediakan: ' + (data.message || ''), 'danger');
                    }
                })
                .catch(error => {
                    showModalAlert('Error: ' + error.message, 'danger');
                    showToast('Error: ' + error.message, 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    document.getElementById('saveTeamBtn').disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Sediakan';
                });
        });

        function showModalAlert(message, type) {
            const alertDiv = document.getElementById('modalAlert');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            alertDiv.style.display = 'block';
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');

            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            container.appendChild(toastEl);
            const bsToast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            bsToast.show();

            toastEl.addEventListener('hidden.bs.toast', () => {
                toastEl.remove();
            });
        }

        document.getElementById('teamNameInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('saveTeamBtn').click();
            }
        });

        document.getElementById('setSlotsAvailable').addEventListener('click', function() {
            const btn = this;

            if (!confirm('Sediakan semua slot pada jadwal?')) {
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyediakan...';

            fetch('admin-bulk-action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=set_available'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Semua slot berhasil disediakan!', 'success');

                        // Update all cells in the table
                        document.querySelectorAll('.team-cell').forEach(cell => {
                            cell.innerHTML = '<span class="text-success">Tersedia</span>';
                            // keep dataset.value empty so modal shows empty input for adding a team
                            cell.dataset.value = '';
                        });

                        // Refresh statistics to reflect changes
                        loadStatistics();
                    } else {
                        showToast('Gagal: ' + (data.message || 'Unknown error'), 'danger');
                    }
                })
                .catch(error => {
                    showToast('Error: ' + error.message, 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Sediakan Semua Slot';
                });
        });

        // Handler untuk tombol sediakan per hari
        document.querySelectorAll('.set-day-available').forEach(button => {
            button.addEventListener('click', function() {
                const day = this.dataset.day;
                const dayName = day.charAt(0).toUpperCase() + day.slice(1);
                const dropdownButton = document.getElementById('setDayAvailableButton');

                if (!confirm(`Sediakan semua slot untuk hari ${dayName}?`)) {
                    return;
                }

                // Disable both dropdown toggle and the clicked item
                dropdownButton.disabled = true;
                const originalDropdownText = dropdownButton.innerHTML;
                dropdownButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Menyediakan...';

                fetch('admin-bulk-action.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=set_day_available&day=${day}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(
                                `Semua slot untuk hari ${dayName} berhasil disediakan`,
                                'success');

                            // Update semua cell untuk hari tersebut (display Tersedia, keep dataset empty)
                            document.querySelectorAll(`.team-cell[data-day="${day}"]`)
                                .forEach(cell => {
                                    cell.innerHTML =
                                        '<span class="text-success">Tersedia</span>';
                                    cell.dataset.value = '';
                                });

                            // Refresh statistics
                            loadStatistics();
                        } else {
                            showToast('Gagal: ' + (data.message || 'Unknown error'),
                                'danger');
                        }
                    })
                    .catch(error => {
                        showToast('Error: ' + error.message, 'danger');
                    })
                    .finally(() => {
                        dropdownButton.disabled = false;
                        dropdownButton.innerHTML = originalDropdownText;
                    });
            });
        });
    });
    </script>
</body>

</html>