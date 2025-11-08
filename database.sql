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
INSERT INTO `fasilitas` (`nama`, `deskripsi`) VALUES
('Lapangan Utama', 'Lapangan bulutangkis utama dengan lantai vinyl dan pencahayaan LED'),
('Ruang Ganti', 'Ruang ganti bersih dan nyaman untuk pemain'),
('Kantin', 'Kantin menyediakan makanan dan minuman ringan'),
('Parkir Luas', 'Area parkir kendaraan yang luas dan aman');
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
