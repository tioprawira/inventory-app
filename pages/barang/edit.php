<?php
include '../../auth/cek_login.php';
include '../../config/database.php';

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validasi ID
if (!isset($_GET['id']) || empty($_GET['id'])) {

    $_SESSION['flash_error'] = 'ID barang tidak valid.';
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

// Ambil barang
$data = $conn->prepare("
    SELECT *
    FROM barang
    WHERE id = ?
");

$data->execute([$id]);

$row = $data->fetch(PDO::FETCH_ASSOC);

if (!$row) {

    $_SESSION['flash_error'] = 'Data barang tidak ditemukan.';
    header('Location: index.php');
    exit;
}

// Ambil detail
$getDetail = $conn->prepare("
    SELECT *
    FROM detail
    WHERE barang_id = ?
");

$getDetail->execute([$id]);

$detail = $getDetail->fetch(PDO::FETCH_ASSOC);

// Ambil kategori
$kategori = $conn->query("
    SELECT *
    FROM kategori
    ORDER BY nama_kategori ASC
");

// Ambil gambar
$getImages = $conn->prepare("
    SELECT *
    FROM gambar_produk
    WHERE barang_id = ?
    ORDER BY urutan ASC, id ASC
");

$getImages->execute([$id]);

$images = $getImages->fetchAll(PDO::FETCH_ASSOC);

// Update
if (isset($_POST['update'])) {

    // Validasi CSRF
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {

        $_SESSION['flash_error'] = 'Token keamanan tidak valid.';
        header('Location: index.php');
        exit;
    }

    $kode               = trim($_POST['kode_barang']);
    $nama               = trim($_POST['nama_barang']);
    $kategori_id        = (int) $_POST['kategori_id'];
    $merek              = trim($_POST['merek']);
    $satuan             = trim($_POST['satuan']);
    $lokasi_rak         = trim($_POST['lokasi_rak']);
    $stok               = (int) $_POST['stok'];

    $nomor_surat_jalan = trim($_POST['nomor_surat_jalan']);
    $nomor_po          = trim($_POST['nomor_po']);
    $persamaan_produk  = trim($_POST['persamaan_produk']);

    $minimum_stok = 5;

    if ($stok < 0) {

        $_SESSION['flash_error'] =
            'Stok tidak boleh minus.';

    } else {

        try {

            $conn->beginTransaction();

            // Cek kode barang
            $cek = $conn->prepare("
                SELECT id
                FROM barang
                WHERE kode_barang = ?
                AND id != ?
            ");

            $cek->execute([$kode, $id]);

            if ($cek->rowCount() > 0) {

                throw new Exception(
                    'Kode barang sudah digunakan.'
                );
            }

            // Upload gambar
            if (!empty($_FILES['gambar']['name'][0])) {

                $folderUpload = '../../assets/img/';

                if (!is_dir($folderUpload)) {
                    mkdir($folderUpload, 0777, true);
                }

                foreach ($_FILES['gambar']['tmp_name'] as $key => $tmpName) {

                    if (empty($tmpName)) {
                        continue;
                    }

                    if ($_FILES['gambar']['error'][$key] == 0) {

                        $ext = strtolower(
                            pathinfo(
                                $_FILES['gambar']['name'][$key],
                                PATHINFO_EXTENSION
                            )
                        );

                        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                        if (!in_array($ext, $allowed)) {
                            continue;
                        }

                        // MIME validation
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);

                        $mime = finfo_file($finfo, $tmpName);

                        finfo_close($finfo);

                        $allowedMime = [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ];

                        if (!in_array($mime, $allowedMime)) {
                            continue;
                        }

                        // Max size 2MB
                        $maxSize = 2 * 1024 * 1024;

                        if ($_FILES['gambar']['size'][$key] > $maxSize) {
                            continue;
                        }

                        $originalName = pathinfo(
                            $_FILES['gambar']['name'][$key],
                            PATHINFO_FILENAME
                        );

                        // Bersihkan karakter aneh
                        $originalName = preg_replace(
                            '/[^A-Za-z0-9_-]/',
                            '_',
                            $originalName
                        );

                        $fileName = $originalName . '.' . $ext;

                        if (
                            move_uploaded_file(
                                $tmpName,
                                $folderUpload . $fileName
                            )
                        ) {

                            $insertImage = $conn->prepare("
                                INSERT INTO gambar_produk (
                                    barang_id,
                                    nama_file
                                ) VALUES (?, ?)
                            ");

                            $insertImage->execute([
                                $id,
                                $fileName
                            ]);
                        }
                    }
                }
            }

            // Update barang
            $updateBarang = $conn->prepare("
                UPDATE barang SET
                    kode_barang = ?,
                    nama_barang = ?,
                    kategori_id = ?,
                    merek = ?,
                    satuan = ?,
                    lokasi_rak = ?,
                    stok = ?,
                    minimum_stok = ?
                WHERE id = ?
            ");

            $updateBarang->execute([
                $kode,
                $nama,
                $kategori_id,
                $merek,
                $satuan,
                $lokasi_rak,
                $stok,
                $minimum_stok,
                $id
            ]);

            // Cek detail
            $cekDetail = $conn->prepare("
                SELECT id
                FROM detail
                WHERE barang_id = ?
            ");

            $cekDetail->execute([$id]);

            if ($cekDetail->rowCount() > 0) {

                $updateDetail = $conn->prepare("
                    UPDATE detail SET
                        nomor_surat_jalan = ?,
                        nomor_po = ?,
                        persamaan_produk = ?
                    WHERE barang_id = ?
                ");

                $updateDetail->execute([
                    $nomor_surat_jalan,
                    $nomor_po,
                    $persamaan_produk,
                    $id
                ]);

            } else {

                $insertDetail = $conn->prepare("
                    INSERT INTO detail (
                        barang_id,
                        nomor_surat_jalan,
                        nomor_po,
                        persamaan_produk
                    ) VALUES (?, ?, ?, ?)
                ");

                $insertDetail->execute([
                    $id,
                    $nomor_surat_jalan,
                    $nomor_po,
                    $persamaan_produk
                ]);
            }

            $conn->commit();

            $_SESSION['flash_success'] =
                'Data barang berhasil diperbarui.';

            header("Location: detail.php?id=" . $id);
            exit;

        } catch (Exception $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $_SESSION['flash_error'] =
                $e->getMessage();
        }
    }
}

include '../../templates/header.php';
?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-warning fw-bold">
        Edit Barang
    </div>

    <div class="card-body">

        <?php if (isset($_SESSION['flash_error'])): ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <?= htmlspecialchars($_SESSION['flash_error']) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            <?php unset($_SESSION['flash_error']); ?>

        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= $_SESSION['csrf_token'] ?>"
            >

            <div class="mb-3">
                <label class="form-label">Kode Barang</label>

                <input
                    type="text"
                    name="kode_barang"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($row['kode_barang']) ?>"
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Barang</label>

                <input
                    type="text"
                    name="nama_barang"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($row['nama_barang']) ?>"
                >
            </div>

            <div class="mb-3">

                <label class="form-label">
                    Kategori
                </label>

                <select
                    name="kategori_id"
                    class="form-select"
                    required
                >

                    <?php while($k = $kategori->fetch(PDO::FETCH_ASSOC)): ?>

                        <option
                            value="<?= $k['id'] ?>"
                            <?= ($k['id'] == $row['kategori_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($k['nama_kategori']) ?>
                        </option>

                    <?php endwhile; ?>

                </select>

            </div>

            <div class="mb-3">
                <label class="form-label">Merek</label>

                <input
                    type="text"
                    name="merek"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($row['merek']) ?>"
                >
            </div>

            <div class="mb-3">

                <label class="form-label">
                    Satuan
                </label>

                <select
                    name="satuan"
                    class="form-select"
                    required
                >

                    <?php
                    $satuanList = [
                        'PCS',
                        'BOX',
                        'PACK',
                        'UNIT',
                        'BOTOL',
                        'ROLL'
                    ];

                    foreach ($satuanList as $s):
                    ?>

                        <option
                            value="<?= $s ?>"
                            <?= ($row['satuan'] == $s) ? 'selected' : '' ?>
                        >
                            <?= $s ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-3">
                <label class="form-label">Lokasi Rak</label>

                <input
                    type="text"
                    name="lokasi_rak"
                    class="form-control"
                    required
                    value="<?= htmlspecialchars($row['lokasi_rak']) ?>"
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Stok</label>

                <input
                    type="number"
                    name="stok"
                    class="form-control"
                    min="0"
                    required
                    value="<?= $row['stok'] ?>"
                >
            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Gambar Produk
                </label>

                <?php if($images): ?>

                <div
                    class="d-flex gap-2 flex-wrap mb-3"
                    id="sortable-images"
                >

                    <?php foreach($images as $img): ?>

                        <div 
                            class="image-item position-relative"
                            data-id="<?= $img['id'] ?>"
                        >

                            <img
                                src="../../assets/img/<?= htmlspecialchars($img['nama_file']) ?>"
                                class="img-thumbnail"
                                style="
                                    width:100px;
                                    height:100px;
                                    object-fit:cover;
                                    border-radius:10px;
                                    cursor:grab;
                                "
                            >

                            <a
                                href="hapus_gambar.php?id=<?= $img['id'] ?>&barang_id=<?= $id ?>"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                onclick="return confirm('Hapus gambar ini?')"
                            >
                                ×
                            </a>

                        </div>

                    <?php endforeach; ?>

                </div>

                <?php endif; ?>

                <input
                    type="file"
                    name="gambar[]"
                    class="form-control"
                    accept="image/*"
                    multiple
                >

            </div>

            <div class="mb-3">
                <label class="form-label">
                    Nomor Surat Jalan
                </label>

                <input
                    type="text"
                    name="nomor_surat_jalan"
                    class="form-control"
                    value="<?= htmlspecialchars($detail['nomor_surat_jalan'] ?? '') ?>"
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Nomor PO
                </label>

                <input
                    type="text"
                    name="nomor_po"
                    class="form-control"
                    value="<?= htmlspecialchars($detail['nomor_po'] ?? '') ?>"
                >
            </div>

            <div class="mb-3">

                <label class="form-label">
                    Persamaan Produk
                </label>

                <textarea
                    name="persamaan_produk"
                    class="form-control"
                    rows="3"
                ><?= htmlspecialchars($detail['persamaan_produk'] ?? '') ?></textarea>

            </div>

            <button
                type="submit"
                name="update"
                class="btn btn-success"
            >
                Update
            </button>

            <a
                href="detail.php?id=<?= $row['id'] ?>"
                class="btn btn-secondary"
            >
                ← Kembali
            </a>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>

const sortable = document.getElementById('sortable-images');

if(sortable){

    new Sortable(sortable, {

        animation: 150,

        onEnd: function () {

            let urutan = [];

            document.querySelectorAll('.image-item')
                .forEach((item, index) => {

                urutan.push({
                    id: item.dataset.id,
                    urutan: index
                });
            });

            fetch('update_urutan_gambar.php', {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify(urutan)

            })
            .then(res => res.json())
            .then(data => {

                console.log(data);

            });

        }
    });
}

</script>

<?php include '../../templates/footer.php'; ?>