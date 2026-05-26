<?php
include '../auth/cek_login.php';
include '../config/database.php';

if($_SESSION['level'] != 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| HITUNG TOTAL DATA
|--------------------------------------------------------------------------
*/

try {
    $total_barang = $conn->query("SELECT COUNT(*) as total FROM barang")->fetch(PDO::FETCH_ASSOC)['total'];
    $total_kategori = $conn->query("SELECT COUNT(*) as total FROM kategori")->fetch(PDO::FETCH_ASSOC)['total'];
    $total_masuk = $conn->query("SELECT COUNT(*) as total FROM barang_masuk")->fetch(PDO::FETCH_ASSOC)['total'];
    $total_keluar = $conn->query("SELECT COUNT(*) as total FROM barang_keluar")->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    // Atur nilai default jika terjadi error agar halaman tetap bisa dimuat
    $total_barang = $total_kategori = $total_masuk = $total_keluar = 0;
    // Opsi: Anda bisa mencatat (log) pesan error ini ke file terpisah
}

// Ambil data untuk grafik (Contoh: Barang Masuk per Bulan untuk Tahun Ini)
$tahun_sekarang = date('Y');

// Query Barang Masuk
$query_masuk = $conn->query("
    SELECT MONTH(tanggal) as bulan, SUM(jumlah) as total 
    FROM barang_masuk 
    WHERE YEAR(tanggal) = '$tahun_sekarang' 
    GROUP BY MONTH(tanggal)
");
$data_masuk = array_fill(1, 12, 0); // Buat array 12 bulan default 0
while ($row = $query_masuk->fetch(PDO::FETCH_ASSOC)) {
    $data_masuk[$row['bulan']] = (int)$row['total'];
}

// Query Barang Keluar
$query_keluar = $conn->query("
    SELECT MONTH(tanggal) as bulan, SUM(jumlah) as total 
    FROM barang_keluar 
    WHERE YEAR(tanggal) = '$tahun_sekarang' 
    GROUP BY MONTH(tanggal)
");
$data_keluar = array_fill(1, 12, 0); 
while ($row = $query_keluar->fetch(PDO::FETCH_ASSOC)) {
    $data_keluar[$row['bulan']] = (int)$row['total'];
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body{
            background: #f4f6f9;
        }

        .dashboard-card{
            transition: 0.3s;
        }

        .dashboard-card:hover{
            transform: translateY(-5px);
        }

        .hero{
            background: linear-gradient(135deg,#0d6efd,#0dcaf0);
        }

    </style>

</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">

    <div class="container">

        <a class="navbar-brand fw-bold" href="#">
            Inventory Admin
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">

            <span class="text-white">
                Halo, <?= htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?>
            </span>

            <a href="../auth/logout.php"
               class="btn btn-danger btn-sm rounded-pill">

                <i class="bi bi-box-arrow-right"></i>
                Logout

            </a>

        </div>

    </div>

</nav>

<div class="container py-4">

    <!-- HERO -->
    <div class="card border-0 shadow-lg rounded-4 text-white mb-4 hero">

        <div class="card-body p-5">

            <h1 class="fw-bold">
                Dashboard Admin
            </h1>

            <p class="lead mb-0">
                PT Meindo Elang Indah
            </p>

        </div>

    </div>

    <!-- STATISTIK -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center">

                <div class="card-body">

                    <i class="bi bi-box-seam display-5 text-primary"></i>

                    <h3 class="fw-bold mt-2">
                        <?= $total_barang; ?>
                    </h3>

                    <p class="text-muted mb-0">
                        Total Barang
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center">

                <div class="card-body">

                    <i class="bi bi-tags display-5 text-success"></i>

                    <h3 class="fw-bold mt-2">
                        <?= $total_kategori; ?>
                    </h3>

                    <p class="text-muted mb-0">
                        Total Kategori
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center">

                <div class="card-body">

                    <i class="bi bi-box-arrow-in-down display-5 text-info"></i>

                    <h3 class="fw-bold mt-2">
                        <?= $total_masuk; ?>
                    </h3>

                    <p class="text-muted mb-0">
                        Barang Masuk
                    </p>

                </div>

            </div>

        </div>

        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 text-center">

                <div class="card-body">

                    <i class="bi bi-arrow-left-right display-5 text-danger"></i>

                    <h3 class="fw-bold mt-2">
                        <?= $total_keluar; ?>
                    </h3>

                    <p class="text-muted mb-0">
                        Barang Keluar
                    </p>

                </div>

            </div>

        </div>

    </div>

    <!-- GRAFIK STATISTIK -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Statistik Pergerakan Barang (Tahun <?= $tahun_sekarang; ?>)</h5>
                    <!-- Canvas untuk Chart.js -->
                    <canvas id="statistikChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- MENU -->
    <div class="row g-4">

        <!-- BARANG -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 h-100 dashboard-card">

                <div class="card-body text-center p-4">

                    <i class="bi bi-box-seam display-3 text-primary"></i>

                    <h4 class="fw-bold mt-3">
                        Data Barang
                    </h4>

                    <p class="text-muted">
                        Kelola seluruh data barang inventory.
                    </p>

                    <a href="../pages/barang/index.php"
                       class="btn btn-primary w-100 rounded-pill">

                        Kelola Barang

                    </a>

                </div>

            </div>

        </div>

        <!-- KATEGORI -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 h-100 dashboard-card">

                <div class="card-body text-center p-4">

                    <i class="bi bi-tags display-3 text-success"></i>

                    <h4 class="fw-bold mt-3">
                        Kategori
                    </h4>

                    <p class="text-muted">
                        Kelola kategori barang.
                    </p>

                    <a href="../pages/kategori/index.php"
                       class="btn btn-success w-100 rounded-pill">

                        Kelola Kategori

                    </a>

                </div>

            </div>

        </div>

        <!-- BARANG MASUK -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 h-100 dashboard-card">

                <div class="card-body text-center p-4">

                    <i class="bi bi-box-arrow-in-down display-3 text-info"></i>

                    <h4 class="fw-bold mt-3">
                        Barang Masuk
                    </h4>

                    <p class="text-muted">
                        Kelola transaksi barang masuk.
                    </p>

                    <a href="../pages/barang_masuk/index.php"
                       class="btn btn-info w-100 rounded-pill">

                        Barang Masuk

                    </a>

                </div>

            </div>

        </div>

        <!-- BARANG KELUAR -->
        <div class="col-md-3">

            <div class="card border-0 shadow rounded-4 h-100 dashboard-card">

                <div class="card-body text-center p-4">

                    <i class="bi bi-arrow-left-right display-3 text-danger"></i>

                    <h4 class="fw-bold mt-3">
                        Barang Keluar
                    </h4>

                    <p class="text-muted">
                        Kelola transaksi pengeluaran barang.
                    </p>

                    <a href="../pages/barang_keluar/index.php"
                       class="btn btn-danger w-100 rounded-pill">

                        Barang Keluar

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- FOOTER -->
<footer class="text-center py-4 text-muted">

    &copy; <?= date('Y'); ?> Inventory App by Prasetio Prawira Tulloh

</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ctx = document.getElementById('statistikChart').getContext('2d');
    
    // Konversi data PHP ke format JavaScript
    const dataMasuk = <?= json_encode(array_values($data_masuk)); ?>;
    const dataKeluar = <?= json_encode(array_values($data_keluar)); ?>;
    const bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

    const statistikChart = new Chart(ctx, {
        type: 'bar', // Bisa diganti 'line' jika ingin grafik garis
        data: {
            labels: bulanLabels,
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: dataMasuk,
                    backgroundColor: 'rgba(13, 202, 240, 0.7)', // Warna info Bootstrap
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Barang Keluar',
                    data: dataKeluar,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)', // Warna danger Bootstrap
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10 // Sesuaikan dengan skala pergerakan barang Anda
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>
</body>
</html>