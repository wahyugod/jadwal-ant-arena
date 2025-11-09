<?php
require_once 'config.php';

// Ambil data jadwal dari database
$conn = getConnection();
$sql = "SELECT * FROM jadwal ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Jadwal - Ant Arena</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="favicon.ico" />
</head>

<body class="site-index">
    <div class="container site-container">
        <section id="jadwal" class="jadwal">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-calendar-week" style="color: var(--primary-gradient-start);"></i>
                        Jadwal Operasional
                    </h2>
                    <p class="mb-0" style="color: var(--text-secondary); font-size: 0.95rem;">Informasi Jadwal dan
                        Reservasi</p>
                </div>
                <a href="admin-login.php" class="btn btn-primary" style="text-decoration: none;">
                    <i class="bi bi-lock"></i> Admin
                </a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
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
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><strong>" . htmlspecialchars($row['jam']) . "</strong></td>";
                        echo "<td>" . htmlspecialchars($row['senin']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['selasa']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['rabu']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['kamis']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jumat']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sabtu']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['minggu']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data jadwal</td></tr>";
                }
                $conn->close();
                ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-center">
                <small style="color: var(--text-secondary);">
                    <i class="bi bi-info-circle"></i> Data diperbarui secara real-time oleh admin
                </small>
            </div>
        </section>

        <!-- gallery section -->
        <section id="gallery" class="gallery mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-images" style="color: var(--primary-gradient-start);"></i>
                        Galeri Ant Arena
                    </h2>
                    <p class="mb-0" style="color: var(--text-secondary); font-size: 0.95rem;">Lihat koleksi foto kami
                    </p>
                </div>
            </div>
            <?php
            // Kumpulkan daftar gambar dari folder assets (jpg, jpeg, png, webp, gif)
            $imageFiles = glob(__DIR__ . '/assets/galeri/*.{jpg,jpeg,png,webp,gif,JPG,JPEG,PNG,WEBP,GIF}', GLOB_BRACE);
            $imageUrls = [];
            foreach ($imageFiles as $filePath) {
                $imageUrls[] = 'assets/galeri/' . basename($filePath);
            }
            // Jika tidak ada gambar ditemukan, fallback ke galeri.jpg
            if (empty($imageUrls)) {
                $imageUrls = ['assets/galeri/galeri1.png'];
            }
            // Pastikan minimal 3 item agar selalu ada kiri, tengah, kanan
            while (count($imageUrls) < 3) {
                $imageUrls[] = $imageUrls[count($imageUrls) % max(1, count($imageUrls))];
            }
            ?>

            <div class="gallery-slider">
                <button class="slider-btn slider-prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <div class="gallery-viewport">
                    <!-- Items akan diinject via JS; berikut fallback tanpa JS -->
                    <noscript>
                        <div class="gallery-item is-prev"><img src="<?= htmlspecialchars($imageUrls[0]) ?>"
                                alt="Galeri 1"></div>
                        <div class="gallery-item is-current"><img src="<?= htmlspecialchars($imageUrls[1]) ?>"
                                alt="Galeri 2"></div>
                        <div class="gallery-item is-next"><img src="<?= htmlspecialchars($imageUrls[2]) ?>"
                                alt="Galeri 3"></div>
                    </noscript>
                </div>
                <button class="slider-btn slider-next" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>
        </section>
        <!-- end gallery section -->
        <!-- fasilitas section (mirip galeri) -->
        <section id="fasilitas" class="gallery mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-building" style="color: var(--primary-gradient-start);"></i>
                        Fasilitas Ant Arena
                    </h2>
                    <p class="mb-0" style="color: var(--text-secondary); font-size: 0.95rem;">Lihat fasilitas
                    </p>
                </div>
            </div>
            <?php
            // Kumpulkan daftar gambar dari folder assets/fasilitas
            $fasilitasFiles = glob(__DIR__ . '/assets/fasilitas/*.{jpg,jpeg,png,webp,gif,JPG,JPEG,PNG,WEBP,GIF}', GLOB_BRACE);
            $fasilitasUrls = [];
            foreach ($fasilitasFiles as $filePath) {
                $fasilitasUrls[] = 'assets/fasilitas/' . basename($filePath);
            }
            // Jika tidak ada gambar ditemukan, fallback ke galeri image
            if (empty($fasilitasUrls)) {
                $fasilitasUrls = ['assets/galeri/galeri1.png'];
            }
            // Pastikan minimal 3 item agar selalu ada kiri, tengah, kanan
            while (count($fasilitasUrls) < 3) {
                $fasilitasUrls[] = $fasilitasUrls[count($fasilitasUrls) % max(1, count($fasilitasUrls))];
            }
            ?>

            <div class="gallery-slider">
                <button class="slider-btn slider-prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
                <div class="gallery-viewport fasilitas-viewport">
                    <!-- Items akan diinject via JS; berikut fallback tanpa JS -->
                    <noscript>
                        <div class="gallery-item is-prev"><img src="<?= htmlspecialchars($fasilitasUrls[0]) ?>" alt="Fasilitas 1"></div>
                        <div class="gallery-item is-current"><img src="<?= htmlspecialchars($fasilitasUrls[1]) ?>" alt="Fasilitas 2"></div>
                        <div class="gallery-item is-next"><img src="<?= htmlspecialchars($fasilitasUrls[2]) ?>" alt="Fasilitas 3"></div>
                    </noscript>
                </div>
                <button class="slider-btn slider-next" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
            </div>
        </section>
        <!-- end fasilitas section -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Galeri slider: tampilkan 1 foto tengah besar + 2 foto samping kecil, geser otomatis looping
    (function() {
        const images = <?php echo json_encode(array_values($imageUrls), JSON_UNESCAPED_SLASHES); ?>;
        const viewport = document.querySelector('.gallery-viewport');
        if (!viewport || !images || !images.length) return;

        // Buat elemen item untuk setiap gambar
        images.forEach((src, idx) => {
            const item = document.createElement('div');
            item.className = 'gallery-item';
            item.dataset.index = String(idx);
            const img = document.createElement('img');
            img.src = src;
            img.alt = 'Galeri ' + (idx + 1);
            item.appendChild(img);
            viewport.appendChild(item);
        });

        const items = Array.from(viewport.querySelectorAll('.gallery-item'));
        let current = 0;

        function applyClasses() {
            const n = items.length;
            const prev = (current - 1 + n) % n;
            const next = (current + 1) % n;
            items.forEach((el, i) => {
                el.classList.remove('is-prev', 'is-current', 'is-next', 'is-hidden');
                if (i === current) el.classList.add('is-current');
                else if (i === prev) el.classList.add('is-prev');
                else if (i === next) el.classList.add('is-next');
                else el.classList.add('is-hidden');
            });
        }

        function nextSlide() {
            current = (current + 1) % items.length;
            applyClasses();
        }

        function prevSlide() {
            current = (current - 1 + items.length) % items.length;
            applyClasses();
        }

        applyClasses();

        // Auto-play (5s) and controls
        let delay = 5000;
        let timer = setInterval(nextSlide, delay);
        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(nextSlide, delay);
        }
        // Prefer attaching hover pause to the full slider container (so buttons also pause)
        const sliderEl = viewport.closest('.gallery-slider') || viewport;
        sliderEl.addEventListener('mouseenter', () => {
            clearInterval(timer);
        });
        sliderEl.addEventListener('mouseleave', () => {
            resetTimer();
        });

        // Wire prev/next buttons (if present)
        const btnPrev = sliderEl.querySelector('.slider-prev');
        const btnNext = sliderEl.querySelector('.slider-next');
        if (btnPrev) btnPrev.addEventListener('click', () => { prevSlide(); resetTimer(); });
        if (btnNext) btnNext.addEventListener('click', () => { nextSlide(); resetTimer(); });

        // Optional: swipe support for mobile
        let startX = null;
        viewport.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, {
            passive: true
        });
        viewport.addEventListener('touchend', (e) => {
            if (startX == null) return;
            const dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 40) {
                if (dx < 0) nextSlide();
                else prevSlide();
            }
            startX = null;
        });
    })();
    </script>
        <script>
        // Fasilitas slider: mirror dari galeri tapi mengambil gambar dari assets/fasilitas
        (function() {
            const images = <?php echo json_encode(array_values($fasilitasUrls), JSON_UNESCAPED_SLASHES); ?>;
            const viewport = document.querySelector('.fasilitas-viewport');
            if (!viewport || !images || !images.length) return;

            // Buat elemen item untuk setiap gambar
            images.forEach((src, idx) => {
                const item = document.createElement('div');
                item.className = 'gallery-item';
                item.dataset.index = String(idx);
                const img = document.createElement('img');
                img.src = src;
                img.alt = 'Fasilitas ' + (idx + 1);
                item.appendChild(img);
                viewport.appendChild(item);
            });

            const items = Array.from(viewport.querySelectorAll('.gallery-item'));
            let current = 0;

            function applyClasses() {
                const n = items.length;
                const prev = (current - 1 + n) % n;
                const next = (current + 1) % n;
                items.forEach((el, i) => {
                    el.classList.remove('is-prev', 'is-current', 'is-next', 'is-hidden');
                    if (i === current) el.classList.add('is-current');
                    else if (i === prev) el.classList.add('is-prev');
                    else if (i === next) el.classList.add('is-next');
                    else el.classList.add('is-hidden');
                });
            }

            function nextSlide() {
                current = (current + 1) % items.length;
                applyClasses();
            }

            function prevSlide() {
                current = (current - 1 + items.length) % items.length;
                applyClasses();
            }

            applyClasses();

            // Auto-play (5s) and controls
            let delay = 5000;
            let timer = setInterval(nextSlide, delay);
            function resetTimer() {
                clearInterval(timer);
                timer = setInterval(nextSlide, delay);
            }
            const sliderEl = viewport.closest('.gallery-slider') || viewport;
            sliderEl.addEventListener('mouseenter', () => {
                clearInterval(timer);
            });
            sliderEl.addEventListener('mouseleave', () => {
                resetTimer();
            });
            const btnPrev = sliderEl.querySelector('.slider-prev');
            const btnNext = sliderEl.querySelector('.slider-next');
            if (btnPrev) btnPrev.addEventListener('click', () => { prevSlide(); resetTimer(); });
            if (btnNext) btnNext.addEventListener('click', () => { nextSlide(); resetTimer(); });

            // Optional: swipe support for mobile
            let startX = null;
            viewport.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            }, {
                passive: true
            });
            viewport.addEventListener('touchend', (e) => {
                if (startX == null) return;
                const dx = e.changedTouches[0].clientX - startX;
                if (Math.abs(dx) > 40) {
                    if (dx < 0) nextSlide();
                    else prevSlide();
                }
                startX = null;
            });
        })();
        </script>
</body>

</html>