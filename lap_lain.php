<?php
session_start();
require 'vendor/autoload.php';
require 'vendor/fpdf/fpdf.php';
include 'koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Ambil parameter
$thajaran = $_POST['tahun'] ?? $_SESSION['thajaran'];
$rombel = $_POST['rombel'] ?? '';
$kategori = isset($_POST['kategori_volume']) ? substr($_POST['kategori_volume'], 0, 3) : '';

// Ambil profil sekolah
$qprofil = mysqli_query($conn, "SELECT * FROM tb_profile LIMIT 1");
$profil = mysqli_fetch_assoc($qprofil);
$nama_sekolah = $profil['nama_sekolah'];
$status = $profil['status'];
$alamat = $profil['alamat'];
$kepsek = $profil['kep_sek'];
$nama_kota = $profil['kota'];

// Ambil nama kategori
$row_kategori = ['nm_biaya' => '-'];
if (!empty($kategori)) {
    $qkategori = mysqli_query($conn, "SELECT nm_biaya FROM tb_kd_biaya WHERE kd_biaya = '$kategori' LIMIT 1");
    if ($qkategori && mysqli_num_rows($qkategori) > 0) {
        $row_kategori = mysqli_fetch_assoc($qkategori);
    }
}

// Query data
$sql = "
    SELECT p.nis, p.kelas, p.kd_biaya, p.bayar, p.kd_transaksi, p.method, b.nm_biaya
    FROM pembayaran_siswa p
    JOIN tb_kd_biaya b ON LEFT(p.kd_biaya, 3) = b.kd_biaya
    WHERE p.thajaran = '$thajaran'
";
if (!empty($rombel)) $sql .= " AND p.kelas = '$rombel'";
if (!empty($kategori)) $sql .= " AND LEFT(p.kd_biaya, 3) = '$kategori'";
$sql .= " ORDER BY p.kelas ASC, p.kd_biaya ASC";

$result = mysqli_query($conn, $sql);

if (isset($_POST['tahun_pdf'])) {
    // ==== PDF ====
    ob_start();
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 7, strtoupper($nama_sekolah), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 6, $status, 0, 1, 'C');
    $pdf->Cell(0, 6, $alamat, 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "LAPORAN PEMBAYARAN SISWA", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 6, "Tahun Ajaran: $thajaran", 0, 1, 'C');
    $pdf->Cell(0, 6, "Kelas: $rombel", 0, 1, 'C');
    $pdf->Cell(0, 6, 'Kategori: ' . $row_kategori['nm_biaya'], 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 8, 'No', 1, 0, 'C');
    $pdf->Cell(15, 8, 'NIS', 1, 0, 'C');
    $pdf->Cell(50, 8, 'Nama Pembayaran', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Kode Biaya', 1, 0, 'C');
    $pdf->Cell(30, 8, 'No Transaksi', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Metode', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Jumlah (Rp)', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $nama_biaya = $row['nm_biaya'];
        if (mb_strlen($nama_biaya) > 22) $nama_biaya = mb_substr($nama_biaya, 0, 17) . '...';

        $pdf->Cell(15, 8, $no++, 1, 0, 'C');
        $pdf->Cell(15, 8, $row['nis'], 1, 0, 'C');
        $pdf->Cell(50, 8, $nama_biaya, 1, 0);
        $pdf->Cell(30, 8, $row['kd_biaya'], 1, 0, 'C');
        $pdf->Cell(30, 8, $row['kd_transaksi'], 1, 0, 'C');
        $pdf->Cell(20, 8, $row['method'], 1, 0, 'C');
        $pdf->Cell(30, 8, number_format($row['bayar'], 0, ',', '.'), 1, 1, 'R');
    }

    $pdf->Output();
    exit;

} elseif (isset($_POST['excel_tahun'])) {
    // ==== EXCEL ====
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header Sekolah
    $sheet->mergeCells('A1:G1')->setCellValue('A1', strtoupper($nama_sekolah));
    $sheet->mergeCells('A2:G2')->setCellValue('A2', $status);
    $sheet->mergeCells('A3:G3')->setCellValue('A3', $alamat);
    $sheet->mergeCells('A4:G4')->setCellValue('A4', "LAPORAN PEMBAYARAN SISWA");
    $sheet->mergeCells('A5:G5')->setCellValue('A5', "Tahun Ajaran: $thajaran");
    $sheet->mergeCells('A6:G6')->setCellValue('A6', "Kelas: $rombel");
    $sheet->mergeCells('A7:G7')->setCellValue('A7', "Kategori: " . $row_kategori['nm_biaya']);

    for ($i = 1; $i <= 7; $i++) {
        $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$i}")->getFont()->setBold(true);
    }

    // Judul Kolom
    $sheet->setCellValue('A9', 'No');
    $sheet->setCellValue('B9', 'NIS');
    $sheet->setCellValue('C9', 'Nama Pembayaran');
    $sheet->setCellValue('D9', 'Kode Biaya');
    $sheet->setCellValue('E9', 'No Transaksi');
    $sheet->setCellValue('F9', 'Metode');
    $sheet->setCellValue('G9', 'Jumlah (Rp)');

    $sheet->getStyle('A9:G9')->getFont()->setBold(true);
    $sheet->getStyle('A9:G9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Isi Data
    $row = 10;
    $no = 1;
    mysqli_data_seek($result, 0); // reset result pointer
    while ($r = mysqli_fetch_assoc($result)) {
        $nama_biaya = mb_strlen($r['nm_biaya']) > 22 ? mb_substr($r['nm_biaya'], 0, 17) . '...' : $r['nm_biaya'];

        $sheet->setCellValue("A{$row}", $no++);
        $sheet->setCellValue("B{$row}", $r['nis']);
        $sheet->setCellValue("C{$row}", $nama_biaya);
        $sheet->setCellValue("D{$row}", $r['kd_biaya']);
        $sheet->setCellValue("E{$row}", $r['kd_transaksi']);
        $sheet->setCellValue("F{$row}", $r['method']);
        $sheet->setCellValue("G{$row}", number_format($r['bayar'], 0, ',', '.'));
        $row++;
    }

    // Set border untuk semua data
    $styleArray = [
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    ];
    $sheet->getStyle("A9:G" . ($row - 1))->applyFromArray($styleArray);

    // Auto width kolom
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Output
    $filename = "Laporan_Pembayaran_Siswa_$thajaran.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>
