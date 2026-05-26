<?php
include '../../auth/cek_login.php';
include '../../config/database.php';

$id = $_GET['id'];

$data = $conn->prepare("
    SELECT *
    FROM barang_masuk
    WHERE id=?
");

$data->execute([$id]);

$row = $data->fetch(PDO::FETCH_ASSOC);

// Kurangi stok kembali
$update = $conn->prepare("
    UPDATE barang
    SET stok = stok - ?
    WHERE id=?
");

$update->execute([
    $row['jumlah'],
    $row['barang_id']
]);

$hapus = $conn->prepare("
    DELETE FROM barang_masuk
    WHERE id=?
");

$hapus->execute([$id]);

header('Location: index.php');
?>