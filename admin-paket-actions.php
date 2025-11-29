<?php
require_once 'config.php';
requireLogin();
$conn = getConnection();

$action = $_POST['action'] ?? '';

function parseFeatures($raw) {
    $lines = preg_split('/\r?\n/', trim($raw));
    $clean = [];
    foreach ($lines as $l) {
        $l = trim($l);
        if ($l === '') continue;
        $clean[] = $l; // keep ! prefix if present
    }
    return implode("\n", $clean);
}

if ($action === 'create') {
    $nama = trim($_POST['nama'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $price = intval($_POST['price'] ?? 0);
    $period = trim($_POST['period'] ?? '');
    $features = parseFeatures($_POST['features'] ?? '');
    $urutan = intval($_POST['urutan'] ?? 0);

    if ($nama === '' || $slug === '' || $subtitle === '' || $price <= 0 || $period === '' || $features === '') {
        setError('Semua field wajib diisi dan harga harus > 0');
        header('Location: admin-paket.php');
        exit;
    }

    $stmt = $conn->prepare('INSERT INTO paket (nama, slug, subtitle, price, period, features, urutan) VALUES (?,?,?,?,?,?,?)');
    $stmt->bind_param('sssissi', $nama, $slug, $subtitle, $price, $period, $features, $urutan);
    if ($stmt->execute()) {
        setSuccess('Paket berhasil ditambahkan');
    } else {
        setError('Gagal menambahkan paket: ' . $conn->error);
    }
    $stmt->close();
    header('Location: admin-paket.php');
    exit;
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $price = intval($_POST['price'] ?? 0);
    $period = trim($_POST['period'] ?? '');
    $features = parseFeatures($_POST['features'] ?? '');
    $urutan = intval($_POST['urutan'] ?? 0);

    if ($id <= 0 || $nama === '' || $slug === '' || $subtitle === '' || $price <= 0 || $period === '' || $features === '') {
        setError('Data update tidak valid');
        header('Location: admin-paket.php');
        exit;
    }
    $stmt = $conn->prepare('UPDATE paket SET nama=?, slug=?, subtitle=?, price=?, period=?, features=?, urutan=? WHERE id=?');
    $stmt->bind_param('sssissii', $nama, $slug, $subtitle, $price, $period, $features, $urutan, $id);
    if ($stmt->execute()) {
        setSuccess('Paket berhasil diperbarui');
    } else {
        setError('Gagal memperbarui paket: ' . $conn->error);
    }
    $stmt->close();
    header('Location: admin-paket.php');
    exit;
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        setError('ID paket tidak valid');
        header('Location: admin-paket.php');
        exit;
    }
    $stmt = $conn->prepare('DELETE FROM paket WHERE id=?');
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        setSuccess('Paket berhasil dihapus');
    } else {
        setError('Gagal menghapus paket: ' . $conn->error);
    }
    $stmt->close();
    header('Location: admin-paket.php');
    exit;
}

// Aksi tidak dikenal
setError('Aksi tidak dikenal');
header('Location: admin-paket.php');
exit;
?>