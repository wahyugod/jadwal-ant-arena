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

    if (empty($nama) || empty($deskripsi) || empty($_FILES['foto']['name'])) {
        setError("Semua field harus diisi");
        header("Location: admin-fasilitas.php");
        exit;
    }

    // Upload foto
    $foto = uploadFoto($_FILES['foto']);
    if (!$foto) {
        header("Location: admin-fasilitas.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO fasilitas (nama, deskripsi, foto) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $deskripsi, $foto);

    if ($stmt->execute()) {
        setSuccess("Fasilitas berhasil ditambahkan");
    } else {
        setError("Gagal menambahkan fasilitas: " . $conn->error);
        // Hapus foto jika gagal insert ke database
        unlink("assets/fasilitas/" . $foto);
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
        setError("Semua field harus diisi kecuali foto");
        header("Location: admin-fasilitas.php");
        exit;
    }

    // Cek apakah ada file foto baru
    if (!empty($_FILES['foto']['name'])) {
        // Upload foto baru
        $foto = uploadFoto($_FILES['foto']);
        if (!$foto) {
            header("Location: admin-fasilitas.php");
            exit;
        }

        // Hapus foto lama
        $stmt = $conn->prepare("SELECT foto FROM fasilitas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            @unlink("assets/fasilitas/" . $row['foto']);
        }
        $stmt->close();

        // Update dengan foto baru
        $stmt = $conn->prepare("UPDATE fasilitas SET nama = ?, deskripsi = ?, foto = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama, $deskripsi, $foto, $id);
    } else {
        // Update tanpa mengubah foto
        $stmt = $conn->prepare("UPDATE fasilitas SET nama = ?, deskripsi = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nama, $deskripsi, $id);
    }

    if ($stmt->execute()) {
        setSuccess("Fasilitas berhasil diperbarui");
    } else {
        setError("Gagal memperbarui fasilitas: " . $conn->error);
        if (isset($foto)) {
            @unlink("assets/fasilitas/" . $foto);
        }
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

    // Ambil nama file foto sebelum menghapus record
    $stmt = $conn->prepare("SELECT foto FROM fasilitas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    // Hapus record dari database
    $stmt = $conn->prepare("DELETE FROM fasilitas WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Hapus file foto jika berhasil menghapus record
        if ($row && !empty($row['foto'])) {
            @unlink("assets/fasilitas/" . $row['foto']);
        }
        setSuccess("Fasilitas berhasil dihapus");
    } else {
        setError("Gagal menghapus fasilitas: " . $conn->error);
    }

    $stmt->close();
    header("Location: admin-fasilitas.php");
    exit;
}

function uploadFoto($file) {
    $targetDir = "assets/fasilitas/";
    
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

    setError("Gagal mengupload file");
    return false;
}
