<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'create':
            handleCreate($conn);
            break;
        case 'update':
            handleUpdate($conn);
            break;
        case 'delete':
            handleDelete($conn);
            break;
        default:
            setError("Aksi tidak valid");
            header("Location: admin-fasilitas.php");
            exit;
    }
}

function handleCreate($conn) {
    $nama = $_POST['nama'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    if (empty($nama) || empty($deskripsi)) {
        setError("Semua field harus diisi");
        header("Location: admin-fasilitas.php");
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO fasilitas (nama, deskripsi) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $deskripsi);
    if ($stmt->execute()) {
        setSuccess("Fasilitas berhasil ditambahkan");
    } else {
        setError("Gagal menambahkan fasilitas: " . $conn->error);
    }
    $stmt->close();
    header("Location: admin-fasilitas.php");
    exit;
}

function handleUpdate($conn) {
    $id = $_POST['id'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    if (empty($id) || empty($nama) || empty($deskripsi)) {
        setError("Semua field harus diisi");
        header("Location: admin-fasilitas.php");
        exit;
    }
    $stmt = $conn->prepare("UPDATE fasilitas SET nama = ?, deskripsi = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nama, $deskripsi, $id);
    if ($stmt->execute()) {
        setSuccess("Fasilitas berhasil diperbarui");
    } else {
        setError("Gagal memperbarui fasilitas: " . $conn->error);
    }
    $stmt->close();
    header("Location: admin-fasilitas.php");
    exit;
}

function handleDelete($conn) {
    $id = $_POST['id'] ?? '';
    if (empty($id)) {
        setError("ID fasilitas tidak valid");
        header("Location: admin-fasilitas.php");
        exit;
    }
    $stmt = $conn->prepare("DELETE FROM fasilitas WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        setSuccess("Fasilitas berhasil dihapus");
    } else {
        setError("Gagal menghapus fasilitas: " . $conn->error);
    }
    $stmt->close();
    header("Location: admin-fasilitas.php");
    exit;
}
