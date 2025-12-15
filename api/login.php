<?php
require_once __DIR__ . "/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    send_json(["message" => "Method tidak diizinkan, gunakan POST"], 405);
}

$data = get_json_input();

$email = isset($data["email"]) ? trim($data["email"]) : "";
$password = isset($data["password"]) ? $data["password"] : "";

if ($email === "" || $password === "") {
    send_json(["message" => "email dan password wajib diisi"], 400);
}

// Cari user berdasarkan email
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    send_json(["message" => "Email atau password salah"], 401);
}

// Verifikasi password
if (!password_verify($password, $user["password"])) {
    send_json(["message" => "Email atau password salah"], 401);
}

// Generate token baru (kompatibel berbagai versi PHP)
if (function_exists('random_bytes')) {
    $token = bin2hex(random_bytes(32));
} elseif (function_exists('openssl_random_pseudo_bytes')) {
    $token = bin2hex(openssl_random_pseudo_bytes(32));
} else {
    // fallback sederhana (cukup untuk tugas kuliah)
    $token = bin2hex(uniqid('', true));
}

// Simpan token ke database
$stmt = $pdo->prepare("UPDATE users SET api_token = ? WHERE id = ?");
$stmt->execute([$token, $user["id"]]);

send_json([
    "message" => "Login berhasil",
    "token" => $token,
    "user" => [
        "id" => (int)$user["id"],
        "name" => $user["name"],
        "email" => $user["email"]
    ]
]);

?>
