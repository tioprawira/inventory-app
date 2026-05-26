<?php
include '../../auth/cek_login.php';
include '../../config/database.php';

// Response JSON
header('Content-Type: application/json');

// Pastikan request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        'status' => false,
        'message' => 'Method tidak diizinkan'
    ]);

    exit;
}

// Ambil data JSON
$data = json_decode(
    file_get_contents('php://input'),
    true
);

// Validasi data
if (
    !$data ||
    !is_array($data)
) {

    echo json_encode([
        'status' => false,
        'message' => 'Data tidak valid'
    ]);

    exit;
}

try {

    $conn->beginTransaction();

    // Prepare update
    $update = $conn->prepare("
        UPDATE gambar_produk
        SET urutan = ?
        WHERE id = ?
    ");

    foreach ($data as $item) {

        // Validasi item
        if (
            !isset($item['id']) ||
            !isset($item['urutan'])
        ) {
            continue;
        }

        $id      = (int) $item['id'];
        $urutan  = (int) $item['urutan'];

        $update->execute([
            $urutan,
            $id
        ]);
    }

    $conn->commit();

    echo json_encode([
        'status' => true,
        'message' => 'Urutan gambar berhasil diperbarui'
    ]);

} catch (Exception $e) {

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        'status' => false,
        'message' => $e->getMessage()
    ]);
}