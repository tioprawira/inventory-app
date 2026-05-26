<?php
include '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = (int) $_POST['id'];

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {

        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        $fileName = time() . '_' . uniqid() . '.' . $ext;

        $tmpName = $_FILES['gambar']['tmp_name'];

        $folderUpload = '../../assets/img/';

        // buat folder jika belum ada
        if (!is_dir($folderUpload)) {
            mkdir($folderUpload, 0777, true);
        }

        $uploadPath = $folderUpload . $fileName;

        if (move_uploaded_file($tmpName, $uploadPath)) {

            $cek = $conn->prepare("
                SELECT * 
                FROM detail 
                WHERE barang_id = ?
            ");

            $cek->execute([$id]);

            if ($cek->rowCount() > 0) {

                $update = $conn->prepare("
                    UPDATE detail 
                    SET gambar = ?
                    WHERE barang_id = ?
                ");

                $update->execute([$fileName, $id]);

            } else {

                $insert = $conn->prepare("
                    INSERT INTO detail (
                        barang_id,
                        gambar
                    ) VALUES (?, ?)
                ");

                $insert->execute([$id, $fileName]);
            }
        }
    }

    header("Location: detail.php?id=" . $id);
    exit;
}
?>