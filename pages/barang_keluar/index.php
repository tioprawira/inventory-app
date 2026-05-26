<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$search = $_GET['search'] ?? '';
$tanggal = $_GET['tanggal'] ?? '';

// base query
$sql = "
    SELECT barang_keluar.*,
           barang.nama_barang,
           barang.kode_barang
    FROM barang_keluar
    JOIN barang ON barang_keluar.barang_id = barang.id
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $sql .= " AND (barang.nama_barang LIKE ? OR barang.kode_barang LIKE ? OR barang_keluar.diminta_oleh LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($tanggal)) {
    $sql .= " AND DATE(barang_keluar.tanggal) = ?";
    $params[] = $tanggal;
}

$sql .= " ORDER BY barang_keluar.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($data);
?>

<?php
// ... [Kode PHP Query Database Anda yang sudah ada di atas tetap biarkan] ...

$total = count($data);
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <strong>Berhasil!</strong> <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <strong>Gagal!</strong> <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-0">Data Barang Keluar</h3>
        <small class="text-muted">Total: <?= $total ?> transaksi</small>
    </div>

    <div class="d-flex gap-2">
        <a href="../../index.php" class="btn btn-outline-secondary btn-sm">← Kembali</a>
        <a href="tambah.php" class="btn btn-primary btn-sm">+ Tambah</a>
    </div>
</div>

<!-- FILTER -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">

            <div class="col-md-5">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Cari barang / kode / peminta..."
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-4">
                <input type="date"
                       name="tanggal"
                       class="form-control"
                       value="<?= htmlspecialchars($tanggal) ?>">
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-dark w-100">Filter</button>
                <a href="index.php" class="btn btn-outline-secondary">Reset</a>
            </div>

        </form>
    </div>
</div>

<!-- TABLE -->
<div class="card shadow-sm">
    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Diminta Oleh</th>
                        <th>Keterangan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody id="tableBody">

                <?php if ($total > 0): ?>
                    <?php $no = 1; foreach($data as $row): ?>
                        <tr id="row-<?= $row['id'] ?>">
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['tanggal']); ?></td>
                            <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    <?= $row['jumlah']; ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['diminta_oleh']); ?></td>
                            <td><?= htmlspecialchars($row['keterangan']); ?></td>
                            <td class="text-center">

                                <a href="edit.php?id=<?= $row['id']; ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <button class="btn btn-danger btn-sm btn-hapus"
                                        data-id="<?= $row['id']; ?>">
                                    Hapus
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            Data tidak ditemukan
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- AJAX DELETE -->
<!-- Pastikan Anda memiliki meta tag ini di bagian <head> HTML Anda -->
<!-- <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>"> -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    
    // === Otomatis hapus notifikasi setelah 5 detik ===
    setTimeout(function() {
        $('.alert').fadeOut(500, function() {
            $(this).remove(); 
        });
    }, 5000); 

    // === Logika Hapus Data ===
    // Gunakan .off() sebelum .on() untuk memastikan klik tidak tereksekusi ganda
    $(document).off('click', '.btn-hapus').on('click', '.btn-hapus', function (e) {
        e.preventDefault(); // Mencegah perilaku default tombol

        let btn = $(this);
        let id = btn.data('id');
        let csrfToken = $('meta[name="csrf-token"]').attr('content'); // Ambil token CSRF

        if (!confirm('Yakin ingin menghapus data ini?')) return;

        // Matikan tombol sementara agar tidak bisa di-klik 2 kali dengan cepat
        btn.prop('disabled', true);

        $.ajax({
            url: 'hapus.php',
            type: 'POST',
            dataType: 'json', // Beritahu AJAX untuk membaca response sebagai JSON
            data: { 
                id: id,
                csrf_token: csrfToken // INI WAJIB DIKIRIM AGAR DITERIMA PHP
            },
            success: function (response) {
                if (response.status === 'success') {
                    // Animasi hapus baris
                    $('#row-' + id).fadeOut(300, function () {
                        $(this).remove();
                    });
                } else {
                    alert(response.message);
                    btn.prop('disabled', false); // Aktifkan tombol lagi jika gagal
                }
            },
            error: function (xhr) {
                // Tampilkan pesan error dari server jika ada (misal: "Invalid CSRF / ID")
                let errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Gagal menghapus data';
                alert(errorMsg);
                btn.prop('disabled', false); // Aktifkan tombol lagi
            }
        });
    });
});
</script>

<?php include '../../templates/footer.php'; ?>