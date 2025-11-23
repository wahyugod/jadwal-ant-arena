<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setError('Metode tidak diizinkan');
    header('Location: admin-tentang.php');
    exit;
}

$paragraph1 = trim($_POST['paragraph_1'] ?? '');
$paragraph2 = trim($_POST['paragraph_2'] ?? '');
$paragraph3 = trim($_POST['paragraph_3'] ?? '');

if ($paragraph1 === '' || $paragraph2 === '' || $paragraph3 === '') {
    setError('Semua paragraf wajib diisi');
    header('Location: admin-tentang.php');
    exit;
}

$imagePath = null;
$uploadedNew = false;

// Handle upload jika ada file baru
if (!empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) {
        setError('Format gambar tidak valid (jpg, jpeg, png, gif, webp)');
        header('Location: admin-tentang.php');
        exit;
    }
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        setError('Ukuran gambar maksimal 5MB');
        header('Location: admin-tentang.php');
        exit;
    }
    $targetDir = 'assets/img/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $newName = 'about_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetFile = $targetDir . $newName;
    if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
        setError('Gagal mengupload gambar');
        header('Location: admin-tentang.php');
        exit;
    }
    $imagePath = $targetFile;
    $uploadedNew = true;
}

// Ambil existing record (jika ada)
$existing = null;
$res = $conn->query('SELECT * FROM about ORDER BY id ASC LIMIT 1');
if ($res) { $existing = $res->fetch_assoc(); $res->free_result(); }

if ($existing) {
    // Jika ada gambar baru dan lama berbeda -> hapus lama
    if ($uploadedNew && !empty($existing['image_path']) && strpos($existing['image_path'], 'assets/img/') === 0) {
        $oldFile = __DIR__ . '/' . $existing['image_path'];
        if (is_file($oldFile) && basename($oldFile) !== 'about.jpeg') { // jangan hapus default
            @unlink($oldFile);
        }
    }
    if ($imagePath === null) {
        $imagePath = $existing['image_path'];
    }
    $stmt = $conn->prepare('UPDATE about SET image_path = ?, paragraph_1 = ?, paragraph_2 = ?, paragraph_3 = ? WHERE id = ?');
    $stmt->bind_param('ssssi', $imagePath, $paragraph1, $paragraph2, $paragraph3, $existing['id']);
    if ($stmt->execute()) {
        setSuccess('Tentang berhasil diperbarui');
    } else {
        setError('Gagal memperbarui tentang: ' . $conn->error);
    }
    $stmt->close();
} else {
    if ($imagePath === null) {
        $imagePath = 'assets/img/about.jpeg';
    }
    $stmt = $conn->prepare('INSERT INTO about (image_path, paragraph_1, paragraph_2, paragraph_3) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $imagePath, $paragraph1, $paragraph2, $paragraph3);
    if ($stmt->execute()) {
        setSuccess('Tentang berhasil dibuat');
    } else {
        setError('Gagal membuat tentang: ' . $conn->error);
    }
    $stmt->close();
}

header('Location: admin-tentang.php');
exit;
