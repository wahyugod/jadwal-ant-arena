<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$conn = getConnection();
$response = ['success' => false, 'message' => '', 'data' => []];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : 'jadwal';
    
    // API untuk jadwal
    if ($action === 'jadwal') {
        $query = "SELECT * FROM jadwal ORDER BY id ASC";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }
            $response['success'] = true;
        } else {
            $response['message'] = 'Tidak ada data jadwal';
        }
    }
    
    // API untuk testimoni
    else if ($action === 'testimoni') {
        $query = "SELECT * FROM testimoni ORDER BY created_at DESC LIMIT 10";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }
            $response['success'] = true;
        } else {
            $response['message'] = 'Tidak ada testimoni';
        }
    }
    
    // API untuk fasilitas
    else if ($action === 'fasilitas') {
        $query = "SELECT * FROM fasilitas ORDER BY created_at DESC LIMIT 10";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $response['data'][] = $row;
            }
            $response['success'] = true;
        } else {
            $response['message'] = 'Tidak ada fasilitas';
        }
    }
}

$conn->close();
echo json_encode($response);
?>
