<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$query = $conn->query("
    SELECT *
    FROM kategori
    ORDER BY id DESC
");
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Data Kategori</h2>

    <a href="tambah.php" class="btn btn-primary">
        + Tambah Kategori
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark text-center">
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Kategori</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if($query->rowCount() > 0): ?>

                    <?php $no = 1; ?>
                    <?php while($row = $query->fetch(PDO::FETCH_ASSOC)): ?>

                        <tr>
                            <td class="text-center">
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_kategori']); ?>
                            </td>

                            <td class="text-center">

                                <a href="edit.php?id=<?= $row['id']; ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="hapus.php?id=<?= $row['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                    Hapus
                                </a>

                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            Data kategori belum tersedia
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="mt-3">
            <a href="../../index.php" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>

    </div>
</div>

<?php include '../../templates/footer.php'; ?>