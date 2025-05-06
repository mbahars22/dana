<?php
session_start();
require('../vendor/fpdf/fpdf.php');
include '../koneksi.php';
require '../vendor/autoload.php'; // Pastikan autoload PHPSpreadsheet sudah di-include

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$thajaran = $_SESSION['thajaran'];
$awal = isset($_POST['awal']) ? $_POST['awal'] : date('Y-m-d');
$akhir = isset($_POST['akhir']) ? $_POST['akhir'] : date('Y-m-d');
$kategori = isset($_POST['kategori_hari']) ? $_POST['kategori_hari'] : '';

// Ambil data profil sekolah
$profil = $conn->query("SELECT * FROM tb_profile LIMIT 1")->fetch_assoc();
$nama_sekolah = $profil['nama_sekolah'];
$status = $profil['status'];
$alamat = $profil['alamat'];
$kepsek = $profil['kep_sek'];
$nama_user = isset($_SESSION['user']) ? $_SESSION['user'] : 'User';
$nama_kota = $profil['kota'];
$tanggal_cetak = date('d-m-Y');

// Query pembayaran
$sql = "
    SELECT kd_transaksi, kd_biaya, tgl_trans, nis, nama, kelas, method, SUM(bayar) as total_bayar 
    FROM pembayaran_siswa 
    WHERE tgl_trans BETWEEN '$awal' AND '$akhir' 
    AND LEFT(kd_biaya, 3) = '$kategori'
    GROUP BY kd_transaksi, tgl_trans, nis 
    ORDER BY tgl_trans ASC
";
$result = $conn->query($sql);


