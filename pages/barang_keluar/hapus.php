<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../auth/csrf.php';

// Pastikan output dikenali sebagai JSON
header('Content-Type: application/json');

// Validasi metode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(["status" => "error", "message" => "Method Not Allowed"]));
}

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$csrf = $_POST['csrf_token'] ?? null;

// Validasi ID dan CSRF
if (!$id || !validate_csrf($csrf)) {
    http_response_code(403);
    exit(json_encode(["status" => "error", "message" => "Invalid CSRF / ID"]));
}

try {
    $conn->beginTransaction();

    // 1. Ambil data transaksi (gunakan FOR UPDATE untuk mencegah race condition)
    $cek_keluar = $conn->prepare("SELECT barang_id, jumlah FROM barang_keluar WHERE id = ? FOR UPDATE");
    $cek_keluar->execute([$id]);
    $transaksi = $cek_keluar->fetch(PDO::FETCH_ASSOC);

    // Jika data tidak ditemukan, batalkan proses
    if (!$transaksi) {
        $conn->rollBack();
        http_response_code(404);
        exit(json_encode(["status" => "error", "message" => "Data transaksi tidak ditemukan"]));
    }

    // 2. Kembalikan stok ke tabel barang
    $restore = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
    $restore->execute([$transaksi['jumlah'], $transaksi['barang_id']]);
    
    // 3. Hapus data transaksinya
    $stmt = $conn->prepare("DELETE FROM barang_keluar WHERE id = ?");
    $stmt->execute([$id]);

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Data dihapus dan stok dikembalikan"]);

} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(500);
    // Catat $e->getMessage() ke file log server Anda jika diperlukan untuk debugging
    echo json_encode(["status" => "error", "message" => "Gagal menghapus data: Terjadi kesalahan sistem"]);
}