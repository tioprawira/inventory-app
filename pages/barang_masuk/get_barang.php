<?php
include '../../config/database.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if(!$id){
    echo json_encode(['status' => 'error', 'message' => 'ID kosong']);
    exit;
}

$stmt = $conn->prepare("
    SELECT b.*, k.nama_kategori
    FROM barang b
    LEFT JOIN kategori k ON k.id = b.kategori_id
    WHERE b.id = ?
");

$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if($data){
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
}