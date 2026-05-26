<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

// Agar semua query parameter dikelola otomatis.
function buildQuery(array $params = []): string
{
    return http_build_query(array_merge($_GET, $params));
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ==========================================
// PENGATURAN PAGINATION & SEARCH
// ==========================================
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// ==========================================
// SEARCH
// ==========================================

$search = trim($_GET['search'] ?? '');

$keywords = !empty($search)
    ? preg_split('/\s+/', $search)
    : [];

$searchConditions = [];
$params = [];

foreach ($keywords as $index => $word) {

    $key = ":search{$index}";

    $searchConditions[] = "
        (
            b.nama_barang LIKE {$key}
            OR b.kode_barang LIKE {$key}
            OR b.merek LIKE {$key}
        )
    ";

    $params[$key] = "%{$word}%";
}

$searchQuery = '';

if (!empty($searchConditions)) {
    $searchQuery = 'WHERE ' . implode(' OR ', $searchConditions);
}

// ==========================================
// PENGATURAN SORTING
// ==========================================

$allowedSortColumns = [
    'kode'     => 'b.kode_barang',
    'nama'     => 'b.nama_barang',
    'kategori' => 'k.nama_kategori',
    'merek'    => 'b.merek',
    'satuan'   => 'b.satuan',
    'lokasi'   => 'b.lokasi_rak',
    'stok'     => 'b.stok',
];

// default sorting
$sortKey = $_GET['sort'] ?? 'kode';

if ($sortKey === 'terbaru') {

    $sortColumn = 'b.id';
    $sortDir = 'DESC';

} else {

    $sortColumn = $allowedSortColumns[$sortKey] ?? 'b.id';

    $sortDir = (
        isset($_GET['dir']) &&
        strtoupper($_GET['dir']) === 'ASC'
    ) ? 'ASC' : 'DESC';
}

// COUNT TOTAL
$countSql = "
    SELECT COUNT(b.id)
    FROM barang b
    LEFT JOIN kategori k ON b.kategori_id = k.id
    $searchQuery
";

$stmtCount = $conn->prepare($countSql);

if (!$stmtCount) {
    die("Query COUNT gagal diproses");
}

foreach ($params as $key => $val) {
    $stmtCount->bindValue($key, $val);
}

$stmtCount->execute();

$totalBarang = $stmtCount->fetchColumn();

// Hitung total halaman
$totalPages = max(1, ceil($totalBarang / $limit));

// Cegah page melebihi total
if ($page > $totalPages) {
    $page = $totalPages;
}

// Update offset jika page berubah
$offset = ($page - 1) * $limit;

// QUERY DATA
$sql = "
    SELECT b.*, k.nama_kategori
    FROM barang b
    LEFT JOIN kategori k ON b.kategori_id = k.id
    $searchQuery
    ORDER BY $sortColumn $sortDir
    LIMIT :limit OFFSET :offset
";

try {

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Prepare query gagal");
    }

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

} catch (Exception $e) {

    die("Terjadi kesalahan pada sistem. Silakan coba lagi nanti.");

}

$dataBarang = $stmt->fetchAll();
?>

