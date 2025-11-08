<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ant-arena');

// Koneksi ke database
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Session configuration
session_start();

// Fungsi untuk cek login admin
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Fungsi untuk redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: admin-login.php");
        exit();
    }
}

// Flash message helpers
function setError($msg) {
    $_SESSION['error_message'] = $msg;
}

function setSuccess($msg) {
    $_SESSION['success_message'] = $msg;
}
?>