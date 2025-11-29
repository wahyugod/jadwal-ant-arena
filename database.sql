-- Database untuk Jadwal Ant Arena
-- Jalankan script ini di phpMyAdmin atau MySQL client

CREATE DATABASE IF NOT EXISTS `ant-arena`;
USE `ant-arena`;

-- Tabel untuk menyimpan jadwal tim
CREATE TABLE IF NOT EXISTS jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jam VARCHAR(50) NOT NULL,
    senin VARCHAR(100) DEFAULT 'Tersedia',
    selasa VARCHAR(100) DEFAULT 'Tersedia',
    rabu VARCHAR(100) DEFAULT 'Tersedia',
    kamis VARCHAR(100) DEFAULT 'Tersedia',
    jumat VARCHAR(100) DEFAULT 'Tersedia',
    sabtu VARCHAR(100) DEFAULT 'Tersedia',
    minggu VARCHAR(100) DEFAULT 'Tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data awal (data yang sudah ada di tabel HTML)
INSERT INTO jadwal (jam, senin, selasa, rabu, kamis, jumat, sabtu, minggu) VALUES
('08.00 - 11.00', 'Garuda Smashers', 'Putra Jaya', 'Srikandi Shuttle', 'Mutiara Net', 'Bintang Smash', 'Nusantara Shuttle', 'Pelangi Raket'),
('11.00 - 14.00', 'Cahaya Raket', 'Tim Merah', 'Tim Biru', 'Raja Smash', 'Putri Lintas', 'Angin Lintas', 'Samudra Shuttle'),
('14.00 - 17.00', 'Surya Smash', 'Kiddo Shuttle', 'Seruni Raket', 'Guntur Badminton', 'Laskar Net', 'Pelita Smash', 'Arjuna Shuttle'),
('17.00 - 20.00', 'Satria Net', 'Jaya Raket', 'Kinara Shuttle', 'Victory Smash', 'Elang Shuttle', 'Putra Nusantara', 'Senja Raket'),
('20.00 - 23.00', 'Metro Smash', 'Jagad Shuttle', 'Mahkota Net', 'Raket Prima', 'Satya Shuttle', 'Pelopor Smash', 'Permata Raket');

-- Tabel untuk admin users
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (username: admin, password: admin123)
-- Password di-hash menggunakan password_hash() function
INSERT INTO admin_users (username, password) VALUES
('admin', '$2y$10$5qJ29YOUtYucoEkyJu7Iuea7ruljEo8.YR.GM97qZ0v3zqt.aejfq');

-- Tabel untuk transaksi pemasukan dan pengeluaran
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `kategori` enum('pemasukan','pengeluaran') NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_kategori` (`kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `transaksi` (`tanggal`, `deskripsi`, `kategori`, `nominal`) VALUES
('2025-11-01', 'Pembayaran sewa lapangan Tim A', 'pemasukan', 500000),
('2025-11-02', 'Pembayaran sewa lapangan Tim B', 'pemasukan', 500000),
('2025-11-03', 'Pembelian shuttle cock', 'pengeluaran', 150000),
('2025-11-04', 'Pembayaran listrik', 'pengeluaran', 200000);

-- Tabel untuk testimoni
CREATE TABLE IF NOT EXISTS `testimoni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `testimoni` text NOT NULL,
  `foto` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data sample untuk testimoni
INSERT INTO `testimoni` (`nama`, `testimoni`, `foto`, `tanggal`) VALUES
('John Doe', 'Lapangan sangat bagus dan pelayanannya ramah!', 'default1.jpg', '2025-11-01'),
('Jane Smith', 'Fasilitas lengkap dan nyaman untuk berlatih.', 'default2.jpg', '2025-11-02');

-- Tabel untuk fasilitas
CREATE TABLE IF NOT EXISTS `fasilitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data sample fasilitas
INSERT INTO `fasilitas` (`nama`, `deskripsi`, `foto`) VALUES
('Lapangan Utama', 'Lapangan bulutangkis utama dengan lantai vinyl dan pencahayaan LED', 'default1.jpg'),
('Ruang Ganti', 'Ruang ganti bersih dan nyaman untuk pemain', 'default2.jpg'),
('Kantin', 'Kantin menyediakan makanan dan minuman ringan', 'default3.jpg'),
('Parkir Luas', 'Area parkir kendaraan yang luas dan aman', 'default4.jpg');

-- Tabel untuk reservasi
CREATE TABLE IF NOT EXISTS `reservasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_tim` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `hari` enum('senin','selasa','rabu','kamis','jumat','sabtu','minggu') NOT NULL,
  `jam` varchar(50) NOT NULL,
  `paket` enum('harian','bulanan','tahunan') NOT NULL DEFAULT 'jam',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `pesan` text,
  `tanggal_mulai` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_tanggal` (`tanggal_mulai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data sample untuk reservasi
INSERT INTO `reservasi` (`nama_tim`, `email`, `no_telepon`, `hari`, `jam`, `status`, `pesan`, `tanggal_mulai`) VALUES
('Tim Garuda', 'tim.garuda@email.com', '081234567890', 'senin', '08.00 - 11.00', 'pending', 'Mohon slot untuk tim regular', '2025-11-10'),
('Meteor Club', 'meteor.club@email.com', '082345678901', 'rabu', '17.00 - 20.00', 'pending', 'Reservasi untuk tim baru', '2025-11-12');

CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertanyaan` varchar(255) NOT NULL,
  `jawaban` text NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `faqs` (`pertanyaan`, `jawaban`, `urutan`) VALUES 
('Bagaimana cara melakukan reservasi lapangan?', 'Anda dapat melakukan reservasi melalui menu "Reservasi" di website, pilih tanggal dan jam yang tersedia, lalu konfirmasi pembayaran.', 1), 
('Apakah bisa membatalkan reservasi?', 'Ya, pembatalan bisa dilakukan maksimal 12 jam sebelum waktu main melalui menu "Riwayat Reservasi".', 2), 
('Apa metode pembayaran yang tersedia?', 'Kami menerima pembayaran melalui transfer bank, e-wallet (OVO, DANA, GoPay), dan kartu debit.', 3), 
('Berapa lama durasi satu sesi sewa lapangan?', 'Satu sesi sewa lapangan berdurasi 1 jam. Anda dapat menambah sesi sesuai kebutuhan jika tersedia.', 4), 
('Apakah tersedia penyewaan raket dan shuttlecock?', 'Ya, tersedia penyewaan raket dan pembelian shuttlecock langsung di lokasi.', 5);

-- Tabel Hero (konten bagian beranda)
CREATE TABLE IF NOT EXISTS `hero` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `heading` VARCHAR(255) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data awal hero (gunakan gambar default jika belum diganti)
INSERT INTO `hero` (`heading`, `image_path`) VALUES ('Raih Kemenangan di Setiap Pukulan!', 'assets/hero/home.jpeg');

-- Tabel About (konten bagian tentang)
CREATE TABLE IF NOT EXISTS `about` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `image_path` VARCHAR(255) NOT NULL,
  `paragraph_1` TEXT NOT NULL,
  `paragraph_2` TEXT NOT NULL,
  `paragraph_3` TEXT NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data awal about
INSERT INTO `about` (`image_path`, `paragraph_1`, `paragraph_2`, `paragraph_3`) VALUES 
('assets/img/about.jpeg', 
'<strong>@nt\'s Arena</strong> berdiri pada Juli 2025 dan dikelola oleh <strong>Ariyanto</strong>. Kami memiliki tujuan untuk mendorong semangat olahraga dan membangun komunitas bulutangkis yang aktif. Kebersihan terjamin dan fasilitas kami lengkap untuk kenyamanan bermain Anda.',
'Dengan dukungan fasilitas lengkap seperti lapangan berkualitas, area istirahat yang nyaman, serta layanan pemesanan jadwal yang mudah, kami siap menjadi tempat terbaik bagi Anda untuk berlatih, bersaing, dan menikmati permainan bulutangkis setiap hari.',
'Sebagai arena yang terus berkembang, @nt\'s Arena juga berkomitmen untuk memberikan pengalaman terbaik bagi setiap pengunjung melalui pelayanan ramah dan sistem pengelolaan yang profesional. Kami selalu terbuka untuk berbagai kegiatan seperti latihan rutin, sparring, hingga turnamen internal yang dapat meningkatkan kemampuan sekaligus mempererat hubungan antar pemain.');

-- Tabel Paket Harga
CREATE TABLE IF NOT EXISTS `paket` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `subtitle` VARCHAR(255) NOT NULL,
  `price` INT NOT NULL,
  `period` VARCHAR(50) NOT NULL,
  `features` TEXT NOT NULL,
  `urutan` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data paket awal (prefix ! menandakan fitur tidak termasuk / disabled)
INSERT INTO `paket` (`nama`, `slug`, `subtitle`, `price`, `period`, `features`, `urutan`) VALUES
('SEWA PER JAM', 'jam', 'Cocok untuk main santai atau trial', 50000, 'Per Jam', 'Akses Lapangan\nMinimal Sewa 3 Jam\n!Diskon Member\n!Prioritas Booking\n!Bonus Event Internal', 1),
('SEWA BULANAN', 'bulanan', 'Hemat untuk pemain rutin', 350000, 'Per Bulan', 'Akses Lapangan\n4x Main (3 jam/sesi)\nDiskon Member 15%\nPrioritas Booking\n!Bonus Event Internal', 2),
('SEWA TAHUNAN', 'tahunan', 'Pilihan maksimal untuk klub aktif', 3600000, 'Per Tahun', 'Akses Lapangan\n60x Main (3 jam/sesi)\nDiskon Member 25%\nPrioritas Booking\nBonus Event Internal', 3);

-- Tabel Kontak
CREATE TABLE IF NOT EXISTS `kontak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `whatsapp` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `instagram` VARCHAR(255) NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data awal kontak
INSERT INTO `kontak` (`whatsapp`, `email`, `instagram`) VALUES 
('+62 812-3456-7890', 'info@nts-arena.com', 'https://instagram.com/ntsarena');

-- Tabel Footer
CREATE TABLE IF NOT EXISTS `footer` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `address` TEXT NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `instagram` VARCHAR(255),
  `facebook` VARCHAR(255),
  `twitter` VARCHAR(255),
  `linkedin` VARCHAR(255),
  `hours_weekday` VARCHAR(100) NOT NULL,
  `hours_weekend` VARCHAR(100) NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data awal footer
INSERT INTO `footer` (`address`, `phone`, `email`, `instagram`, `facebook`, `twitter`, `linkedin`, `hours_weekday`, `hours_weekend`) VALUES 
('Jl. Rejang Raya Gg Barokah, Bukit Pinang, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75131', '+62 812-3456-7890', 'info@nts-arena.com', 'https://instagram.com/ntsarena', '#', '#', '#', 'Senin-Jumat: 8 Pagi - 11 Malam', 'Sabtu-Minggu: 8 Pagi - 11 Malam');

ALTER TABLE footer DROP COLUMN address, DROP COLUMN phone, DROP COLUMN email, ADD COLUMN description TEXT AFTER id, ADD COLUMN copyright VARCHAR(255) AFTER description;