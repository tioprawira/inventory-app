<?php
include '../../auth/cek_login.php';
include '../../config/database.php';

if (!isset($_GET['id'])) {
    header('Location:index.php');
    exit;
}

$id = (int) $_GET['id'];
$barang_id = (int) $_GET['barang_id'];

$get = $conn->prepare("
    SELECT *
    FROM gambar_produk
    WHERE id = ?
");

$get->execute([$id]);

$data = $get->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header('Location:edit.php?id=' . $barang_id);
    exit;
}

$filePath = '../../assets/img/' . $data['nama_file'];

try {

    $conn->beginTransaction();

    $delete = $conn->prepare("
        DELETE FROM gambar_produk
        WHERE id = ?
    ");

    $delete->execute([$id]);

    // Hapus file fisik
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $conn->commit();

} catch (Exception $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
}

header('Location:edit.php?id=' . $barang_id);
exit;