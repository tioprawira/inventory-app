<?php
include '../auth/cek_login.php';

if($_SESSION['level'] != 'staff') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Staff</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">

    <div class="container">

        <a class="navbar-brand fw-bold" href="#">
            Inventory Staff
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">

            <span class="text-white">
                Halo, <?= $_SESSION['nama']; ?>
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

    <div class="card border-0 shadow-lg rounded-4 bg-primary text-white mb-4">

        <div class="card-body p-5">

            <h1 class="fw-bold">
                Dashboard Staff
            </h1>

            <p class="lead">
                Sistem Inventory Barang Modern
            </p>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

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

        <div class="col-md-4">

            <div class="card border-0 shadow rounded-4 h-100">

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

</body>
</html>