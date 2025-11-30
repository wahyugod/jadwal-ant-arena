<?php
// Handler form reservasi (AJAX) untuk section Reservasi di halaman utama
// Mengembalikan string 'OK' jika berhasil sesuai dengan assets/vendor/php-email-form/validate.js

require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo 'Method not allowed';
        exit;
    }

    // Ambil input
    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $nohp   = trim($_POST['nohp'] ?? '');
    $tanggal= trim($_POST['tanggal'] ?? ''); // format yyyy-mm-dd dari input type=date
    $waktu  = trim($_POST['waktu'] ?? '');   // format HH:MM dari input type=time
    $paket  = trim($_POST['paket'] ?? '');   // paket: jam | bulanan | tahunan
    $pesan  = trim($_POST['pesan'] ?? '');

    // Validasi sederhana
    if ($nama === '' || $email === '' || $nohp === '' || $tanggal === '' || $waktu === '' || $paket === '') {
        http_response_code(400);
        echo 'Semua field wajib diisi';
        exit;
    }

    // Normalisasi paket: frontend uses value 'jam' for harian — map it to DB enum 'harian'
    if ($paket === 'jam') {
        $paket = 'harian';
    }

    // Validasi paket sesuai struktur DB (paket di DB: harian, bulanan, tahunan)
    $allowedPaket = ['harian','bulanan','tahunan'];
    if (!in_array($paket, $allowedPaket, true)) {
        http_response_code(400);
        echo 'Paket tidak valid';
        exit;
    }

    // Validasi tanggal
    $ts = strtotime($tanggal);
    if ($ts === false) {
        http_response_code(400);
        echo 'Tanggal tidak valid';
        exit;
    }

    // Tentukan nilai 'hari' (senin..minggu) dari tanggal
    // PHP: N => 1 (Mon) .. 7 (Sun)
    $mapHari = [1=>'senin',2=>'selasa',3=>'rabu',4=>'kamis',5=>'jumat',6=>'sabtu',7=>'minggu'];
    $n = (int)date('N', $ts);
    $hari = $mapHari[$n] ?? 'senin';

    // Simpan ke DB
    $conn = getConnection();

    // Normalisasi jam agar cocok dengan format di tabel jadwal (contoh: 08.00 - 11.00)
    $jamStr = $waktu;
    $clean = preg_replace('/\s+/', '', $waktu);
    if ($clean && strpos($clean, '-') !== false) {
        [$start, $end] = explode('-', $clean, 2);
        $fmt = function(string $t){
            // Ubah 08:00 -> 08.00, 8:00 -> 08.00
            $t = trim($t);
            // Pastikan ada ':'
            if (strpos($t, ':') === false) return $t;
            [$h, $m] = explode(':', $t, 2);
            $h = str_pad(preg_replace('/[^0-9]/', '', $h), 2, '0', STR_PAD_LEFT);
            $m = str_pad(preg_replace('/[^0-9]/', '', $m), 2, '0', STR_PAD_LEFT);
            return $h . '.' . $m;
        };
        $s = $fmt($start);
        $e = $fmt($end);
        if ($s && $e) {
            $jamStr = $s . ' - ' . $e;
        }
    }

    // Cek ketersediaan slot pada tabel jadwal
    $validDays = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
    if (!in_array($hari, $validDays, true)) {
        echo 'Jadwal tidak tersedia, silahkan pilih hari atau jam lain!';
        exit;
    }

    $checkSql = "SELECT `$hari` AS slot FROM jadwal WHERE jam = ? LIMIT 1";
    $check = $conn->prepare($checkSql);
    if ($check) {
        $check->bind_param('s', $jamStr);
        if ($check->execute()) {
            $res = $check->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $slot = $row['slot'] ?? '';
            $trim = trim((string)$slot);
            $occupied = ($trim !== '' && $trim !== '-' && strcasecmp($trim, 'Tersedia') !== 0);
            if ($occupied) {
                // Beri pesan ramah sesuai permintaan, status 200 agar tampil di UI
                echo 'Jadwal tidak tersedia, silahkan pilih hari atau jam lain!';
                exit;
            }
            if (!$row) {
                // Jam tidak ditemukan di jadwal
                echo 'Jadwal tidak tersedia, silahkan pilih hari atau jam lain!';
                exit;
            }
        }
        $check->close();
    }

    // Lanjut simpan reservasi
    $sql = 'INSERT INTO reservasi (nama_tim, email, no_telepon, hari, jam, paket, status, pesan, tanggal_mulai) VALUES (?,?,?,?,?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Gagal prepare statement');
    }

    // Status harus variabel (bind_param by reference)
    $status = 'pending';
    $stmt->bind_param(
        'sssssssss',
        $nama,
        $email,
        $nohp,
        $hari,
        $jamStr,
        $paket,
        $status,
        $pesan,
        $tanggal
    );

    if (!$stmt->execute()) {
        throw new Exception('Gagal menyimpan reservasi');
    }

    $stmt->close();
    $conn->close();

    // Sukses
    echo 'OK';
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Terjadi kesalahan: ' . $e->getMessage();
}