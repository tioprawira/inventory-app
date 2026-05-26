<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$barang = $conn->query("
    SELECT *
    FROM barang
    ORDER BY nama_barang ASC
");

$kategori = $conn->query("
    SELECT *
    FROM kategori
    ORDER BY nama_kategori ASC
");

if(isset($_POST['simpan'])) {

    try{
        $barang_id = $_POST['barang_id'] ?? null;

        $kode_barang_baru = trim($_POST['kode_barang_baru']);
        $nama_barang_baru = trim($_POST['nama_barang_baru']);

        //Validasi data bentrok
        if (!empty($barang_id) && (!empty($kode_barang_baru) || !empty($nama_barang_baru))) {
        throw new Exception("Pilih barang lama ATAU isi barang baru, tidak boleh keduanya");
        }

        if(!empty($kode_barang_baru) && empty($nama_barang_baru)){
            throw new Exception("Nama barang baru wajib diisi");
        }

        if(empty($kode_barang_baru) && !empty($nama_barang_baru)){
            throw new Exception("Kode barang baru wajib diisi");
        }

        $kategori_id_baru = $_POST['kategori_id_baru'];

        if(!empty($kode_barang_baru) && empty($kategori_id_baru)){
            throw new Exception("Kategori barang wajib dipilih");
        }

        $merek_baru = trim($_POST['merek_baru']);

        if(!empty($kode_barang_baru) && empty($merek_baru)){
            throw new Exception("Merek barang wajib diisi");
        }

        $jumlah = (int) $_POST['jumlah'];

        // ✅ VALIDASI JUMLAH
        if($jumlah <= 0){
            throw new Exception("Jumlah harus lebih dari 0");
        }

        $tanggal = $_POST['tanggal'];

        // ✅ VALIDASI TANGGAL
        if(empty($tanggal)){
            throw new Exception("Tanggal wajib diisi");
        }

        $nomor_po = trim($_POST['nomor_po']);
        $keterangan = trim($_POST['keterangan']);

        $conn->beginTransaction();

        // ===== BARANG BARU =====
        if(!empty($kode_barang_baru) && !empty($nama_barang_baru)) {

            $cek = $conn->prepare("SELECT id FROM barang WHERE kode_barang=?");
            $cek->execute([$kode_barang_baru]);
            $exist = $cek->fetch(PDO::FETCH_ASSOC);

            if($exist){
                throw new Exception("Kode barang sudah terdaftar");
            } else {

                $ins = $conn->prepare("
                    INSERT INTO barang
                    (kode_barang, nama_barang, kategori_id, merek, stok)
                    VALUES (?, ?, ?, ?, 0)
                ");

                $ins->execute([
                    $kode_barang_baru,
                    $nama_barang_baru,
                    $kategori_id_baru,
                    $merek_baru
                ]);

                $barang_id = $conn->lastInsertId();
            }
        }

        if(empty($barang_id)){
            throw new Exception("Barang belum dipilih");
        }

        // ===== INSERT BARANG MASUK =====
        $stmt = $conn->prepare("
            INSERT INTO barang_masuk
            (barang_id, jumlah, tanggal, nomor_po, keterangan)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $barang_id,
            $jumlah,
            $tanggal,
            $nomor_po,
            $keterangan
        ]);

        // ===== UPDATE STOK =====
        $up = $conn->prepare("
            UPDATE barang SET stok = stok + ? WHERE id=?
        ");

        $up->execute([$jumlah, $barang_id]);

        $conn->commit();

        echo "<script>
            alert('Barang masuk berhasil');
            window.location='index.php';
        </script>";

    } catch(Exception $e) {
        if($conn->inTransaction()){
            $conn->rollBack();
        }
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="card shadow-sm">
<div class="card-header bg-success text-white">Tambah Barang Masuk</div>
    <div class="card-body">
        <form method="POST">
            <!-- FORM BARANG BARU -->
            <div class="alert alert-info">
                <strong>Barang Baru?</strong>
                Isi form berikut jika barang belum tersedia.
            </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Kode Barang Baru</label>
                        <input type="text" id="kode_barang_baru" name="kode_barang_baru" class="form-control" autofocus>
                    </div>
                    <div class="col-md-6">
                        <label>Nama Barang Baru</label>
                        <input type="text" id="nama_barang_baru" name="nama_barang_baru" class="form-control">
                    </div>
                </div>
                    <!-- PILIH BARANG -->
                    <div class="mb-3">
                        <label class="form-label">Barang</label>
                        <select name="barang_id" id="barang_lama" class="form-select">
                            <option value="">-- Pilih Barang Lama --</option>
                            <?php while($b = $barang->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $b['id']; ?>">
                                    <?= $b['kode_barang']; ?> - <?= $b['nama_barang']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <!-- ✅ INI LETAK DETAIL BARANG (UI) -->
                    <div class="alert alert-info mt-3" id="infoBarang" style="display:none;">
                        <h6>Detail Barang</h6>

                        <p><strong>Kode:</strong> <span id="kode"></span></p>
                        <p><strong>Nama:</strong> <span id="nama"></span></p>
                        <p><strong>Kategori:</strong> <span id="kategori"></span></p>
                        <p><strong>Merek:</strong> <span id="merek"></span></p>
                        <p><strong>Stok:</strong> <span id="stok"></span></p>
                    </div>
                    <div class="mt-2">
                        <label>Kategori</label>
                        <select name="kategori_id_baru" id="kategori_id_baru" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <?php while($k = $kategori->fetch(PDO::FETCH_ASSOC)): ?>
                                <option value="<?= $k['id']; ?>">
                                    <?= $k['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mt-2">
                        <label>Merek</label>
                        <input type="text" id="merek_baru" name="merek_baru" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Masuk</label>
                        <input type="number" name="jumlah" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label>Nomor PO</label>
                        <input type="text" name="nomor_po" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control"></textarea>
                    </div>
            <button type="submit" name="simpan" class="btn btn-success" onclick="return confirm('Simpan data barang masuk ?')">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>

<script>

const barangLama = document.getElementById('barang_lama');
const kodeBaru = document.getElementById('kode_barang_baru');
const namaBaru = document.getElementById('nama_barang_baru');
const kategori = document.getElementById('kategori_id_baru');
const merek = document.getElementById('merek_baru');

barangLama.addEventListener('change', function () {

    const id = this.value;

    if(id === '') {

        document.getElementById('infoBarang').style.display = 'none';

        kodeBaru.disabled = false;
        namaBaru.disabled = false;
        kategori.disabled = false;
        merek.disabled = false;

        kategori.value = '';
        merek.value = '';

        return;
    }

    fetch('get_barang.php?id=' + id)
        .then(res => res.json())
        .then(res => {

            if(res.status === 'success') {

                kategori.disabled = true;
                merek.disabled = true;

                kodeBaru.value = '';
                namaBaru.value = '';

                const d = res.data;

                // tampil detail
                document.getElementById('kode').innerText = d.kode_barang;
                document.getElementById('nama').innerText = d.nama_barang;
                document.getElementById('kategori').innerText = d.nama_kategori ?? '-';
                document.getElementById('merek').innerText = d.merek ?? '-';
                document.getElementById('stok').innerText = d.stok;

                document.getElementById('infoBarang').style.display = 'block';

                // AUTO FILL FORM
                kategori.value = d.kategori_id;
                merek.value = d.merek;

                // disable barang baru
                kodeBaru.disabled = true;
                namaBaru.disabled = true;

            } else {
                alert('Data barang tidak ditemukan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mengambil data barang');
        });
});

// input barang baru override barang lama
kodeBaru.addEventListener('keyup', reset);
namaBaru.addEventListener('keyup', reset);

function reset() {
    barangLama.value = '';

    document.getElementById('infoBarang').style.display = 'none';

    kodeBaru.disabled = false;
    namaBaru.disabled = false;
    kategori.disabled = false;
    merek.disabled = false;
}

</script>

<?php include '../../templates/footer.php'; ?>