if (isset($_POST['download_excel'])) {
    // === Download Excel ===
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Styling header
    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'LAPORAN PEMBAYARAN SISWA');
    $sheet->mergeCells('A2:I2');
    $sheet->setCellValue('A2', 'Tahun Ajaran: ' . $thajaran);
    $sheet->mergeCells('A3:I3');
    $sheet->setCellValue('A3', 'Periode: ' . date('d-m-Y', strtotime($awal)) . ' s/d ' . date('d-m-Y', strtotime($akhir)));
    $sheet->mergeCells('A4:I4');
    $sheet->setCellValue('A4', 'Kategori: ' . $kategori);

    // Header Table
    $sheet->setCellValue('A5', 'No')
          ->setCellValue('B5', 'Tanggal')
          ->setCellValue('C5', 'No Transaksi')
          ->setCellValue('D5', 'NIS')
          ->setCellValue('E5', 'Nama Siswa')
          ->setCellValue('F5', 'Kelas')
          ->setCellValue('G5', 'Kode Biaya')
          ->setCellValue('H5', 'Metode')
          ->setCellValue('I5', 'Jumlah Bayar');

    // Styling header columns
    $sheet->getStyle('A5:I5')->getFont()->setBold(true);
    $sheet->getColumnDimension('A')->setWidth(5);
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getColumnDimension('C')->setWidth(20);
    $sheet->getColumnDimension('D')->setWidth(15);
    $sheet->getColumnDimension('E')->setWidth(30);
    $sheet->getColumnDimension('F')->setWidth(10);
    $sheet->getColumnDimension('G')->setWidth(20);
    $sheet->getColumnDimension('H')->setWidth(15);
    $sheet->getColumnDimension('I')->setWidth(20);

    // Data Rows
    $no = 1;
    $total = 0;
    $row_num = 6; // Start from row 6
    while ($row = $result->fetch_assoc()) {
        $sheet->setCellValue("A$row_num", $no++)
              ->setCellValue("B$row_num", date('d-m-Y', strtotime($row['tgl_trans'])))
              ->setCellValue("C$row_num", $row['kd_transaksi'])
              ->setCellValue("D$row_num", $row['nis'])
              ->setCellValue("E$row_num", $row['nama'])
              ->setCellValue("F$row_num", $row['kelas'])
              ->setCellValue("G$row_num", $row['kd_biaya'])
              ->setCellValue("H$row_num", $row['method'])
              ->setCellValue("I$row_num", $row['total_bayar']);
        $sheet->getStyle("I$row_num")->getNumberFormat()->setFormatCode('"Rp" #,##0');

        $total += $row['total_bayar'];
        $row_num++;
    }

    // Total Pembayaran
    $sheet->mergeCells("A$row_num:H$row_num");
    $sheet->setCellValue("A$row_num", 'TOTAL PEMBAYARAN');
    $sheet->setCellValue("I$row_num", $total);
    $sheet->getStyle("I$row_num")->getNumberFormat()->setFormatCode('"Rp" #,##0');

    // Set header and download Excel file
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="laporan_mingguan.xlsx"');
    $writer->save('php://output');
    exit;
} else {
    // === Generate PDF ===
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->SetMargins(15, 10, 10);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);

    // Header Sekolah
    $pdf->Cell(0, 7, strtoupper($nama_sekolah), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $status, 0, 1, 'C');
    $pdf->Cell(0, 5, $alamat, 0, 1, 'C');
    $pdf->Ln(5);

    // Judul
    $pdf->SetFont('Arial', 'B', 11);
    $judul_periode = ($awal == $akhir)
        ? 'Tanggal: ' . date('d-m-Y', strtotime($awal))
        : 'Periode: ' . date('d-m-Y', strtotime($awal)) . ' s/d ' . date('d-m-Y', strtotime($akhir));
    $pdf->Cell(0, 7, 'LAPORAN PEMBAYARAN SISWA', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Tahun Ajaran: ' . $thajaran, 0, 1, 'C');
    $pdf->Cell(0, 5, $judul_periode, 0, 1, 'C');
    $pdf->Cell(0, 5, 'Kategori: ' . $kategori, 0, 1, 'C');
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 8, 'No', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(25, 8, 'No Transaksi', 1, 0, 'C');
    $pdf->Cell(20, 8, 'NIS', 1, 0, 'C');
    $pdf->Cell(60, 8, 'Nama Siswa', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Kelas', 1, 0, 'C');
    $pdf->Cell(35, 8, 'Pembayaran', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Metode', 1, 0, 'C');
    $pdf->Cell(40, 8, 'Jumlah Bayar', 1, 1, 'C');

    // Data rows
    $pdf->SetFont('Arial', '', 10);
    $no = 1;
    $total_all = 0;
    $result->data_seek(0); // Kembalikan pointer ke awal hasil
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(15, 8, $no++, 1, 0, 'C');
        $pdf->Cell(25, 8, date('d-m-Y', strtotime($row['tgl_trans'])), 1, 0, 'C');
        $pdf->Cell(25, 8, $row['kd_transaksi'], 1, 0, 'C');
        $pdf->Cell(20, 8, $row['nis'], 1, 0, 'C');
        $nama = $row['nama'];
        if (mb_strlen($nama) > 22) {
            $nama = mb_substr($nama, 0, 17) . '...';
        }
        $pdf->Cell(60, 8, $nama, 1, 0);
        $pdf->Cell(25, 8, $row['kelas'], 1, 0, 'C');
        $pdf->Cell(35, 8, $row['kd_biaya'], 1, 0, 'C');
        $pdf->Cell(25, 8, $row['method'], 1, 0, 'C');
        $pdf->Cell(40, 8, 'Rp ' . number_format($row['total_bayar'], 0, ',', '.'), 1, 1, 'C');
        $total_all += $row['total_bayar'];
    }

    // Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(175, 8, 'TOTAL PEMBAYARAN', 1, 0, 'C');
    $pdf->Cell(40, 8, 'Rp ' . number_format($total_all, 0, ',', '.'), 1, 1, 'C');

    // Tanda Tangan
    // === Tanda Tangan ===
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(140, 6, 'Mengetahui,', 0, 0, 'L');
$pdf->Cell(130, 6, $nama_kota . ', ' . $tanggal_cetak, 0, 1, 'R');
$pdf->Cell(140, 6, 'Kepala Sekolah', 0, 0, 'L');
$pdf->Cell(130, 6, 'Dicetak oleh,', 0, 1, 'R');
$pdf->Ln(18);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(140, 6, $kepsek, 0, 0, 'L');
$pdf->Cell(130, 6, $nama_user, 0, 1, 'R');
    // Output PDF
    $pdf->Output();
}
?>
