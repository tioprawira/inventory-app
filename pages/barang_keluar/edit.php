<?php
// Tampilkan error (Hapus 2 baris ini jika web sudah rilis/production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data transaksi saat ini
$data = $conn->prepare("SELECT * FROM barang_keluar WHERE id=?");
$data->execute([$id]);
$row = $data->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "<div class='alert alert-danger m-3'>Data tidak ditemukan!</div>";
    include '../../templates/footer.php';
    exit;
}

$barang = $conn->query("SELECT * FROM barang ORDER BY nama_barang ASC");

if (isset($_POST['update'])) {
    $barang_id_baru = filter_input(INPUT_POST, 'barang_id', FILTER_SANITIZE_NUMBER_INT);
    $jumlah_baru = filter_input(INPUT_POST, 'jumlah', FILTER_SANITIZE_NUMBER_INT);
    $diminta_oleh = trim($_POST['diminta_oleh']);
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);

    if ($jumlah_baru <= 0) {
        $_SESSION['error'] = "Jumlah tidak valid!";
        header("Location: edit.php?id=" . $id);
        exit;
    }

    try {
        // MULAI TRANSAKSI
        $conn->beginTransaction();

        // 1. Kembalikan stok lama ke barang lama terlebih dahulu
        $kembalikan = $conn->prepare("UPDATE barang SET stok = stok + ? WHERE id=?");
        $kembalikan->execute([$row['jumlah'], $row['barang_id']]);

        // 2. Cek ketersediaan stok barang baru (Gunakan FOR UPDATE)
        $cek = $conn->prepare("SELECT stok FROM barang WHERE id=? FOR UPDATE");
        $cek->execute([$barang_id_baru]);
        $stokBarang = $cek->fetch(PDO::FETCH_ASSOC);

        if (!$stokBarang || $jumlah_baru > $stokBarang['stok']) {
            throw new Exception("Stok tidak mencukupi! Sisa stok: " . ($stokBarang['stok'] ?? 0));
        }

        // 3. Kurangi stok barang dengan jumlah yang baru
        $kurangi = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id=?");
        $kurangi->execute([$jumlah_baru, $barang_id_baru]);

        // 4. Update tabel transaksi barang_keluar
        $update = $conn->prepare("
            UPDATE barang_keluar
            SET barang_id=?, jumlah=?, diminta_oleh=?, tanggal=?, keterangan=?
            WHERE id=?
        ");
        $update->execute([$barang_id_baru, $jumlah_baru, $diminta_oleh, $tanggal, $keterangan, $id]);

        // 5. COMMIT TRANSAKSI (Simpan permanen)
        $conn->commit();

        $_SESSION['success'] = "Data barang keluar berhasil diupdate.";
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        // BATALKAN JIKA ADA ERROR
        $conn->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header("Location: edit.php?id=" . $id);
        exit;
    }
}
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <strong>Gagal!</strong> <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        Edit Barang Keluar
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label>Barang</label>
                <select name="barang_id" class="form-select" required>
                    <?php while($b = $barang->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $b['id']; ?>" <?= ($b['id'] == $row['barang_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($b['kode_barang']); ?> - <?= htmlspecialchars($b['nama_barang']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Jumlah Keluar</label>
                <input type="number" name="jumlah" value="<?= $row['jumlah']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Diminta Oleh</label>
                <input type="text" name="diminta_oleh" value="<?= htmlspecialchars($row['diminta_oleh']); ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= $row['tanggal']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control"><?= htmlspecialchars($row['keterangan']); ?></textarea>
            </div>

            <button type="submit" name="update" class="btn btn-success">Update</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script>
// Versi Vanilla JS (Tanpa perlu bergantung pada jQuery)
setTimeout(function() {
    let alertElement = document.querySelector('.alert');
    if (alertElement) {
        // Menggunakan library bootstrap bawaan untuk menutup alert dengan halus
        let bsAlert = new bootstrap.Alert(alertElement);
        bsAlert.close();
    }
}, 5000);
</script>

<?php include '../../templates/footer.php'; ?>