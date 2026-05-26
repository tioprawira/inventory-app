<?php
// Tampilkan error (Hapus 2 baris ini jika web sudah rilis/production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$barang = $conn->query("SELECT * FROM barang ORDER BY nama_barang ASC");

if (isset($_POST['simpan'])) {
    // Validasi input dasar
    $barang_id = filter_input(INPUT_POST, 'barang_id', FILTER_SANITIZE_NUMBER_INT);
    $jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_SANITIZE_NUMBER_INT);
    $diminta = trim($_POST['diminta_oleh']);
    $tanggal = $_POST['tanggal'];
    $keterangan = trim($_POST['keterangan']);

    if ($jumlah <= 0) {
        $_SESSION['error'] = "Jumlah tidak valid!";
        header("Location: tambah.php");
        exit;
    }

    try {
        // 1. MULAI TRANSAKSI
        $conn->beginTransaction();

        // 2. Cek Stok (Gunakan FOR UPDATE untuk mencegah Race Condition)
        $cek = $conn->prepare("SELECT stok FROM barang WHERE id=? FOR UPDATE");
        $cek->execute([$barang_id]);
        $dataBarang = $cek->fetch(PDO::FETCH_ASSOC);

        if (!$dataBarang || $jumlah > $dataBarang['stok']) {
            throw new Exception("Stok tidak mencukupi! Sisa stok: " . ($dataBarang['stok'] ?? 0));
        }

        // 3. Insert ke barang_keluar
        $stmt = $conn->prepare("INSERT INTO barang_keluar (barang_id, jumlah, diminta_oleh, tanggal, keterangan) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$barang_id, $jumlah, $diminta, $tanggal, $keterangan]);

        // 4. Kurangi stok barang
        $updateStok = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id=?");
        $updateStok->execute([$jumlah, $barang_id]);

        // 5. COMMIT TRANSAKSI (Simpan permanen jika semua sukses)
        $conn->commit();

        $_SESSION['success'] = "Barang keluar berhasil disimpan.";
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        // BATALKAN SEMUA PERUBAHAN JIKA ADA YANG GAGAL
        $conn->rollBack();
        $_SESSION['error'] = $e->getMessage();
        header("Location: tambah.php");
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

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <strong>Berhasil!</strong> <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
        Tambah Barang Keluar
    </div>

    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label>Barang</label>
                <select name="barang_id" class="form-select" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php while($b = $barang->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?= $b['id']; ?>">
                            <?= htmlspecialchars($b['kode_barang']); ?> - <?= htmlspecialchars($b['nama_barang']); ?> (Stok: <?= $b['stok']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Jumlah Keluar</label>
                <input type="number" name="jumlah" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Diminta Oleh</label>
                <input type="text" name="diminta_oleh" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control"></textarea>
            </div>

            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
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