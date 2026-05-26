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

// Ambil kategori
$kategori = $conn->query("
    SELECT *
    FROM kategori
    ORDER BY nama_kategori ASC
");

// Simpan data
if (isset($_POST['simpan'])) {

    // Validasi CSRF
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {

        $_SESSION['flash_error'] =
            'Token keamanan tidak valid.';

        header('Location: index.php');
        exit;
    }

    // Ambil input
    $kode               = trim($_POST['kode_barang']);
    $nama               = trim($_POST['nama_barang']);
    $kategori_id        = (int) $_POST['kategori_id'];
    $merek              = trim($_POST['merek']);
    $satuan             = trim($_POST['satuan']);
    $lokasi_rak         = trim($_POST['lokasi_rak']);
    $stok               = (int) $_POST['stok'];

    $nomor_surat_jalan  = trim($_POST['nomor_surat_jalan'] ?? '');
    $nomor_po           = trim($_POST['nomor_po'] ?? '');
    $persamaan_produk   = trim($_POST['persamaan_produk'] ?? '');

    $minimum_stok = 5;

    // Validasi
    if (
        empty($kode) ||
        empty($nama) ||
        empty($kategori_id) ||
        empty($merek) ||
        empty($satuan) ||
        empty($lokasi_rak)
    ) {

        $_SESSION['flash_error'] =
            'Semua field wajib diisi.';

    } elseif ($stok < 0) {

        $_SESSION['flash_error'] =
            'Stok tidak boleh minus.';

    } else {

        try {

            $conn->beginTransaction();

            // Cek kode barang
            $cek = $conn->prepare("
                SELECT *
                FROM barang
                WHERE kode_barang = ?
                FOR UPDATE
            ");

            $cek->execute([$kode]);

            $data = $cek->fetch(PDO::FETCH_ASSOC);

            // Jika barang sudah ada
            if ($data) {

                $update = $conn->prepare("
                    UPDATE barang
                    SET stok = stok + ?
                    WHERE kode_barang = ?
                ");

                $update->execute([
                    $stok,
                    $kode
                ]);

                $barang_id = $data['id'];

                $_SESSION['flash_success'] =
                    'Stok barang berhasil ditambahkan.';

            } else {

                // Insert barang baru
                $insert = $conn->prepare("
                    INSERT INTO barang
                    (
                        kode_barang,
                        nama_barang,
                        kategori_id,
                        merek,
                        satuan,
                        lokasi_rak,
                        stok,
                        minimum_stok
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $insert->execute([
                    $kode,
                    $nama,
                    $kategori_id,
                    $merek,
                    $satuan,
                    $lokasi_rak,
                    $stok,
                    $minimum_stok
                ]);

                $barang_id = $conn->lastInsertId();

                // Insert detail
                $insertDetail = $conn->prepare("
                    INSERT INTO detail
                    (
                        barang_id,
                        nomor_surat_jalan,
                        nomor_po,
                        persamaan_produk
                    )
                    VALUES (?, ?, ?, ?)
                ");

                $insertDetail->execute([
                    $barang_id,
                    $nomor_surat_jalan,
                    $nomor_po,
                    $persamaan_produk
                ]);

                $_SESSION['flash_success'] =
                    'Barang baru berhasil ditambahkan.';
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

                        $allowed = [
                            'jpg',
                            'jpeg',
                            'png',
                            'webp'
                        ];

                        if (!in_array($ext, $allowed)) {
                            continue;
                        }

                        // Validasi MIME
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

                        // Max 2MB
                        $maxSize = 2 * 1024 * 1024;

                        if ($_FILES['gambar']['size'][$key] > $maxSize) {
                            continue;
                        }

                        // Bersihkan nama file
                        $originalName = pathinfo(
                            $_FILES['gambar']['name'][$key],
                            PATHINFO_FILENAME
                        );

                        $originalName = preg_replace(
                            '/[^A-Za-z0-9_-]/',
                            '_',
                            $originalName
                        );

                        // Nama file unik
                        $fileName =
                            time() .
                            '_' .
                            uniqid() .
                            '_' .
                            $originalName .
                            '.' .
                            $ext;

                        // Upload file
                        if (
                            move_uploaded_file(
                                $tmpName,
                                $folderUpload . $fileName
                            )
                        ) {

                            // Simpan database
                            $insertImage = $conn->prepare("
                                INSERT INTO gambar_produk
                                (
                                    barang_id,
                                    nama_file
                                )
                                VALUES (?, ?)
                            ");

                            $insertImage->execute([
                                $barang_id,
                                $fileName
                            ]);
                        }
                    }
                }
            }

            $conn->commit();

            header('Location: index.php');
            exit;

        } catch (Exception $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $_SESSION['flash_error'] =
                'Gagal menyimpan data : ' .
                $e->getMessage();
        }
    }
}

include '../../templates/header.php';
?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-primary text-white fw-bold">
        Tambah Barang
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

                <label class="form-label">
                    Kode Barang
                </label>

                <input
                    type="text"
                    name="kode_barang"
                    class="form-control"
                    required
                    autofocus
                >

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Nama Barang
                </label>

                <input
                    type="text"
                    name="nama_barang"
                    class="form-control"
                    required
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

                    <option value="">
                        -- Pilih Kategori --
                    </option>

                    <?php while($k = $kategori->fetch(PDO::FETCH_ASSOC)): ?>

                        <option value="<?= $k['id']; ?>">

                            <?= htmlspecialchars($k['nama_kategori']); ?>

                        </option>

                    <?php endwhile; ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Merek
                </label>

                <input
                    type="text"
                    name="merek"
                    class="form-control"
                    required
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

                    <option value="">
                        -- Pilih Satuan --
                    </option>

                    <option value="PCS">PCS</option>
                    <option value="BOX">BOX</option>
                    <option value="PACK">PACK</option>
                    <option value="UNIT">UNIT</option>
                    <option value="BOTOL">BOTOL</option>
                    <option value="ROLL">ROLL</option>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Lokasi Rak
                </label>

                <input
                    type="text"
                    name="lokasi_rak"
                    class="form-control"
                    placeholder="Contoh : Rak A1"
                    required
                >

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Stok
                </label>

                <input
                    type="number"
                    name="stok"
                    class="form-control"
                    min="0"
                    required
                >

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Gambar Produk
                </label>

                <input
                    type="file"
                    name="gambar[]"
                    class="form-control"
                    accept="image/*"
                    multiple
                >

                <small class="text-muted">
                    Format: JPG, JPEG, PNG, WEBP (Max 2MB)
                </small>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Nomor Surat Jalan
                </label>

                <input
                    type="text"
                    name="nomor_surat_jalan"
                    class="form-control"
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
                ></textarea>

            </div>

            <button
                type="submit"
                name="simpan"
                class="btn btn-success"
            >
                Simpan
            </button>

            <a
                href="index.php"
                class="btn btn-secondary"
            >
                ← Kembali
            </a>

        </form>

    </div>

</div>

<script>

// Auto close alert
setTimeout(function () {

    let alerts = document.querySelectorAll('.alert');

    alerts.forEach(function(alert) {

        alert.classList.remove('show');

        setTimeout(() => {
            alert.remove();
        }, 150);

    });

}, 5000);

</script>

<?php include '../../templates/footer.php'; ?>