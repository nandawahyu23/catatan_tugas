<?php
// Konfigurasi database
$host = "localhost";
$dbname = "catatan_tugas_db";
$username = "root"; // default XAMPP
$password = "";     // default XAMPP (kosong)

// Set header default JSON
header("Content-Type: application/json; charset=UTF-8");

// Untuk development: tampilkan error (bisa dimatikan di produksi)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Gagal koneksi database",
        "error" => $e->getMessage()
    ]);
    exit;
}

// Helper: kirim JSON response
function send_json($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}

// Helper: ambil body JSON request
function get_json_input() {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        send_json(["message" => "Body bukan JSON yang valid"], 400);
    }
    return $data;
}

// Helper: ambil token dari header Authorization: Bearer {token}
function get_bearer_token() {
    $headers = [];
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$key] = $value;
            }
        }
    }

    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

// Helper: autentikasi berdasarkan token
function authenticate($pdo) {
    $token = get_bearer_token();
    if (!$token) {
        send_json(["message" => "Token tidak ditemukan. Tambahkan header Authorization: Bearer {token}"], 401);
    }

    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE api_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        send_json(["message" => "Token tidak valid"], 401);
    }

    return $user;
}
?>
