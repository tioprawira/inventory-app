<?php
include '../../auth/cek_login.php';
include '../../config/database.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// 1. Validasi request method
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $_SESSION['flash_error'] = 'Akses tidak valid.';
    header('Location: index.php');
    exit;
}

// 2. Validasi CSRF token
if(
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
){
    $_SESSION['flash_error'] = 'Aksi tidak diizinkan (Token keamanan tidak valid).';
    header('Location: index.php');
    exit;
}

// 3. Validasi ID Barang
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if($id <= 0){
    $_SESSION['flash_error'] = 'ID barang tidak valid.';
    header('Location: index.php');
    exit;
}

try {
    // 4. Cek Integritas Data (Barang Masuk)
    $cekMasuk = $conn->prepare("SELECT COUNT(*) FROM barang_masuk WHERE barang_id = ?");
    $cekMasuk->execute([$id]);

    if($cekMasuk->fetchColumn() > 0){
        $_SESSION['flash_error'] = 'Gagal dihapus: Barang masih memiliki data transaksi masuk.';
        header('Location: index.php');
        exit;
    }
    
    // Opsional: Tambahkan cek t_barang_keluar jika ada tabelnya
    /*
    $cekKeluar = $conn->prepare("SELECT COUNT(*) FROM barang_keluar WHERE barang_id = ?");
    $cekKeluar->execute([$id]);
    if($cekKeluar->fetchColumn() > 0){
        $_SESSION['flash_error'] = 'Gagal dihapus: Barang masih memiliki data transaksi keluar.';
        header('Location: index.php');
        exit;
    }
    */

    // 5. Eksekusi Hapus Data
    $stmt = $conn->prepare("DELETE FROM barang WHERE id = ?");
    $stmt->execute([$id]);

    // Notifikasi sukses
    $_SESSION['flash_success'] = 'Data barang berhasil dihapus!';
    header('Location: index.php');
    exit;

} catch(PDOException $e) {
    // Catat ke log internal server jika diperlukan: error_log($e->getMessage());
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem, gagal menghapus data.';
    header('Location: index.php');
    exit;
}
?>