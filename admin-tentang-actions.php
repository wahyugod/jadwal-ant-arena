<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setError('Metode tidak diizinkan');
    header('Location: admin-tentang.php');
    exit;
}

$action = $_POST['action'] ?? '';

// Handle upload gambar slider
if ($action === 'upload_images') {
    if (empty($_FILES['images']['name'][0])) {
        setError('Tidak ada gambar yang dipilih');
        header('Location: admin-tentang.php');
        exit;
    }

    $targetDir = __DIR__ . '/assets/tentang/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $uploaded = 0;
    $errors = [];
    $allowed = ['jpg','jpeg','png','gif','webp'];

    foreach ($_FILES['images']['name'] as $key => $name) {
        if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
            continue;
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "$name - Format tidak valid";
            continue;
        }

        if ($_FILES['images']['size'][$key] > 5 * 1024 * 1024) {
            $errors[] = "$name - Ukuran melebihi 5MB";
            continue;
        }

        $newName = 'tentang_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetFile = $targetDir . $newName;

        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $targetFile)) {
            $uploaded++;
        } else {
            $errors[] = "$name - Gagal upload";
        }
    }

    if ($uploaded > 0) {
        setSuccess("$uploaded gambar berhasil diupload" . (!empty($errors) ? '. Beberapa gagal: ' . implode(', ', $errors) : ''));
    } else {
        setError('Semua gambar gagal diupload: ' . implode(', ', $errors));
    }

    header('Location: admin-tentang.php');
    exit;
}

// Handle delete gambar
if ($action === 'delete_image') {
    $filename = $_POST['filename'] ?? '';
    if ($filename === '') {
        setError('Nama file tidak valid');
        header('Location: admin-tentang.php');
        exit;
    }

    $targetFile = __DIR__ . '/assets/tentang/' . basename($filename);
    if (is_file($targetFile)) {
        if (unlink($targetFile)) {
            setSuccess('Gambar berhasil dihapus');
        } else {
            setError('Gagal menghapus gambar');
        }
    } else {
        setError('File tidak ditemukan');
    }

    header('Location: admin-tentang.php');
    exit;
}

// Handle update text
if ($action === 'update_text') {
    $paragraph1 = trim($_POST['paragraph_1'] ?? '');
    $paragraph2 = trim($_POST['paragraph_2'] ?? '');
    $paragraph3 = trim($_POST['paragraph_3'] ?? '');

    if ($paragraph1 === '' || $paragraph2 === '' || $paragraph3 === '') {
        setError('Semua paragraf wajib diisi');
        header('Location: admin-tentang.php');
        exit;
    }

    // Ambil existing record (jika ada)
    $existing = null;
    $res = $conn->query('SELECT * FROM about ORDER BY id ASC LIMIT 1');
    if ($res) { $existing = $res->fetch_assoc(); $res->free_result(); }

    if ($existing) {
        $stmt = $conn->prepare('UPDATE about SET paragraph_1 = ?, paragraph_2 = ?, paragraph_3 = ? WHERE id = ?');
        $stmt->bind_param('sssi', $paragraph1, $paragraph2, $paragraph3, $existing['id']);
        if ($stmt->execute()) {
            setSuccess('Teks tentang berhasil diperbarui');
        } else {
            setError('Gagal memperbarui teks: ' . $conn->error);
        }
        $stmt->close();
    } else {
        $imagePath = 'assets/img/about.jpeg';
        $stmt = $conn->prepare('INSERT INTO about (image_path, paragraph_1, paragraph_2, paragraph_3) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $imagePath, $paragraph1, $paragraph2, $paragraph3);
        if ($stmt->execute()) {
            setSuccess('Teks tentang berhasil dibuat');
        } else {
            setError('Gagal membuat teks: ' . $conn->error);
        }
        $stmt->close();
    }

    header('Location: admin-tentang.php');
    exit;
}

setError('Aksi tidak valid');
header('Location: admin-tentang.php');
exit;
