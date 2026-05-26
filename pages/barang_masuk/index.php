<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

$query = $conn->query("
    SELECT
        barang_masuk.*,
        barang.nama_barang,
        barang.kode_barang
    FROM barang_masuk
    JOIN barang
        ON barang_masuk.barang_id = barang.id
    ORDER BY barang_masuk.id DESC
");
?>

<style>
    th{
        cursor: pointer;
        user-select: none;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">

    <h2 class="mb-0">Data Barang Masuk</h2>

    <a href="tambah.php"
       class="btn btn-primary">
        + Tambah Barang Masuk
    </a>

</div>

<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table id="tabelBarangMasuk"
                   class="table table-bordered table-striped table-hover align-middle">

                <thead class="table-dark">

                    <tr>

                        <th onclick="sortTable(0)">No</th>
                        <th onclick="sortTable(1)">Tanggal</th>
                        <th onclick="sortTable(2)">Kode Barang</th>
                        <th onclick="sortTable(3)">Nama Barang</th>
                        <th onclick="sortTable(4)">Jumlah</th>
                        <th onclick="sortTable(5)">Nomor PO</th>
                        <th onclick="sortTable(6)">Keterangan</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php $no = 1; ?>

                <?php while($row = $query->fetch(PDO::FETCH_ASSOC)): ?>

                    <tr>

                        <td><?= $no++; ?></td>

                        <td>
                            <?= date('d-m-Y', strtotime($row['tanggal'])); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['kode_barang']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nama_barang']); ?>
                        </td>

                        <td>
                            <?= number_format($row['jumlah']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['nomor_po']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($row['keterangan']); ?>
                        </td>

                        <td>

                            <a href="edit.php?id=<?= $row['id']; ?>"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <a href="hapus.php?id=<?= $row['id']; ?>"
                               onclick="return confirm('Yakin ingin hapus data ini?')"
                               class="btn btn-danger btn-sm">
                                Hapus
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

            <a href="../../admin/dashboard.php"
               class="btn btn-secondary mt-2">
                ← Kembali
            </a>

        </div>

    </div>

</div>

<script>
function sortTable(n) {

    const table = document.getElementById("tabelBarangMasuk");
    let switching = true;
    let dir = "asc";

    while (switching) {

        switching = false;
        const rows = table.rows;

        for (let i = 1; i < (rows.length - 1); i++) {

            let shouldSwitch = false;

            const x = rows[i].getElementsByTagName("TD")[n];
            const y = rows[i + 1].getElementsByTagName("TD")[n];

            if (dir === "asc") {

                if (x.innerText.toLowerCase() > y.innerText.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }

            } else {

                if (x.innerText.toLowerCase() < y.innerText.toLowerCase()) {
                    shouldSwitch = true;
                    break;
                }

            }
        }

        if (shouldSwitch) {

            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;

        } else {

            if (dir === "asc") {
                dir = "desc";
                switching = true;
            }

        }
    }
}
</script>

<?php include '../../templates/footer.php'; ?>