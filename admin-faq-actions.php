<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertanyaan` varchar(255) NOT NULL,
  `jawaban` text NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_urutan` (`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$action = $_POST['action'] ?? '';

try {
    if ($action === 'create') {
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
        $jawaban    = trim($_POST['jawaban'] ?? '');
        $urutan     = (int)($_POST['urutan'] ?? 0);
        if ($pertanyaan === '' || $jawaban === '') { throw new Exception('Pertanyaan dan jawaban wajib diisi'); }
        $stmt = $conn->prepare('INSERT INTO faqs (pertanyaan, jawaban, urutan) VALUES (?,?,?)');
        $stmt->bind_param('ssi', $pertanyaan, $jawaban, $urutan);
        if (!$stmt->execute()) throw new Exception('Gagal menyimpan');
        setSuccess('FAQ berhasil ditambahkan');
    } elseif ($action === 'update') {
        $id         = (int)($_POST['id'] ?? 0);
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
        $jawaban    = trim($_POST['jawaban'] ?? '');
        $urutan     = (int)($_POST['urutan'] ?? 0);
        if ($id <= 0) throw new Exception('ID tidak valid');
        if ($pertanyaan === '' || $jawaban === '') { throw new Exception('Pertanyaan dan jawaban wajib diisi'); }
        $stmt = $conn->prepare('UPDATE faqs SET pertanyaan=?, jawaban=?, urutan=? WHERE id=?');
        $stmt->bind_param('ssii', $pertanyaan, $jawaban, $urutan, $id);
        if (!$stmt->execute()) throw new Exception('Gagal mengubah');
        setSuccess('FAQ berhasil diubah');
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) throw new Exception('ID tidak valid');
        $stmt = $conn->prepare('DELETE FROM faqs WHERE id=?');
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) throw new Exception('Gagal menghapus');
        setSuccess('FAQ berhasil dihapus');
    } else {
        throw new Exception('Aksi tidak dikenal');
    }
} catch (Exception $e) {
    setError($e->getMessage());
}

header('Location: admin-faq.php');
exit;