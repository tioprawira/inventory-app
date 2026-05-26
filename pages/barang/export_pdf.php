<?php
// 1. TAMBAHKAN PROTEKSI LOGIN (Wajib agar tidak bocor)
require '../../auth/cek_login.php';
require '../../config/database.php';
require '../../vendor/autoload.php';

try {
    // 2. AMBIL DATA DARI DATABASE
    $query = $conn->query("
        SELECT b.*, k.nama_kategori
        FROM barang b
        LEFT JOIN kategori k ON b.kategori_id = k.id
        ORDER BY b.id DESC
    ");
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    // 3. INISIALISASI TCPDF
    // Mengubah ke 'L' (Landscape) karena kolom tabel kita cukup banyak
    $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

    // Set metadata dokumen
    $pdf->SetCreator('Sistem Inventori');
    $pdf->SetTitle('Laporan Data Barang');

    // Matikan header/footer default
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margin (Kiri, Atas, Kanan)
    $pdf->SetMargins(15, 15, 15);
    // Set auto page break (Margin Bawah)
    $pdf->SetAutoPageBreak(TRUE, 15);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    // 4. SUSUN DESAIN TABEL HTML (Dengan styling CSS inline)
    $html = '
    <h2 style="text-align:center; margin-bottom: 20px;">Laporan Data Barang</h2>
    <table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
        <thead>
            <tr style="background-color:#0d6efd; color:#ffffff; font-weight:bold; text-align:center;">
                <th width="12%">Kode</th>
                <th width="22%">Nama Barang</th>
                <th width="15%">Kategori</th>
                <th width="14%">Merek</th>
                <th width="10%">Satuan</th>
                <th width="11%">Lokasi Rak</th>
                <th width="8%">Stok</th>
                <th width="8%">Min. Stok</th>
            </tr>
        </thead>
        <tbody>';

    // Looping data
    foreach($data as $row) {
        $html .= '
            <tr>
                <td align="center">'.htmlspecialchars($row['kode_barang']).'</td>
                <td>'.htmlspecialchars($row['nama_barang']).'</td>
                <td>'.htmlspecialchars($row['nama_kategori']).'</td>
                <td>'.htmlspecialchars($row['merek']).'</td>
                <td align="center">'.htmlspecialchars($row['satuan']).'</td>
                <td align="center">'.htmlspecialchars($row['lokasi_rak']).'</td>
                <td align="center">'.$row['stok'].'</td>
                <td align="center">'.$row['minimum_stok'].'</td>
            </tr>';
    }

    $html .= '</tbody></table>';

    // 5. CETAK HTML KE PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // 6. BERSIHKAN OUTPUT BUFFER (Wajib sebelum Output)
    if (ob_get_length()) {
        ob_end_clean();
    }

    // 7. HASILKAN PDF 
    // Gunakan 'I' untuk preview di browser, ganti ke 'D' jika ingin auto-download
    $namaFile = 'Laporan-Data-Barang-' . date('Y-m-d') . '.pdf';
    $pdf->Output($namaFile, 'I');
    exit;

} catch (Exception $e) {
    // 8. PENANGANAN ERROR (Kembali ke index jika gagal)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_error'] = 'Gagal menghasilkan PDF: ' . $e->getMessage();
    header('Location: index.php');
    exit;
}
?>