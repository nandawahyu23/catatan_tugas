<?php
require_once __DIR__ . "/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    send_json(["message" => "Method tidak diizinkan, gunakan POST"], 405);
}

$data = get_json_input();

$name = isset($data["name"]) ? trim($data["name"]) : "";
$email = isset($data["email"]) ? trim($data["email"]) : "";
$password = isset($data["password"]) ? $data["password"] : "";

if ($name === "" || $email === "" || $password === "") {
    send_json(["message" => "name, email, dan password wajib diisi"], 400);
}

// Cek apakah email sudah terdaftar
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    send_json(["message" => "Email sudah terdaftar"], 409);
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Simpan user baru
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $hashedPassword]);

$user_id = $pdo->lastInsertId();

send_json([
    "message" => "Register berhasil",
    "user" => [
        "id" => (int)$user_id,
        "name" => $name,
        "email" => $email
    ]
], 201);
?>
