<?php
require_once __DIR__ . "/config.php";

$method = $_SERVER["REQUEST_METHOD"];
$user = authenticate($pdo); 
$user_id = (int)$user["id"];

// Ambil parameter id tugas dari query string (jika ada)
$tugas_id = isset($_GET["id"]) ? (int)$_GET["id"] : null;

switch ($method) {
    case "GET":
        handle_get($pdo, $user_id, $tugas_id);
        break;
    case "POST":
        handle_post($pdo, $user_id);
        break;
    case "PUT":
        handle_put($pdo, $user_id, $tugas_id);
        break;
    case "DELETE":
        handle_delete($pdo, $user_id, $tugas_id);
        break;
    default:
        send_json(["message" => "Method tidak diizinkan"], 405);
}

// ============= HANDLER FUNCTION =============

function handle_get($pdo, $user_id, $tugas_id) {
    if ($tugas_id) {
        // Detail 1 tugas
        $stmt = $pdo->prepare("SELECT id, mata_kuliah, judul, deskripsi, deadline, status
                               FROM tugas WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$tugas_id, $user_id]);
        $tugas = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tugas) {
            send_json(["message" => "Tugas tidak ditemukan"], 404);
        }
        send_json($tugas);
    } else {
        // List semua tugas milik user
        $stmt = $pdo->prepare("SELECT id, mata_kuliah, judul, deskripsi, deadline, status
                               FROM tugas WHERE user_id = ?
                               ORDER BY deadline ASC, created_at DESC");
        $stmt->execute([$user_id]);
        $tugas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        send_json($tugas);
    }
}

function handle_post($pdo, $user_id) {
    $data = get_json_input();

    $mata_kuliah = isset($data["mata_kuliah"]) ? trim($data["mata_kuliah"]) : "";
    $judul = isset($data["judul"]) ? trim($data["judul"]) : "";
    $deskripsi = isset($data["deskripsi"]) ? trim($data["deskripsi"]) : null;
    $deadline = isset($data["deadline"]) ? trim($data["deadline"]) : null;
    $status = isset($data["status"]) ? trim($data["status"]) : "belum";

    if ($mata_kuliah === "" || $judul === "" || $deadline === null) {
        send_json(["message" => "mata_kuliah, judul, dan deadline wajib diisi"], 400);
    }

    $allowedStatus = ["belum", "proses", "selesai"];
    if (!in_array($status, $allowedStatus)) {
        send_json(["message" => "status harus salah satu dari: belum, proses, selesai"], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO tugas (user_id, mata_kuliah, judul, deskripsi, deadline, status)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $mata_kuliah, $judul, $deskripsi, $deadline, $status]);

    $new_id = $pdo->lastInsertId();

    send_json([
        "message" => "Tugas berhasil dibuat",
        "data" => [
            "id" => (int)$new_id,
            "mata_kuliah" => $mata_kuliah,
            "judul" => $judul,
            "deskripsi" => $deskripsi,
            "deadline" => $deadline,
            "status" => $status
        ]
    ], 201);
}

function handle_put($pdo, $user_id, $tugas_id) {
    if (!$tugas_id) {
        send_json(["message" => "id tugas wajib diisi di query string, contoh: tugas.php?id=3"], 400);
    }

    // Pastikan tugas milik user
    $stmt = $pdo->prepare("SELECT * FROM tugas WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$tugas_id, $user_id]);
    $tugas = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tugas) {
        send_json(["message" => "Tugas tidak ditemukan"], 404);
    }

    $data = get_json_input();

    $mata_kuliah = isset($data["mata_kuliah"]) ? trim($data["mata_kuliah"]) : $tugas["mata_kuliah"];
    $judul = isset($data["judul"]) ? trim($data["judul"]) : $tugas["judul"];
    $deskripsi = array_key_exists("deskripsi", $data) ? trim($data["deskripsi"]) : $tugas["deskripsi"];
    $deadline = isset($data["deadline"]) ? trim($data["deadline"]) : $tugas["deadline"];
    $status = isset($data["status"]) ? trim($data["status"]) : $tugas["status"];

    $allowedStatus = ["belum", "proses", "selesai"];
    if (!in_array($status, $allowedStatus)) {
        send_json(["message" => "status harus salah satu dari: belum, proses, selesai"], 400);
    }

    $stmt = $pdo->prepare("UPDATE tugas
                           SET mata_kuliah = ?, judul = ?, deskripsi = ?, deadline = ?, status = ?
                           WHERE id = ? AND user_id = ?");
    $stmt->execute([$mata_kuliah, $judul, $deskripsi, $deadline, $status, $tugas_id, $user_id]);

    send_json([
        "message" => "Tugas berhasil diupdate",
        "data" => [
            "id" => (int)$tugas_id,
            "mata_kuliah" => $mata_kuliah,
            "judul" => $judul,
            "deskripsi" => $deskripsi,
            "deadline" => $deadline,
            "status" => $status
        ]
    ]);
}

function handle_delete($pdo, $user_id, $tugas_id) {
    if (!$tugas_id) {
        send_json(["message" => "id tugas wajib diisi di query string, contoh: tugas.php?id=3"], 400);
    }

    // Pastikan tugas milik user
    $stmt = $pdo->prepare("SELECT id FROM tugas WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->execute([$tugas_id, $user_id]);
    $tugas = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tugas) {
        send_json(["message" => "Tugas tidak ditemukan"], 404);
    }

    $stmt = $pdo->prepare("DELETE FROM tugas WHERE id = ? AND user_id = ?");
    $stmt->execute([$tugas_id, $user_id]);

    send_json(["message" => "Tugas berhasil dihapus"]);
}
?>
