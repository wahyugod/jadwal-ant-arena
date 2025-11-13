<?php
require_once __DIR__ . '/config.php';

// Koneksi DB
$conn = getConnection();

// Ambil data jadwal
$jadwalRows = [];
$res = $conn->query("SELECT * FROM jadwal ORDER BY id ASC");
if ($res) {
  while ($row = $res->fetch_assoc()) { $jadwalRows[] = $row; }
  $res->free_result();
}

// Ambil fasilitas
$fasilitasRows = [];
$res = $conn->query("SELECT * FROM fasilitas ORDER BY id ASC");
if ($res) {
  while ($row = $res->fetch_assoc()) { $fasilitasRows[] = $row; }
  $res->free_result();
}

// Ambil testimoni
$testimoniRows = [];
$res = $conn->query("SELECT * FROM testimoni ORDER BY id DESC");
if ($res) {
  while ($row = $res->fetch_assoc()) { $testimoniRows[] = $row; }
  $res->free_result();
}

// Ambil FAQ
$faqRows = [];
$res = $conn->query("SELECT * FROM faqs ORDER BY urutan ASC, id DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) { $faqRows[] = $row; }
    $res->free_result();
}

// Ambil gambar galeri dari folder yang dikelola admin
$galeriImages = glob(__DIR__ . '/assets/galeri/*.{jpg,jpeg,png,webp,gif,JPG,JPEG,PNG,WEBP,GIF}', GLOB_BRACE) ?: [];
// Urutkan terbaru di atas
usort($galeriImages, function($a, $b){ return filemtime($b) <=> filemtime($a); });

