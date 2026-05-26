<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$error = '';

// Validasi ID
if(!isset($_GET['id']) || $_GET['id'] == '') {

    echo "
    <script>
        alert('ID kategori tidak ditemukan');
        window.location='index.php';
    </script>
    ";
    exit;
}

$id = $_GET['id'];

// Ambil data kategori
$data = $conn->prepare("
    SELECT *
    FROM kategori
    WHERE id=?
");

$data->execute([$id]);

$row = $data->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ada
if(!$row) {

    echo "
    <script>
        alert('Data kategori tidak ditemukan');
        window.location='index.php';
    </script>
    ";
    exit;
}

if(isset($_POST['update'])) {

    $nama = trim($_POST['nama_kategori']);

    // Validasi kosong
    if($nama == '') {

        $error = 'Nama kategori tidak boleh kosong';

    } else {

        // Cek duplikat selain ID sekarang
        $cek = $conn->prepare("
            SELECT id
            FROM kategori
            WHERE nama_kategori = ?
            AND id != ?
        ");

        $cek->execute([$nama, $id]);

        if($cek->rowCount() > 0) {

            $error = 'Nama kategori sudah digunakan';

        } else {

            $sql = "
                UPDATE kategori
                SET nama_kategori=?
                WHERE id=?
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$nama, $id]);

            echo "
            <script>
                alert('Kategori berhasil diupdate');
                window.location='index.php';
            </script>
            ";
            exit;
        }
    }
}
?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-warning">
        <h5 class="mb-0">Edit Kategori</h5>
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
                    required
                    value="<?= htmlspecialchars($row['nama_kategori']); ?>"
                >

            </div>

            <button
                type="submit"
                name="update"
                class="btn btn-success">
                Update
            </button>

            <a href="index.php"
               class="btn btn-secondary">
                Kembali
            </a>

        </form>

    </div>
</div>

<?php include '../../templates/footer.php'; ?>