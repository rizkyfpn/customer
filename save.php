<?php
// Tampilkan semua error (untuk debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Konfigurasi database
$host = "localhost";
$user = "root";
$password = "28@Oktober1999";
$dbname = "warungomahjoglo_db";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    file_put_contents("debug.log", "Koneksi gagal: " . $conn->connect_error . "\n", FILE_APPEND);
    die("Koneksi gagal: " . $conn->connect_error);
}
file_put_contents("debug.log", "Koneksi berhasil!\n", FILE_APPEND);

// Ambil data JSON dari request
$data = json_decode(file_get_contents("php://input"), true);

// Periksa apakah data barcode ada
if (isset($data['barcode']) && !empty($data['barcode'])) {
    $barcode = $data['barcode'];

    // Debug untuk melihat data barcode
    file_put_contents("debug.log", "Received barcode: " . $barcode . PHP_EOL, FILE_APPEND);

    // Pisahkan barcode untuk mendapatkan nama, no hp, dan email
    $parts = explode('|', $barcode);

    // Pastikan barcode memiliki format yang benar
    if (count($parts) === 3) {
        list($name, $phone, $email) = $parts;

        // Validasi data
        if (empty($name) || empty($phone) || empty($email)) {
            echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["success" => false, "message" => "Email tidak valid"]);
            exit;
        }

        // Simpan data ke database
        $stmt = $conn->prepare("INSERT INTO customers (name, phone, email) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $name, $phone, $email);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Data berhasil disimpan"]);
            } else {
                echo json_encode(["success" => false, "message" => "Gagal menyimpan data: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Kesalahan dalam menyiapkan query: " . $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Format barcode tidak valid"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Data barcode tidak ditemukan"]);
}

// Tutup koneksi
$conn->close();