// Helper aman HTML
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@nt's Arena - Lapangan Bulutangkis</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="assets/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container position-relative d-flex align-items-center justify-content-between">

            <a href="index.html" class="logo d-flex align-items-center me-auto me-xl-0">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">@nt's Arena</h1>
                <span>.</span>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#beranda" class="active">Beranda<br></a></li>
                    <li><a href="#tentang">Tentang</a></li>
                    <li><a href="#fasilitas">Fasilitas</a></li>
                    <li><a href="#galeri">Galeri</a></li>
                    <li><a href="#harga">Harga</a></li>
                    <li><a href="#jadwal">Jadwal</a></li>
                    <li><a href="#testimoni">Testimoni</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="#reservasi">Reservasi</a>

        </div>
    </header>

    <main class="main">

        <!-- Beranda Section -->
        <section id="beranda" class="hero section light-background"
            style="background-image: url('assets/img/home.jpeg'); min-height:100vh; background-size:cover; background-position:center;">
            <div class="container">
                <div class="row gy-4 justify-content-center align-items-center" style="min-height: calc(100vh - 80px);">
                    <div class="col-lg-8 d-flex flex-column justify-content-center text-center">
                        <h1 data-aos="fade-up">Raih Kemenangan di Setiap Pukulan!</h1>
                        <div class="d-flex justify-content-center" data-aos="fade-up" data-aos-delay="200">
                            <a href="#reservasi" class="btn-get-started">Reservasi Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- /Beranda Section -->

        <!-- Tentang Kami Section -->
        <section id="tentang" class="about section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Tentang<br></h2>
                <p><span>Pelajari Lebih</span> <span class="description-title">Tentang Kami</span></p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row gy-4">
                    <div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
                        <img src="assets/img/about.jpeg" class="img-fluid mb-4" alt="">
                    </div>
                    <div class="col-lg-5 d-flex align-items-center" data-aos="fade-up" data-aos-delay="250">
                        <div class="content ps-0 ps-lg-5" style="width:100%; text-align:left; line-height:1.8;">
                            <div style="margin:auto 0;">
                                <p class="fst-italic">
                                    <strong>@nt's Arena</strong> berdiri pada Juli 2025 dan dikelola
                                    oleh <strong>Ariyanto</strong>. Kami memiliki tujuan untuk mendorong
                                    semangat olahraga dan membangun komunitas bulutangkis yang aktif.
                                    Kebersihan terjamin dan fasilitas kami lengkap untuk kenyamanan
                                    bermain Anda.
                                </p>
                                <p class="fst-italic">
                                    Dengan dukungan fasilitas lengkap
                                    seperti lapangan berkualitas, area istirahat yang nyaman, serta layanan
                                    pemesanan jadwal yang mudah, kami siap menjadi tempat terbaik bagi Anda
                                    untuk berlatih, bersaing, dan menikmati permainan bulutangkis setiap hari.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

        </section><!-- /About Section -->

        <!-- Why Us Section -->
        <section id="why-us" class="why-us section light-background">

            <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="why-box">
                            <h3>Kenapa Pilih @nt's Arena</h3>
                            <p>
                                @nt's Arena adalah pilihan terbaik untuk Anda yang ingin menyewa lapangan badminton di
                                Samarinda.
                                Kami menyediakan lapangan berkualitas tinggi dengan lantai berstandar turnamen,
                                pencahayaan optimal,
                                serta suasana nyaman untuk latihan maupun pertandingan.
                            </p>
                            <p>
                                Dengan sistem pemesanan yang mudah dan harga sewa yang terjangkau,
                                Anda dapat bermain kapan saja tanpa khawatir kehabisan jadwal.
                                Kami juga menyediakan area istirahat dan fasilitas lengkap untuk mendukung pengalaman
                                bermain Anda.
                            </p>
                        </div>
                    </div><!-- End Why Box -->

                    <div class="col-lg-8 d-flex align-items-stretch">
                        <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">

                            <div class="col-xl-4">
                                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-clipboard-data"></i>
                                    <h4 class="fw-bold">Lapangan Standar Turnamen</h4>
                                    <p>Permukaan lapangan berkualitas tinggi dengan pencahayaan yang dirancang agar
                                        nyaman di mata dan cocok untuk pertandingan profesional.</p>
                                </div>
                            </div><!-- End Icon Box -->

                            <div class="col-xl-4" data-aos="fade-up" data-aos-delay="300">
                                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-gem"></i>
                                    <h4 class="fw-bold">Pemesanan Online Mudah</h4>
                                    <p>Booking jadwal bermain Anda secara online kapan pun dan di mana pun tanpa perlu
                                        antre.
                                        Jadwalkan permainan dengan cepat dan praktis.</p>
                                </div>
                            </div><!-- End Icon Box -->

                            <div class="col-xl-4" data-aos="fade-up" data-aos-delay="400">
                                <div class="icon-box d-flex flex-column justify-content-center align-items-center">
                                    <i class="bi bi-inboxes"></i>
                                    <h4 class="fw-bold">Harga & Fasilitas</h4>
                                    <p>Nikmati tarif sewa bersahabat dengan fasilitas memadai seperti kantin, mushola,
                                        dan pencahayaan LED untuk mendukung kenyamanan Anda bermain.</p>
                                </div>
                            </div><!-- End Icon Box -->

                        </div>
                    </div>

                </div>

            </div>

        </section><!-- /Why Us Section -->

        <!-- Stats Section -->
        <section id="stats" class="stats section dark-background">

            <img src="assets/img/stats.jpg" alt="" data-aos="fade-in">

            <div class="container position-relative" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4 justify-content-center text-center">

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Klien</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Total Jam Reservasi</p>
                        </div>
                    </div><!-- End Stats Item -->

                    <div class="col-lg-3 col-md-6">
                        <div class="stats-item text-center w-100 h-100">
                            <span data-purecounter-start="0" data-purecounter-end="32" data-purecounter-duration="1"
                                class="purecounter"></span>
                            <p>Pegawai</p>
                        </div>
                    </div><!-- End Stats Item -->

                </div>

            </div>

        </section><!-- /Stats Section -->

        <!-- Fasilitas Section -->
        <section id="fasilitas" class="events section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Fasilitas</h2>
                <p><span>Cek</span> <span class="description-title">Fasilitas</span> <span>Kami</span></p>
            </div><!-- End Section Title -->

            <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": "auto",
                        "pagination": {
                            "el": ".swiper-pagination",
                            "type": "bullets",
                            "clickable": true
                        },
                        "breakpoints": {
                            "320": {
                                "slidesPerView": 1,
                                "spaceBetween": 40
                            },
                            "1200": {
                                "slidesPerView": 3,
                                "spaceBetween": 1
                            }
                        }
                    }
                    </script>
                    <div class="swiper-wrapper">
                        <?php if (!empty($fasilitasRows)): ?>
                        <?php foreach ($fasilitasRows as $f): 
                    $foto = 'assets/fasilitas/' . ($f['foto'] ?? '');
                    // Jika file tidak ada, gunakan gambar default dari template
                    $bg = (is_file(__DIR__ . '/' . $foto)) ? $foto : 'assets/img/events-1.jpg';
              ?>
                        <div class="swiper-slide event-item d-flex flex-column justify-content-end"
                            style="background-image: url(<?= e($bg) ?>)">
                            <h3><?= e($f['nama'] ?? 'Fasilitas') ?></h3>
                            <p class="description"><?= e($f['deskripsi'] ?? '') ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <!-- Fallback jika belum ada data fasilitas -->
                        <div class="swiper-slide event-item d-flex flex-column justify-content-end"
                            style="background-image: url(assets/img/events-1.jpg)">
                            <h3>Pencahayaan LED</h3>
                            <p class="description">Pencahayaan terang dan merata untuk permainan maksimal.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>

        </section><!-- /Events Section -->

        <!-- Galeri Section -->
        <section id="galeri" class="gallery section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Galeri</h2>
                <p><span>Cek</span> <span class="description-title">Galeri</span> <span>Kami</span></p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": "auto",
                        "centeredSlides": true,
                        "pagination": {
                            "el": ".swiper-pagination",
                            "type": "bullets",
                            "clickable": true
                        },
                        "breakpoints": {
                            "320": {
                                "slidesPerView": 1,
                                "spaceBetween": 20
                            },
                            "768": {
                                "slidesPerView": 2,
                                "spaceBetween": 30
                            },
                            "1200": {
                                "slidesPerView": 3,
                                "spaceBetween": 30
                            }
                        }
                    }
                    </script>
                    <div class="swiper-wrapper align-items-center">
                        <?php if (!empty($galeriImages)): ?>
                        <?php foreach ($galeriImages as $imgPath): 
                    $rel = 'assets/galeri/' . basename($imgPath);
              ?>
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery" href="<?= e($rel) ?>">
                                <img src="<?= e($rel) ?>" class="img-fluid" alt="Galeri">
                            </a>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <!-- Fallback gambar default template -->
                        <div class="swiper-slide"><a class="glightbox" data-gallery="images-gallery"
                                href="assets/img/gallery/gallery-1.jpg"><img src="assets/img/gallery/gallery-1.jpg"
                                    class="img-fluid" alt=""></a></div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>

        </section><!-- /Gallery Section -->

        <!-- Harga Section -->
        <section id="harga" class="pricing section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Harga</h2>
                <p><span>Cek</span> <span class="description-title">Paket Harga</span> <span>Kami</span></p>
            </div><!-- End Section Title -->

            <div class="container">
                <div class="row gy-4 justify-content-center">

                    <!-- FREE/BASIC PLAN -->
                    <div class="col-lg-5 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="pricing-card pricing-free">
                            <div class="pricing-header">
                                <h3 class="pricing-title">SEWA PER JAM</h3>
                                <p class="pricing-subtitle">Cocok untuk main santai atau trial</p>
                            </div>
                            <div class="pricing-price">
                                <h2>Rp 50.000</h2>
                                <span class="pricing-period">/Per Jam</span>
                            </div>
                            <div class="pricing-features">
                                <h4>Yang Termasuk:</h4>
                                <ul>
                                    <li><i class="bi bi-check-circle-fill"></i> Akses Lapangan</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Pencahayaan LED</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Minimal Sewa 3 Jam</li>
                                    <li class="disabled"><i class="bi bi-x-circle"></i> Diskon Member</li>
                                    <li class="disabled"><i class="bi bi-x-circle"></i> Prioritas Booking</li>
                                </ul>
                            </div>
                            <div class="pricing-action">
                                <a href="#reservasi" class="btn-pricing btn-pricing-free">Pilih Paket</a>
                            </div>
                        </div>
                    </div>

                    <!-- ENTERPRISE/PREMIUM PLAN -->
                    <div class="col-lg-5 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="pricing-card pricing-enterprise">
                            <div class="pricing-header">
                                <h3 class="pricing-title">SEWA BULANAN</h3>
                                <p class="pricing-subtitle">Hemat untuk pemain rutin</p>
                            </div>
                            <div class="pricing-price">
                                <h2>Rp 350.000</h2>
                                <span class="pricing-period">/Per Bulan</span>
                            </div>
                            <div class="pricing-features">
                                <h4>Yang Termasuk:</h4>
                                <ul>
                                    <li><i class="bi bi-check-circle-fill"></i> Akses Lapangan</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Pencahayaan LED</li>
                                    <li><i class="bi bi-check-circle-fill"></i> 4x Main (3 jam/sesi)</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Diskon Member 15%</li>
                                    <li><i class="bi bi-check-circle-fill"></i> Prioritas Booking</li>
                                </ul>
                            </div>
                            <div class="pricing-action">
                                <a href="#reservasi" class="btn-pricing btn-pricing-enterprise">Pilih Paket</a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- ===== JADWAL SECTION ===== -->
        <section id="jadwal" class="jadwal-section section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Jadwal</h2>
                <p><span>Cek</span> <span class="description-title">Jadwal Operasional</span></p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">
                <div class="table-responsive">
                    <table class="jadwal-table">
                        <thead>
                            <tr>
                                <th>JAM</th>
                                <th>SENIN</th>
                                <th>SELASA</th>
                                <th>RABU</th>
                                <th>KAMIS</th>
                                <th>JUMAT</th>
                                <th>SABTU</th>
                                <th>MINGGU</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($jadwalRows)): ?>
                            <?php foreach ($jadwalRows as $row): ?>
                            <tr>
                                <td class="time-slot"><?= e($row['jam']) ?></td>
                                <?php foreach (['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $d): 
                          $val = trim((string)($row[$d] ?? ''));
                          $isAvail = ($val === '' || $val === '-' || strcasecmp($val, 'Tersedia') === 0);
                    ?>
                                <td><?= $isAvail ? '<span class="text-success">Tersedia</span>' : e($val) ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Jadwal belum tersedia</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="jadwal-info" data-aos="fade-up" data-aos-delay="200">
                    <p><i class="bi bi-info-circle"></i> Data diperbarui secara real-time oleh admin</p>
                </div>
            </div>

        </section>

        <!-- Testimonial Section -->
        <section id="testimoni" class="testimonials section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>TESTIMONI</h2>
                <p>Yang mereka <span class="description-title">Katakan Tentang Kami</span></p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": "auto",
                        "pagination": {
                            "el": ".swiper-pagination",
                            "type": "bullets",
                            "clickable": true
                        }
                    }
                    </script>
                    <div class="swiper-wrapper">
                        <?php if (!empty($testimoniRows)): ?>
                        <?php foreach ($testimoniRows as $t): 
                    $foto = 'assets/testimoni/' . ($t['foto'] ?? '');
                    $img = (is_file(__DIR__ . '/' . $foto)) ? $foto : 'assets/img/testimonials/testimonials-1.jpg';
              ?>
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="row gy-4 justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="testimonial-content">
                                            <p>
                                                <i class="bi bi-quote quote-icon-left"></i>
                                                <span><?= e($t['testimoni'] ?? '') ?></span>
                                                <i class="bi bi-quote quote-icon-right"></i>
                                            </p>
                                            <h3><?= e($t['nama'] ?? 'Pelanggan') ?></h3>
                                            <div class="stars">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                                    class="bi bi-star-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-center">
                                        <img src="<?= e($img) ?>" class="img-fluid testimonial-img" alt="Testimoni">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <!-- Fallback jika belum ada testimoni -->
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="row gy-4 justify-content-center">
                                    <div class="col-lg-6">
                                        <div class="testimonial-content">
                                            <p>
                                                <i class="bi bi-quote quote-icon-left"></i>
                                                <span>Belum ada testimoni.</span>
                                                <i class="bi bi-quote quote-icon-right"></i>
                                            </p>
                                            <h3>Pelanggan</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>

        </section><!-- /Testimonials Section -->

        <!-- FAQ Section -->
        <section id="faq" class="faq-accordion section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>FAQ</h2>
                <p><span>Yang</span> <span class="description-title">Sering Ditanyakan<br></span></p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="faq-container">
                    <?php if (!empty($faqRows)): ?>
                        <?php foreach ($faqRows as $f): ?>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <h3><?= e($f['pertanyaan']) ?></h3>
                                    <button class="faq-toggle" type="button">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                                <div class="faq-answer">
                                    <p><?= nl2br(e($f['jawaban'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback FAQ jika belum ada data -->
                        <div class="faq-item">
                            <div class="faq-question">
                                <h3>Bagaimana cara reservasi?</h3>
                                <button class="faq-toggle" type="button"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <div class="faq-answer"><p>Isi form reservasi atau hubungi WA kami. Konfirmasi 1x24 jam.</p></div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

        </section><!-- /FAQ Section -->

        <!-- Reservasi Section -->
        <section id="reservasi" class="book-a-table section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Reservasi</h2>
                <p><span class="description-title">Booking cepat,</span> <span>main lebih seru!<br></span></p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row g-0" data-aos="fade-up" data-aos-delay="100">

                    <div class="col-lg-4 reservation-img" style="background-image: url(assets/img/reservation.jpg);">
                    </div>

                    <div class="col-lg-8 d-flex align-items-stretch reservation-form-bg" data-aos="fade-up"
                        data-aos-delay="200">
                        <div class="row w-100 g-0">
                            <!-- Form Reservasi -->
                            <div class="col-xl-7 p-4 d-flex flex-column">
                                <form action="forms/reservasi.php" method="post" role="form"
                                    class="php-email-form flex-grow-1 d-flex flex-column">
                                    <div class="row gy-4">
                                        <div class="col-lg-6 col-md-6">
                                            <input type="text" name="nama" class="form-control" id="nama"
                                                placeholder="Nama Tim" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="Email Anda" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <input type="text" class="form-control" name="nohp" id="nohp"
                                                placeholder="Nomor WA" required>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <input type="date" name="tanggal" class="form-control" id="tanggal"
                                                required>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <select class="form-select" name="waktu" id="waktu" required>
                                                <option value="" disabled selected>Pilih Jam</option>
                                                <option value="08:00-11:00">08:00-11:00</option>
                                                <option value="11:00-14:00">11:00-14:00</option>
                                                <option value="14:00-17:00">14:00-17:00</option>
                                                <option value="17:00-20:00">17:00-20:00</option>
                                                <option value="20:00-23:00">20:00-23:00</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3 flex-grow-1">
                                        <textarea class="form-control h-100" name="pesan" rows="4"
                                            placeholder="Pesan (opsional)"></textarea>
                                    </div>
                                    <div class="text-center mt-3">
                                        <div class="loading">Loading</div>
                                        <div class="error-message"></div>
                                        <div class="sent-message">Permintaan booking anda sudah terkirim. Kami akan
                                            hubungi anda melalui WA dan Email untuk mengonfirmasi reservasi. Terima
                                            Kasih!</div>
                                        <button type="submit">Reservasi</button>
                                    </div>
                                </form>
                            </div>
                            <!-- Tracking Reservasi -->
                            <div class="col-xl-5 p-4" style="background:#f8f9fa; border-left:1px solid #eee;">
                                <div class="h-100 d-flex flex-column">
                                    <h3 class="mb-3" style="font-weight:600;">Lacak Reservasi</h3>
                                    <p class="small text-muted mb-3">Masukkan email atau nomor WA yang digunakan saat
                                        reservasi untuk melihat status.</p>
                                    <form id="trackForm" class="mb-3">
                                        <div class="input-group">
                                            <input type="text" id="trackQuery" class="form-control"
                                                placeholder="Email atau Nomor WA" required>
                                            <button class="btn btn-outline-primary" type="submit">Cari</button>
                                        </div>
                                    </form>
                                    <div id="trackResult" class="flex-grow-1" style="overflow:auto; max-height:270px;">
                                        <div class="text-muted small">Belum ada pencarian.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Reservation / Tracking -->

                </div>

            </div>

        </section><!-- /Book A Table Section -->
        <script>
        // Tracking Reservasi (public)
        document.addEventListener('DOMContentLoaded', function() {
            const trackForm = document.getElementById('trackForm');
            const trackQuery = document.getElementById('trackQuery');
            const trackResult = document.getElementById('trackResult');
            if (!trackForm) return;
            trackForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const q = trackQuery.value.trim();
                if (q === '') return;
                trackResult.innerHTML = '<div class="text-muted small">Memuat...</div>';
                fetch('track-reservasi.php?q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) {
                            trackResult.innerHTML = '<div class="text-danger small">' + (data
                                .message || 'Terjadi kesalahan') + '</div>';
                            return;
                        }
                        const rows = data.data || [];
                        if (rows.length === 0) {
                            trackResult.innerHTML =
                                '<div class="text-muted small">Tidak ditemukan reservasi untuk data tersebut.</div>';
                            return;
                        }
                        const badge = (st) => {
                            switch (st) {
                                case 'pending':
                                    return '<span class="badge bg-warning text-dark">Pending</span>';
                                case 'approved':
                                    return '<span class="badge bg-success">Disetujui</span>';
                                case 'rejected':
                                    return '<span class="badge bg-danger">Ditolak</span>';
                                default:
                                    return '<span class="badge bg-secondary">' + st + '</span>';
                            }
                        };
                        let html = '<div class="list-group list-group-flush">';
                        rows.forEach(r => {
                            const tanggal = r.tanggal_mulai ? new Date(r.tanggal_mulai)
                                .toLocaleDateString('id-ID') : '-';
                            html +=
                                '<div class="list-group-item py-3" style="background:#fff; border:1px solid #eee; margin-bottom:6px; border-radius:8px;">' +
                                '<div style="font-weight:600; font-size:0.95rem;">' + (r
                                    .nama_tim || 'Tim') + '</div>' +
                                '<div class="small text-muted">' + tanggal + ' • ' + (r
                                    .jam || '-') + ' • ' + (r.hari || '-') + '</div>' +
                                '<div class="mt-2">' + badge(r.status) + '</div>' +
                                (r.pesan ?
                                    '<div class="small mt-2" style="font-style:italic; color:#555;">"' +
                                    r.pesan.replace(/</g, '&lt;') + '"</div>' : '') +
                                '</div>';
                        });
                        html += '</div>';
                        trackResult.innerHTML = html;
                    })
                    .catch(err => {
                        trackResult.innerHTML = '<div class="text-danger small">Error: ' + err
                            .message + '</div>';
                    });
            });
        });
        </script>

        <!-- Kontak Section -->
        <section id="kontak" class="contact section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Kontak</h2>
                <p><span>Butuh Bantuan?</span> <span class="description-title">Hubungi Kami</span></p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="mb-5">
                    <iframe style="width: 100%; height: 290px;"
                        src="https://www.google.com/maps/embed/v1/place?q=%40nt's%20Arena&key=AIzaSyBFw0Qbyq9zTFTd-tUY6dZWTgaQzuU17R8"
                        frameborder="0" allowfullscreen=""></iframe>
                </div><!-- End Google Maps -->

                <div class="row gy-4">

                    <div class="col-md-6">
                        <div class="info-item d-flex align-items-center">
                            <i class="icon bi bi-geo-alt flex-shrink-0"></i>
                            <div>
                                <h3>Alamat</h3>
                                <p>
                                    Jl. Rejang Raya Gg Barokah, Bukit Pinang,
                                    Kec. Samarinda Ulu
                                </p>
                            </div>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="col-md-6">
                        <div class="info-item d-flex align-items-center">
                            <i class="icon bi bi-telephone flex-shrink-0"></i>
                            <div>
                                <h3>WhatsApp</h3>
                                <p>+62 812-3456-7890</p>
                            </div>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="col-md-6">
                        <div class="info-item d-flex align-items-center">
                            <i class="icon bi bi-envelope flex-shrink-0"></i>
                            <div>
                                <h3>Email</h3>
                                <p>info@nts-arena.com</p>
                            </div>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="col-md-6">
                        <div class="info-item d-flex align-items-center">
                            <i class="icon bi bi-clock flex-shrink-0"></i>
                            <div>
                                <h3>Instagram</h3>
                                <p>@nts_arena</p>
                            </div>
                        </div>
                    </div><!-- End Info Item -->

        </section><!-- /Contact Section -->

    </main>

    <!-- footer -->
    <footer id="footer" class="footer dark-background">

        <div class="container">
            <div class="row gy-3">
                <div class="col-lg-3 col-md-6 d-flex">
                    <i class="bi bi-geo-alt icon"></i>
                    <div class="address">
                        <h4>Alamat</h4>
                        <p>Jl. Rejang Raya Gg Barokah, Bukit Pinang, Kec. Samarinda Ulu</p>
                        <p>Kota Samarinda, Kalimantan Timur 75131</p>
                        <p></p>
                    </div>

                </div>

                <div class="col-lg-3 col-md-6 d-flex">
                    <i class="bi bi-telephone icon"></i>
                    <div>
                        <h4>Kontak</h4>
                        <p>
                            <strong>WhatsApp:</strong> <span>+62 812-3456-7890</span><br>
                            <strong>Email:</strong> <span>info@nts-arena.com</span><br>
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 d-flex">
                    <i class="bi bi-clock icon"></i>
                    <div>
                        <h4>Jam Operasional</h4>
                        <p>
                            <strong>Senin-Minggu:</strong> <span>8 Pagi - 11 Malam</span><br>
                        </p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h4>Follow Kami</h4>
                    <div class="social-links d-flex">
                        <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© <span>2025</span> <strong class="px-1 sitename">@nt's Arena</strong> <span>All Rights Reserved</span>
            </p>
            <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you've purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
                Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
            </div>
        </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>