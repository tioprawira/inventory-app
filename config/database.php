<?php

$host = 'localhost';
$db   = 'inventory_db';
$user = 'root';
$pass = '';

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    // Aktifkan mode error exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Default fetch associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Disable emulate prepare untuk keamanan
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {

    // Untuk production lebih aman jangan tampilkan detail error
    die("Koneksi database gagal");

    // Untuk development:
    // die("Koneksi Gagal: " . $e->getMessage());

}
?>