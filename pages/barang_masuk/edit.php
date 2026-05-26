<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$id = $_GET['id'];

$data = $conn->prepare("
    SELECT barang_masuk.*,
       barang.nama_barang,
       barang.kode_barang,
       barang.merek,
       kategori.nama_kategori
    FROM barang_masuk
    JOIN barang
        ON barang_masuk.barang_id = barang.id
    LEFT JOIN kategori
        ON barang.kategori_id = kategori.id
    WHERE barang_masuk.id=?
");

$data->execute([$id]);

$row = $data->fetch(PDO::FETCH_ASSOC);

if(!$row) {

    echo "
    <script>
        alert('Data tidak ditemukan');
        window.location='index.php';
    </script>
    ";

    exit;
}

if(isset($_POST['update'])) {

    $jumlah_baru = (int) $_POST['jumlah'];
    if ($jumlah_baru <= 0) {
        die('Jumlah Tidak Valid');
    }
    $tanggal = $_POST['tanggal'];
    $nomor_po = $_POST['nomor_po'];
    $keterangan = $_POST['keterangan'];

    // kembalikan stok lama

    $kembali = $conn->prepare("
        UPDATE barang
        SET stok = stok - ?
        WHERE id=?
    ");

    $kembali->execute([
        $row['jumlah'],
        $row['barang_id']
    ]);

    // tambah stok baru

    $tambah = $conn->prepare("
        UPDATE barang
        SET stok = stok + ?
        WHERE id=?
    ");

    $tambah->execute([
        $jumlah_baru,
        $row['barang_id']
    ]);

    // update transaksi

    $update = $conn->prepare("
        UPDATE barang_masuk
        SET jumlah=?,
            tanggal=?,
            nomor_po=?,
            keterangan=?
        WHERE id=?
    ");

    $update->execute([
        $jumlah_baru,
        $tanggal,
        $nomor_po,
        $keterangan,
        $id
    ]);

    echo "
    <script>
        alert('Data berhasil diupdate');
        window.location='index.php';
    </script>
    ";
}
?>

<div class="card border-0 shadow rounded-4">

    <div class="card-header bg-warning text-dark">

        <h5 class="mb-0">

            <i class="bi bi-pencil-square"></i>

            Edit Barang Masuk

        </h5>

    </div>

    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">
                    Barang
                </label>
                <input type="text"
                       class="form-control"
                       value="<?= $row['kode_barang']; ?> - <?= $row['nama_barang']; ?>"
                       readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Kategori
                </label>
                <input type="text"
                       class="form-control"
                       value="<?= $row['nama_kategori']; ?>"
                       readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Merek
                </label>
                <input type="text"
                       class="form-control"
                       value="<?= $row['merek']; ?>"
                       readonly>
            </div>

            <div class="mb-3">

                <label class="form-label">
                    Jumlah Masuk
                </label>

                <input type="number"
                       name="jumlah"
                       class="form-control"
                       value="<?= $row['jumlah']; ?>"
                       min="1"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Nomor PO
                </label>

                <input type="text"
                       name="nomor_po"
                       class="form-control"
                       value="<?= $row['nomor_po']; ?>">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Tanggal
                </label>

                <input type="date"
                       name="tanggal"
                       class="form-control"
                       value="<?= $row['tanggal']; ?>"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Keterangan
                </label>

                <textarea name="keterangan"
                          class="form-control"
                          rows="3"><?= $row['keterangan']; ?></textarea>

            </div>

            <div class="d-flex gap-2">

                <button type="submit"
                        name="update"
                        class="btn btn-warning">

                    <i class="bi bi-check-circle"></i>

                    Update

                </button>

                <a href="index.php"
                   class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>

                    Kembali

                </a>

            </div>

        </form>

    </div>

</div>

<?php include '../../templates/footer.php'; ?>