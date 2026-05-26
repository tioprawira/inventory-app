<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$error = '';

if(isset($_POST['simpan'])) {

    $nama = trim($_POST['nama_kategori']);

    // Validasi kosong
    if($nama == '') {

        $error = 'Nama kategori tidak boleh kosong';

    } else {

        // Cek kategori sudah ada atau belum
        $cek = $conn->prepare("
            SELECT id
            FROM kategori
            WHERE nama_kategori = ?
        ");

        $cek->execute([$nama]);

        if($cek->rowCount() > 0) {

            $error = 'Kategori sudah tersedia';

        } else {

            $sql = "
                INSERT INTO kategori (nama_kategori)
                VALUES (?)
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$nama]);

            echo "
            <script>
                alert('Kategori berhasil ditambahkan');
                window.location='index.php';
            </script>
            ";
            exit;
        }
    }
}
?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Tambah Kategori</h5>
    </div>

    <div class="card-body">

        <?php if($error != ''): ?>
            <div class="alert alert-danger">
                <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Nama Kategori
                </label>

                <input
                    type="text"
                    name="nama_kategori"
                    class="form-control"
                    maxlength="100"
                    autocomplete="off"
                    required
                    value="<?= isset($_POST['nama_kategori'])
                        ? htmlspecialchars($_POST['nama_kategori'])
                        : ''; ?>"
                >

            </div>

            <button
                type="submit"
                name="simpan"
                class="btn btn-success">
                Simpan
            </button>

            <a href="index.php"
               class="btn btn-secondary">
                Kembali
            </a>

        </form>

    </div>
</div>

<?php include '../../templates/footer.php'; ?>