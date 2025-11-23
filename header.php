<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - Ant Arena</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php if (isset($extraStyles)) echo $extraStyles; ?>
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
            <a class="nav-link <?= ($currentPage === 'admin-dashboard.php') ? 'active' : '' ?>"
                href="admin-dashboard.php">
                <i class="bi bi-house-door"></i>
                <span>Beranda</span>
            </a>
            <a class="nav-link <?= ($currentPage === 'admin-dashboard.php') ? '' : '' ?>"
                href="admin-dashboard.php#edit-jadwal">
                <i class="bi bi-calendar-week"></i>
                <span>Penjadwalan</span>
            </a>
            <a class="nav-link <?= ($currentPage === 'admin-reservasi.php') ? 'active' : '' ?>"
                href="admin-reservasi.php">
                <i class="bi bi-calendar-check"></i>
                <span>Reservasi</span>
            </a>
            <a class="nav-link <?= ($currentPage === 'admin-transaksi.php') ? 'active' : '' ?>"
                href="admin-transaksi.php">
                <i class="bi bi-cash-coin"></i>
                <span>Transaksi</span>
            </a>

            <?php 
        $webPages = ['admin-hero.php', 'admin-tentang.php', 'admin-fasilitas.php', 'admin-galeri.php', 'admin-testimoni.php', 'admin-paket.php', 'admin-faq.php', 'admin-kontak.php', 'admin-footer.php'];
        $isWebPage = in_array($currentPage, $webPages);
        ?>
            <div class="nav-dropdown <?= $isWebPage ? 'show' : '' ?>">
                <a class="nav-link nav-dropdown-toggle <?= $isWebPage ? 'active' : '' ?>">
                    <div style="display: flex; align-items: center; flex: 1;">
                        <i class="bi bi-gear"></i>
                        <span>Pengaturan Web</span>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <div class="nav-dropdown-menu">
                    <a class="nav-link <?= ($currentPage === 'admin-hero.php') ? 'active' : '' ?>"
                        href="admin-hero.php">
                        <i class="bi bi-lightning-charge"></i>
                        <span>Hero</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-tentang.php') ? 'active' : '' ?>"
                        href="admin-tentang.php">
                        <i class="bi bi-info-circle"></i>
                        <span>Tentang</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-fasilitas.php') ? 'active' : '' ?>"
                        href="admin-fasilitas.php">
                        <i class="bi bi-list-check"></i>
                        <span>Fasilitas</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-galeri.php') ? 'active' : '' ?>"
                        href="admin-galeri.php">
                        <i class="bi bi-images"></i>
                        <span>Galeri</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-testimoni.php') ? 'active' : '' ?>"
                        href="admin-testimoni.php">
                        <i class="bi bi-chat-quote"></i>
                        <span>Testimoni</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-paket.php') ? 'active' : '' ?>"
                        href="admin-paket.php">
                        <i class="bi bi-tags"></i>
                        <span>Paket</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-faq.php') ? 'active' : '' ?>" href="admin-faq.php">
                        <i class="bi bi-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-kontak.php') ? 'active' : '' ?>"
                        href="admin-kontak.php">
                        <i class="bi bi-envelope"></i>
                        <span>Kontak</span>
                    </a>
                    <a class="nav-link <?= ($currentPage === 'admin-footer.php') ? 'active' : '' ?>"
                        href="admin-footer.php">
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
                    <div style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">
                        Halaman / <?= $pageBreadcrumb ?? 'Admin' ?>
                    </div>
                    <h1 class="m-0" style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">
                        <?= $pageTitle ?? 'Admin Panel' ?>
                    </h1>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2"
                    style="background: white; padding: 10px 16px; border-radius: 12px; box-shadow: 0 4px 12px rgba(112, 144, 176, 0.08);">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--primary-gradient-start);"></i>
                    <span
                        style="font-weight: 600; color: var(--text-primary);"><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="container-fluid" style="padding: 30px;">
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_SESSION['success_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); endif; ?>