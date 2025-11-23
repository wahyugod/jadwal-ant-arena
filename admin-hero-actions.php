<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setError('Metode tidak diizinkan');
    header('Location: admin-hero.php');
    exit;
}

$action = $_POST['action'] ?? 'update';

// Handle delete image
if ($action === 'delete_image') {
    $filename = $_POST['filename'] ?? '';
    if ($filename !== '' && preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
        $filePath = __DIR__ . '/assets/hero/' . $filename;
        if (is_file($filePath)) {
            @unlink($filePath);
            setSuccess('Gambar berhasil dihapus');
        } else {
            setError('File tidak ditemukan');
        }
    } else {
        setError('Nama file tidak valid');
    }
    header('Location: admin-hero.php');
    exit;
}

$heading = trim($_POST['heading'] ?? '');
if ($heading === '') {
    setError('Judul hero wajib diisi');
    header('Location: admin-hero.php');
    exit;
}

$uploadedCount = 0;

// Handle upload multiple images
if (!empty($_FILES['images']['name'][0])) {
    $targetDir = 'assets/hero/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $allowed = ['jpg','jpeg','png','gif','webp'];
    
    foreach ($_FILES['images']['name'] as $idx => $name) {
        if (empty($name)) continue;
        $tmpName = $_FILES['images']['tmp_name'][$idx];
        $size = $_FILES['images']['size'][$idx];
        $error = $_FILES['images']['error'][$idx];
        
        if ($error !== UPLOAD_ERR_OK) continue;
        
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            setError('File ' . htmlspecialchars($name) . ' format tidak valid (jpg, jpeg, png, gif, webp)');
            continue;
        }
        if ($size > 5 * 1024 * 1024) { // 5MB
            setError('File ' . htmlspecialchars($name) . ' terlalu besar (max 5MB)');
            continue;
        }
        
        $newName = 'hero_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetFile = $targetDir . $newName;
        
        if (move_uploaded_file($tmpName, $targetFile)) {
            $uploadedCount++;
        }
    }
    
    if ($uploadedCount > 0) {
        setSuccess($uploadedCount . ' gambar berhasil diupload');
    }
}

// Update atau create heading di database (image_path tidak digunakan lagi, hanya heading)
$existing = null;
$res = $conn->query('SELECT * FROM hero ORDER BY id ASC LIMIT 1');
if ($res) { $existing = $res->fetch_assoc(); $res->free_result(); }

if ($existing) {
    $stmt = $conn->prepare('UPDATE hero SET heading = ? WHERE id = ?');
    $stmt->bind_param('si', $heading, $existing['id']);
    if ($stmt->execute()) {
        if ($uploadedCount === 0 && empty($_FILES['images']['name'][0])) {
            setSuccess('Heading berhasil diperbarui');
        }
    } else {
        setError('Gagal memperbarui heading: ' . $conn->error);
    }
    $stmt->close();
} else {
    // Buat record baru jika belum ada
    $dummyPath = 'assets/hero/home.jpeg';
    $stmt = $conn->prepare('INSERT INTO hero (heading, image_path) VALUES (?, ?)');
    $stmt->bind_param('ss', $heading, $dummyPath);
    if ($stmt->execute()) {
        if ($uploadedCount === 0) {
            setSuccess('Heading berhasil dibuat');
        }
    } else {
        setError('Gagal membuat heading: ' . $conn->error);
    }
    $stmt->close();
}

header('Location: admin-hero.php');
exit;
