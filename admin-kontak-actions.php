<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setError('Metode tidak diizinkan');
    header('Location: admin-kontak.php');
    exit;
}

$whatsapp = trim($_POST['whatsapp'] ?? '');
$email = trim($_POST['email'] ?? '');
$instagram = trim($_POST['instagram'] ?? '');

if ($whatsapp === '' || $email === '' || $instagram === '') {
    setError('Semua field wajib diisi');
    header('Location: admin-kontak.php');
    exit;
}

// Cek apakah sudah ada record
$existing = null;
$res = $conn->query('SELECT * FROM kontak ORDER BY id ASC LIMIT 1');
if ($res) { $existing = $res->fetch_assoc(); $res->free_result(); }

if ($existing) {
    $stmt = $conn->prepare('UPDATE kontak SET whatsapp=?, email=?, instagram=? WHERE id=?');
    $stmt->bind_param('sssi', $whatsapp, $email, $instagram, $existing['id']);
    if ($stmt->execute()) {
        setSuccess('Kontak berhasil diperbarui');
    } else {
        setError('Gagal memperbarui kontak: ' . $conn->error);
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare('INSERT INTO kontak (whatsapp, email, instagram) VALUES (?,?,?)');
    $stmt->bind_param('sss', $whatsapp, $email, $instagram);
    if ($stmt->execute()) {
        setSuccess('Kontak berhasil dibuat');
    } else {
        setError('Gagal membuat kontak: ' . $conn->error);
    }
    $stmt->close();
}

header('Location: admin-kontak.php');
exit;
