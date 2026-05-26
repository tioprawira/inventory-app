<?php
include '../../auth/cek_login.php';
include '../../config/database.php';
include '../../templates/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {

    echo "<script>
            alert('Data tidak ditemukan');
            window.location='index.php';
          </script>";
    exit;
}

$id = (int) $_GET['id'];

$query = $conn->prepare("
    SELECT 
        b.*,
        k.nama_kategori,
        d.nomor_surat_jalan,
        d.nomor_po,
        d.persamaan_produk

    FROM barang b

    LEFT JOIN kategori k 
        ON b.kategori_id = k.id

    LEFT JOIN detail d
        ON d.barang_id = b.id

    WHERE b.id = ?
");

$query->execute([$id]);

$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {

    echo "<script>
            alert('Produk tidak ditemukan');
            window.location='index.php';
          </script>";
    exit;
}

// Ambil gambar
$getImages = $conn->prepare("
    SELECT nama_file
    FROM gambar_produk
    WHERE barang_id = ?
    ORDER BY urutan ASC, id ASC
");

$getImages->execute([$id]);

$images = $getImages->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="card shadow-sm border-0">

    <div class="card-header bg-primary text-white">
        <h4 class="mb-0 fw-bold">
            Detail Produk
        </h4>
    </div>

    <div class="card-body">

        <?php if (isset($_SESSION['flash_success'])): ?>

            <div class="alert alert-success alert-dismissible fade show">

                <?= htmlspecialchars($_SESSION['flash_success']); ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

            <?php unset($_SESSION['flash_success']); ?>

        <?php endif; ?>

        <div class="row g-4">

            <!-- Gambar -->
            <div class="col-md-4">

                <div class="border rounded p-3 bg-light">

                    <?php if (!empty($images)): ?>

                        <div class="product-gallery">

                            <div class="product-image-wrapper">

                                <img
                                    src="../../assets/img/<?= htmlspecialchars($images[0]['nama_file']) ?>"
                                    class="product-image"
                                    id="mainImage"
                                    alt="<?= htmlspecialchars($row['nama_barang']) ?>"
                                >

                                <div class="zoom-window" id="zoomWindow"></div>

                            </div>

                            <div class="thumbnail-slider mt-3">

                                <?php foreach($images as $index => $img): ?>

                                    <img
                                        src="../../assets/img/<?= htmlspecialchars($img['nama_file']) ?>"
                                        class="thumb-image <?= $index === 0 ? 'active' : '' ?>"
                                        onclick="changeImage(this)"
                                    >

                                <?php endforeach; ?>

                            </div>

                        </div>

                    <?php else: ?>

                        <div class="text-center py-5 text-muted">
                            Gambar tidak tersedia
                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <!-- Detail -->
            <div class="col-md-8">

                <table class="table table-bordered">

                    <tr>
                        <th width="250">Kode Barang</th>
                        <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                    </tr>

                    <tr>
                        <th>Nama Barang</th>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    </tr>

                    <tr>
                        <th>Kategori</th>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                    </tr>

                    <tr>
                        <th>Merek</th>
                        <td><?= htmlspecialchars($row['merek']) ?></td>
                    </tr>

                    <tr>
                        <th>Satuan</th>
                        <td><?= htmlspecialchars($row['satuan']) ?></td>
                    </tr>

                    <tr>
                        <th>Lokasi Rak</th>
                        <td><?= htmlspecialchars($row['lokasi_rak']) ?></td>
                    </tr>

                    <tr>
                        <th>Stok</th>
                        <td><?= number_format($row['stok']) ?></td>
                    </tr>

                    <tr>
                        <th>Minimum Stok</th>
                        <td><?= number_format($row['minimum_stok']) ?></td>
                    </tr>

                    <tr>
                        <th>Nomor Surat Jalan</th>
                        <td>
                            <?= htmlspecialchars($row['nomor_surat_jalan'] ?? '-') ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Nomor PO</th>
                        <td>
                            <?= htmlspecialchars($row['nomor_po'] ?? '-') ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Persamaan Produk</th>
                        <td>
                            <?= nl2br(htmlspecialchars($row['persamaan_produk'] ?? '-')) ?>
                        </td>
                    </tr>

                </table>

                <div class="d-flex gap-2">

                    <a
                        href="edit.php?id=<?= $row['id'] ?>"
                        class="btn btn-warning"
                    >
                        Edit Produk
                    </a>

                    <a
                        href="index.php"
                        class="btn btn-secondary"
                    >
                        ← Kembali
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<style>

.product-image-wrapper{
    position:relative;
    width:100%;
    height:320px;
    background:#fff;
    border-radius:12px;

    display:flex;
    align-items:center;
    justify-content:center;

    overflow:visible;
}

.product-image{
    width:100%;
    height:100%;
    object-fit:contain;
    cursor:crosshair;
}

.zoom-window{
    position:absolute;
    inset:0;

    width:100%;
    height:100%;

    border-radius:12px;

    background-repeat:no-repeat;
    background-color:#fff;

    display:none;

    z-index:10;

    cursor:crosshair;

    transition:
    background-size 0.1s ease,
    background-position 0.05s linear;
}

.thumb-image:hover{
    transform:scale(1.05);
}

.thumb-image{
    transition:0.2s;
}

.thumbnail-slider{
    display:flex;
    gap:10px;
    overflow-x:auto;
}

.thumb-image{
    width:70px;
    height:70px;
    object-fit:cover;

    border-radius:10px;
    border:2px solid transparent;

    cursor:pointer;
}

.thumb-image.active{
    border-color:#0d6efd;
}

</style>

<script>

const wrapper = document.querySelector('.product-image-wrapper');
const image = document.getElementById('mainImage');
const zoomWindow = document.getElementById('zoomWindow');

let zoomLevel = 2.5;

if(wrapper && image){

    function updateZoom(e){

        const rect = wrapper.getBoundingClientRect();

        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;

        zoomWindow.style.display = 'block';

        zoomWindow.style.backgroundImage =
            `url('${image.src}')`;

        zoomWindow.style.backgroundSize =
            `${zoomLevel * 100}%`;

        zoomWindow.style.backgroundPosition =
            `${xPercent}% ${yPercent}%`;
    }

    wrapper.addEventListener('mousemove', function(e){

        updateZoom(e);

    });

    wrapper.addEventListener('mouseenter', function(){

        zoomWindow.style.display = 'block';

    });

    wrapper.addEventListener('mouseleave', function(){

        zoomWindow.style.display = 'none';

    });

    // Scroll zoom
    wrapper.addEventListener('wheel', function(e){

        e.preventDefault();

        if(e.deltaY < 0){

            // Scroll atas = zoom in
            zoomLevel += 0.2;

        } else {

            // Scroll bawah = zoom out
            zoomLevel -= 0.2;
        }

        // batas zoom
        if(zoomLevel < 1){
            zoomLevel = 1;
        }

        if(zoomLevel > 6){
            zoomLevel = 6;
        }

        updateZoom(e);

    });

}

function changeImage(element){

    image.src = element.src;

    zoomWindow.style.backgroundImage =
        `url('${element.src}')`;

    document.querySelectorAll('.thumb-image')
        .forEach(img => img.classList.remove('active'));

    element.classList.add('active');

}


setTimeout(() => {

    const alertBox = document.querySelector('.alert');

    if(alertBox){

        const bsAlert = bootstrap.Alert.getOrCreateInstance(alertBox);

        bsAlert.close();

    }

}, 5000);

</script>

<?php include '../../templates/footer.php'; ?>