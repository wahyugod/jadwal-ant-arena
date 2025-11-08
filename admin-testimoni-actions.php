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
            header("Location: admin-testimoni.php");
            exit;
    }
}

function handleCreate($conn) {
    $nama = $_POST['nama'] ?? '';
    $testimoni = $_POST['testimoni'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';

    // Validasi input
    if (empty($nama) || empty($testimoni) || empty($tanggal) || empty($_FILES['foto']['name'])) {
        setError("Semua field harus diisi");
        header("Location: admin-testimoni.php");
        exit;
    }

    // Upload foto
    $foto = uploadFoto($_FILES['foto']);
    if (!$foto) {
        setError("Gagal mengupload foto");
        header("Location: admin-testimoni.php");
        exit;
    }

    // Insert ke database
    $stmt = $conn->prepare("INSERT INTO testimoni (nama, testimoni, foto, tanggal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $testimoni, $foto, $tanggal);

    if ($stmt->execute()) {
        setSuccess("Testimoni berhasil ditambahkan");
    } else {
        setError("Gagal menambahkan testimoni: " . $conn->error);
        // Hapus foto jika gagal insert ke database
        unlink("assets/testimoni/" . $foto);
    }

    $stmt->close();
    header("Location: admin-testimoni.php");
    exit;
}

function handleUpdate($conn) {
    $id = $_POST['id'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $testimoni = $_POST['testimoni'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';

    // Validasi input
    if (empty($id) || empty($nama) || empty($testimoni) || empty($tanggal)) {
        setError("Semua field harus diisi kecuali foto");
        header("Location: admin-testimoni.php");
        exit;
    }

    // Cek apakah ada file foto baru
    if (!empty($_FILES['foto']['name'])) {
        // Upload foto baru
        $foto = uploadFoto($_FILES['foto']);
        if (!$foto) {
            setError("Gagal mengupload foto");
            header("Location: admin-testimoni.php");
            exit;
        }

        // Hapus foto lama
        $stmt = $conn->prepare("SELECT foto FROM testimoni WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            @unlink("assets/testimoni/" . $row['foto']);
        }
        $stmt->close();

        // Update dengan foto baru
        $stmt = $conn->prepare("UPDATE testimoni SET nama = ?, testimoni = ?, foto = ?, tanggal = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nama, $testimoni, $foto, $tanggal, $id);
    } else {
        // Update tanpa mengubah foto
        $stmt = $conn->prepare("UPDATE testimoni SET nama = ?, testimoni = ?, tanggal = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama, $testimoni, $tanggal, $id);
    }

    if ($stmt->execute()) {
        setSuccess("Testimoni berhasil diperbarui");
    } else {
        setError("Gagal memperbarui testimoni: " . $conn->error);
        if (isset($foto)) {
            // Hapus foto baru jika gagal update
            @unlink("assets/testimoni/" . $foto);
        }
    }

    $stmt->close();
    header("Location: admin-testimoni.php");
    exit;
}

function handleDelete($conn) {
    $id = $_POST['id'] ?? '';

    if (empty($id)) {
        setError("ID testimoni tidak valid");
        header("Location: admin-testimoni.php");
        exit;
    }

    // Ambil nama file foto sebelum menghapus record
    $stmt = $conn->prepare("SELECT foto FROM testimoni WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    // Hapus record dari database
    $stmt = $conn->prepare("DELETE FROM testimoni WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Hapus file foto jika berhasil menghapus record
        if ($row && !empty($row['foto'])) {
            @unlink("assets/testimoni/" . $row['foto']);
        }
        setSuccess("Testimoni berhasil dihapus");
    } else {
        setError("Gagal menghapus testimoni: " . $conn->error);
    }

    $stmt->close();
    header("Location: admin-testimoni.php");
    exit;
}

function uploadFoto($file) {
    $targetDir = "assets/testimoni/";
    
    // Buat direktori jika belum ada
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    // Validasi tipe file
    if (!in_array($fileExtension, $allowedTypes)) {
        setError("Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan");
        return false;
    }

    // Validasi ukuran file (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        setError("Ukuran file maksimal adalah 5MB");
        return false;
    }

    // Generate nama file unik
    $fileName = uniqid() . '.' . $fileExtension;
    $targetFile = $targetDir . $fileName;

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $fileName;
    }

    return false;
}