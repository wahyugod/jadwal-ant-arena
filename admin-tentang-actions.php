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

// Handle update Why Us
if ($action === 'update_why_us') {
    $title = trim($_POST['why_title'] ?? '');
    $paragraph1 = trim($_POST['why_paragraph_1'] ?? '');
    $paragraph2 = trim($_POST['why_paragraph_2'] ?? '');
    $f1_icon = trim($_POST['feature_1_icon'] ?? '');
    $f1_title = trim($_POST['feature_1_title'] ?? '');
    $f1_desc = trim($_POST['feature_1_desc'] ?? '');
    $f2_icon = trim($_POST['feature_2_icon'] ?? '');
    $f2_title = trim($_POST['feature_2_title'] ?? '');
    $f2_desc = trim($_POST['feature_2_desc'] ?? '');
    $f3_icon = trim($_POST['feature_3_icon'] ?? '');
    $f3_title = trim($_POST['feature_3_title'] ?? '');
    $f3_desc = trim($_POST['feature_3_desc'] ?? '');

    if ($title === '' || $paragraph1 === '' || $paragraph2 === '') {
        setError('Judul dan paragraf wajib diisi');
        header('Location: admin-tentang.php');
        exit;
    }

    $existing = null;
    $res = $conn->query('SELECT * FROM why_us ORDER BY id ASC LIMIT 1');
    if ($res) { $existing = $res->fetch_assoc(); $res->free_result(); }

    if ($existing) {
        $stmt = $conn->prepare('UPDATE why_us SET title=?, paragraph_1=?, paragraph_2=?, feature_1_icon=?, feature_1_title=?, feature_1_desc=?, feature_2_icon=?, feature_2_title=?, feature_2_desc=?, feature_3_icon=?, feature_3_title=?, feature_3_desc=? WHERE id=?');
        $stmt->bind_param('ssssssssssssi', $title, $paragraph1, $paragraph2, $f1_icon, $f1_title, $f1_desc, $f2_icon, $f2_title, $f2_desc, $f3_icon, $f3_title, $f3_desc, $existing['id']);
    } else {
        $stmt = $conn->prepare('INSERT INTO why_us (title, paragraph_1, paragraph_2, feature_1_icon, feature_1_title, feature_1_desc, feature_2_icon, feature_2_title, feature_2_desc, feature_3_icon, feature_3_title, feature_3_desc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssssssss', $title, $paragraph1, $paragraph2, $f1_icon, $f1_title, $f1_desc, $f2_icon, $f2_title, $f2_desc, $f3_icon, $f3_title, $f3_desc);
    }

    if ($stmt->execute()) {
        setSuccess('Data Why Us berhasil diperbarui');
    } else {
        setError('Gagal memperbarui Why Us: ' . $conn->error);
    }
    $stmt->close();

    header('Location: admin-tentang.php');
    exit;
}

// Handle update Stats
if ($action === 'update_stats') {
    $s1_icon = trim($_POST['stat_1_icon'] ?? '');
    $s1_value = intval($_POST['stat_1_value'] ?? 0);
    $s1_label = trim($_POST['stat_1_label'] ?? '');
    $s2_icon = trim($_POST['stat_2_icon'] ?? '');
    $s2_value = intval($_POST['stat_2_value'] ?? 0);
    $s2_label = trim($_POST['stat_2_label'] ?? '');
    $s3_icon = trim($_POST['stat_3_icon'] ?? '');
    $s3_value = intval($_POST['stat_3_value'] ?? 0);
    $s3_label = trim($_POST['stat_3_label'] ?? '');
    $s4_icon = trim($_POST['stat_4_icon'] ?? '');
    $s4_value = intval($_POST['stat_4_value'] ?? 0);
    $s4_label = trim($_POST['stat_4_label'] ?? '');

    $existing = null;
    $res = $conn->query('SELECT * FROM stats ORDER BY id ASC LIMIT 1');
    if ($res) { $existing = $res->fetch_assoc(); $res->free_result(); }

    if ($existing) {
        $stmt = $conn->prepare('UPDATE stats SET stat_1_icon=?, stat_1_value=?, stat_1_label=?, stat_2_icon=?, stat_2_value=?, stat_2_label=?, stat_3_icon=?, stat_3_value=?, stat_3_label=?, stat_4_icon=?, stat_4_value=?, stat_4_label=? WHERE id=?');
        $stmt->bind_param('sisisisisisii', $s1_icon, $s1_value, $s1_label, $s2_icon, $s2_value, $s2_label, $s3_icon, $s3_value, $s3_label, $s4_icon, $s4_value, $s4_label, $existing['id']);
    } else {
        $stmt = $conn->prepare('INSERT INTO stats (stat_1_icon, stat_1_value, stat_1_label, stat_2_icon, stat_2_value, stat_2_label, stat_3_icon, stat_3_value, stat_3_label, stat_4_icon, stat_4_value, stat_4_label) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sisisisisisi', $s1_icon, $s1_value, $s1_label, $s2_icon, $s2_value, $s2_label, $s3_icon, $s3_value, $s3_label, $s4_icon, $s4_value, $s4_label);
    }

    if ($stmt->execute()) {
        setSuccess('Data Statistik berhasil diperbarui');
    } else {
        setError('Gagal memperbarui Statistik: ' . $conn->error);
    }
    $stmt->close();

    header('Location: admin-tentang.php');
    exit;
}

setError('Aksi tidak valid');
header('Location: admin-tentang.php');
exit;
