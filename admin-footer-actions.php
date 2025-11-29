<?php
require_once 'config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin-footer.php');
    exit;
}

$conn = getConnection();

$description = trim($_POST['description'] ?? '');
$copyright = trim($_POST['copyright'] ?? '');
$instagram = trim($_POST['instagram'] ?? '#');
$facebook = trim($_POST['facebook'] ?? '#');
$twitter = trim($_POST['twitter'] ?? '#');
$linkedin = trim($_POST['linkedin'] ?? '#');
$hours_weekday = trim($_POST['hours_weekday'] ?? '');
$hours_weekend = trim($_POST['hours_weekend'] ?? '');

if (empty($description) || empty($copyright) || empty($hours_weekday) || empty($hours_weekend)) {
    $_SESSION['error_message'] = 'Deskripsi, copyright, dan jam operasional wajib diisi!';
    $conn->close();
    header('Location: admin-footer.php');
    exit;
}

// Cek apakah ada data footer
$res = $conn->query("SELECT id FROM footer LIMIT 1");
$exists = ($res && $res->num_rows > 0);
if ($res) $res->free_result();

if ($exists) {
    $stmt = $conn->prepare("UPDATE footer SET description=?, copyright=?, instagram=?, facebook=?, twitter=?, linkedin=?, hours_weekday=?, hours_weekend=? WHERE id=(SELECT id FROM (SELECT id FROM footer LIMIT 1) AS tmp)");
    $stmt->bind_param('ssssssss', $description, $copyright, $instagram, $facebook, $twitter, $linkedin, $hours_weekday, $hours_weekend);
} else {
    $stmt = $conn->prepare("INSERT INTO footer (description, copyright, instagram, facebook, twitter, linkedin, hours_weekday, hours_weekend) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssss', $description, $copyright, $instagram, $facebook, $twitter, $linkedin, $hours_weekday, $hours_weekend);
}

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'Informasi footer berhasil diperbarui!';
} else {
    $_SESSION['error_message'] = 'Gagal memperbarui footer: ' . $stmt->error;
}

$stmt->close();
$conn->close();
header('Location: admin-footer.php');
exit;