<style>
    /* Mengambil font Inter dari Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    /* Terapkan font Inter ke seluruh komponen tabel dan teks */
    body, .table, .form-control, .btn, .badge {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
    }

    .card-body {
        padding: 20px;
    }

    /* PERBAIKAN: Menghilangkan scroll vertikal, hanya aktifkan scroll horizontal jika layar sempit */
    .table-responsive {
        overflow-x: auto;
        border-radius: 12px;
    }

    .table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
        background: #fff;
        font-size: 14px;
    }

    /* Mengoptimalkan tampilan angka stok & kode agar sejajar secara vertikal */
    .table th, .table td {
        font-variant-numeric: tabular-nums;
    }

    .table thead th {
        background: #1e293b !important;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        padding: 14px 10px;
        border: none;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .table thead th a {
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .table thead th a:hover {
        color: #38bdf8;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.001);
    }

    .table td {
        vertical-align: middle;
        padding: 12px 10px;
        border-bottom: 1px solid #e2e8f0;
        color: #334155;
        font-size: 13px;
    }

    .table td.text-center {
        text-align: center;
    }

    .table td:nth-child(3) {
        min-width: 220px;
    }

    .badge {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .aksi-group {
        display: flex;
        gap: 6px;
        justify-content: center;
        /* Menghilangkan flex-wrap agar tidak turun ke bawah */
        flex-wrap: nowrap; 
    }

    .aksi-group .btn {
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 10px;
        transition: 0.2s ease;
    }

    .aksi-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
    }

    /* Memastikan kolom aksi memiliki ruang yang cukup untuk kedua tombol */
    .table th:last-child, 
    .table td:last-child {
        width: 160px; /* Atur lebar ideal kotak aksi */
        white-space: nowrap;
    }

    .btn-primary {
        border-radius: 10px;
        font-weight: 600;
    }

    .export-btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 14px;
    }

    .pagination .page-link {
        border: none;
        margin: 0 3px;
        border-radius: 8px;
        color: #334155;
        font-weight: 500;
        transition: 0.2s;
    }

    .pagination .page-link:hover {
        background: #0f172a;
        color: #fff;
    }

    .pagination .active .page-link {
        background: #0f172a;
        color: #fff;
    }

    .form-control {
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #0ea5e9;
    }

    .btn {
        transition: 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .table {
            font-size: 12px;
        }

        .table td, .table th {
            padding: 10px 8px;
        }

        /*.aksi-group {
            flex-direction: column;
        }

        .aksi-group .btn {
            width: 100%;
        } */

        .export-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Data Stok Barang</h2>
    <a href="tambah.php" class="btn btn-primary">
        + Tambah Barang
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <!-- NOTIFIKASI -->
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['flash_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['flash_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        
        <!-- FORM PENCARIAN (SERVER-SIDE) -->
        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text"
                    id="searchInput"
                   name="search" 
                   class="form-control" 
                   placeholder="🔍 Cari kode atau nama barang..."
                   value="<?= htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-secondary px-4">Cari</button>
            <?php if (!empty($search)): ?>
                <a href="index.php" class="btn btn-outline-danger">Reset</a>
            <?php endif; ?>
        </form>

        <p class="text-muted">
            Total Barang: <strong><?= $totalBarang; ?></strong> 
            (Menampilkan halaman <?= $page; ?> dari <?= $totalPages > 0 ? $totalPages : 1; ?>)
        </p>

        <div class="d-flex gap-2 mb-3">
            <a href="export_excel.php?search=<?= urlencode($search) ?>&sort=<?= urlencode($sortKey) ?>&dir=<?= urlencode($sortDir) ?>" class="btn btn-success btn-sm export-btn d-flex align-items-center gap-2 px-3">
                📊 Excel
            </a>
            <a href="export_pdf.php?search=<?= urlencode($search) ?>&sort=<?= urlencode($sortKey) ?>&dir=<?= urlencode($sortDir) ?>" class="btn btn-danger btn-sm export-btn d-flex align-items-center gap-2 px-3">
                📄 PDF
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <?php
                        // Daftar header yang bisa di-klik
                        $headers = [
                            'kode' => 'Kode',
                            'nama' => 'Nama Barang',
                            'kategori' => 'Kategori',
                            'merek' => 'Merek',
                            'satuan' => 'Satuan',
                            'lokasi' => 'Lokasi Rak',
                            'stok' => 'Stok',
                        ];

                        foreach ($headers as $key => $label): 
                            // Tentukan arah kebalikan untuk klik selanjutnya
                            $nextDir = ($sortKey === $key && $sortDir === 'ASC') ? 'DESC' : 'ASC';
                            
                            // Tentukan ikon panah
                            $icon = '<span></span>';
                            if ($sortKey === $key) {
                                $icon = $sortDir === 'ASC' ? '<span class="text-warning">▲</span>' : '<span class="text-warning">▼</span>';
                            }
                            
                            // Bangun URL agar search dan page tetap terbawa
                            $sortUrl = "?page={$page}&search=" . urlencode($search) . "&sort={$key}&dir={$nextDir}";
                        ?>
                            <th>
                                <a href="<?= $sortUrl ?>">
                                    <?= $label ?> <?= $icon ?>
                                </a>
                            </th>
                        <?php endforeach; ?>
                        <th>Status</th>
                        <th class="text-nowrap text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Penomoran disesuaikan dengan offset agar tidak mengulang dari 1 di tiap halaman
                    $no = $offset + 1; 
                    ?>
                    
                    <?php if (empty($dataBarang)): ?>
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">
                                <div class="py-4 text-center text-muted">
                                    <div class="display-1 opacity-50">📦</div>
                                    <h5 class="mt-2">
                                        Data barang tidak ditemukan
                                    </h5>

                                    <?php if($search): ?>
                                        <p class="small">
                                            Kata kunci:
                                            <strong><?= htmlspecialchars($search); ?></strong>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($dataBarang as $row): ?>
                        <?php
                            $stok = (int)$row['stok'];
                            $min = !empty($row['minimum_stok'])
                                ? (int)$row['minimum_stok']
                                : 0;

                            if ($stok <= 0) {
                                $badge = "<span class='badge bg-danger'>0</span>";
                                $status = "<span class='badge bg-danger'>Kosong</span>";
                            } elseif ($min > 0 && $stok <= $min) {
                                $badge = "<span class='badge bg-warning text-dark'>{$stok}</span>";
                                $status = "<span class='badge bg-warning text-dark'>Menipis</span>";
                            } else {
                                $badge = "<span class='badge bg-success'>{$stok}</span>";
                                $status = "<span class='badge bg-success'>Aman</span>";
                            }
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['kode_barang']); ?></td>
                            <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                            <td><?= $row['nama_kategori'] ? htmlspecialchars($row['nama_kategori']) : '-'; ?></td>
                            <td><?= $row['merek'] ? htmlspecialchars($row['merek']) : '-'; ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['satuan']); ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['lokasi_rak'] ?? '-'); ?></td>
                            <td class="text-center"><?= $badge; ?></td>
                            <td class="text-center"><?= $status; ?></td>
                            <td class="text-center">
                                <div class="aksi-group">
                                <a href="detail.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm text-white">
                                    <i class="bi bi-eye"></i>
                                    Detail
                                </a>
                                    <button type="button"
                                            class="btn btn-danger btn-sm btn-hapus"
                                            data-id="<?= (int)$row['id']; ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_barang'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#hapusModal">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- PAGINATION UI -->
            <?php if ($totalPages > 1): ?>
                <?php 
                // Buat string parameter URL agar kode HTML di bawah tetap rapi
                $urlParams = "&search=" . urlencode($search) . "&sort=" . urlencode($sortKey) . "&dir=" . urlencode($sortDir); 
                ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Tombol Previous -->
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link"
                               href="?<?= buildQuery(['page' => max(1, $page - 1)]) ?>">
                                &laquo; Prev
                            </a>
                        </li>

                        <!-- Angka Halaman -->
                        <?php 
                        // Membatasi tampilan nomor halaman agar tidak terlalu panjang (misal tampil 5 nomor di tengah)
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link"
                                   href="?<?= buildQuery(['page' => 1]) ?>">
                                   1
                                </a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?= buildQuery(['page' => $i]) ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $totalPages; ?><?= $urlParams; ?>"><?= $totalPages; ?></a></li>
                        <?php endif; ?>

                        <!-- Tombol Next -->
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?= min($totalPages, $page + 1); ?><?= $urlParams; ?>">Next &raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

            <div class="mt-3">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='../../index.php'">
                    &larr; Kembali ke Dashboard
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="hapusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="hapus.php" method="POST">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        Konfirmasi Hapus
                    </h5>

                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <p>
                        Yakin ingin menghapus barang:
                    </p>

                    <h5 id="namaBarangHapus"></h5>

                    <input type="hidden"
                           name="id"
                           id="hapusId">

                    <input type="hidden"
                           name="csrf_token"
                           value="<?= $_SESSION['csrf_token']; ?>">

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-danger">
                        Ya, Hapus
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>
// Auto-close alert dalam 5 detik
setTimeout(function() {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.remove();
        }, 150);
    });
}, 5000);

const searchInput = document.getElementById('searchInput');

let timeout;

if (searchInput) {

    searchInput.addEventListener('input', function () {

        clearTimeout(timeout);

        timeout = setTimeout(() => {

            if (
                this.value.trim().length >= 3 ||
                this.value.trim().length === 0
            ) {
                this.form.submit();
            }

        }, 500);

    });

}

document.querySelectorAll('.btn-hapus').forEach(button => {

    button.addEventListener('click', function () {

        const id = this.dataset.id;
        const nama = this.dataset.nama;

        document.getElementById('hapusId').value = id;
        document.getElementById('namaBarangHapus').textContent = nama;

    });

});

</script>

<?php include '../../templates/footer.php'; ?>