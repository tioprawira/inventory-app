<?php
// 1. TAMBAHKAN PROTEKSI LOGIN (Sangat Penting!)
require '../../auth/cek_login.php';
require '../../config/database.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

try {
    // Ambil data dari database
    $query = $conn->query("
        SELECT b.*, k.nama_kategori
        FROM barang b
        LEFT JOIN kategori k ON b.kategori_id = k.id
        ORDER BY b.id DESC
    ");
    
    $data = $query->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Barang');

    // 2. HEADER TABEL LENGKAP
    $headers = ['Kode', 'Nama Barang', 'Kategori', 'Merek', 'Satuan', 'Lokasi Rak', 'Stok', 'Min. Stok'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }

    // Hitung kolom terakhir secara dinamis (A sampai H)
    $lastCol = chr(ord('A') + count($headers) - 1); 

    // 3. STYLING HEADER EXCEL (Warna Biru, Teks Putih Bold, Border)
    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF0D6EFD'] 
        ],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN]
        ]
    ];
    $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

    // 4. ISI DATA BARANG
    $rowNum = 2;
    foreach($data as $row) {
        $sheet->setCellValue('A'.$rowNum, $row['kode_barang']);
        $sheet->setCellValue('B'.$rowNum, $row['nama_barang']);
        $sheet->setCellValue('C'.$rowNum, $row['nama_kategori']);
        $sheet->setCellValue('D'.$rowNum, $row['merek']);
        $sheet->setCellValue('E'.$rowNum, $row['satuan']);
        $sheet->setCellValue('F'.$rowNum, $row['lokasi_rak']);
        $sheet->setCellValue('G'.$rowNum, $row['stok']);
        $sheet->setCellValue('H'.$rowNum, $row['minimum_stok']);
        $rowNum++;
    }

    // 5. STYLING DATA EXCEL (Berikan border & auto-width kolom)
    if ($rowNum > 2) { 
        // Beri border untuk seluruh data
        $sheet->getStyle('A2:' . $lastCol . ($rowNum - 1))->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ]);
    }

    // Sesuaikan lebar kolom otomatis mengikuti panjang teks
    foreach (range('A', $lastCol) as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // 6. BERSIHKAN OUTPUT BUFFER (Mencegah file excel error/corrupt saat dibuka)
    if (ob_get_length()) {
        ob_end_clean();
    }

    // 7. HEADER BROWSER UNTUK FORCE DOWNLOAD
    $namaFile = "Data-Barang-" . date('Y-m-d') . ".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $namaFile . '"');
    header('Cache-Control: max-age=0');
    // Header tambahan untuk kompatibilitas browser lama
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
    header('Cache-Control: cache, must-revalidate'); 
    header('Pragma: public'); 

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    // Jika ada error dari database atau phpspreadsheet, kembalikan ke index dengan notifikasi
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_error'] = 'Gagal mengekspor data: ' . $e->getMessage();
    header('Location: index.php');
    exit;
}
?>