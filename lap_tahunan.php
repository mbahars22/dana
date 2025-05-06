<?php
session_start();
require('vendor/fpdf/fpdf.php');
require 'vendor/autoload.php'; // PHPSpreadsheet
include 'koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil tahun ajaran dan rombel
$thajaran = isset($_POST['tahun']) ? $_POST['tahun'] : $_SESSION['thajaran'];
$rombel = isset($_POST['rombel']) ? $_POST['rombel'] : '';
$format = isset($_POST['format']) ? $_POST['format'] : 'pdf';

$bulanMap = [
    'JUL' => 7, 'AGU' => 8, 'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DES' => 12,
    'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MEI' => 5, 'JUN' => 6
];

// Ambil profil sekolah
$qprofil = mysqli_query($conn, "SELECT * FROM tb_profile LIMIT 1");
$profil = mysqli_fetch_assoc($qprofil);
$nama_sekolah = $profil['nama_sekolah'];
$status = $profil['status'];
$alamat = $profil['alamat'];
$kepsek = $profil['kep_sek'];
$nama_kota = $profil['kota'];
$nama_user = isset($_SESSION['user']) ? $_SESSION['user'] : 'User';
$tanggal_cetak = date('d-m-Y');

// Ambil data siswa
$querySiswa = "
    SELECT DISTINCT p.nis, p.nama 
    FROM pembayaran_siswa p
    WHERE p.thajaran='$thajaran' AND p.kelas='$rombel'
";
$resultSiswa = mysqli_query($conn, $querySiswa);
$dataSiswa = [];
$totalKelas = 0;

while ($row = mysqli_fetch_assoc($resultSiswa)) {
    $nis = $row['nis'];
    $nama = $row['nama'];
    $bulanBayar = array_fill_keys(array_keys($bulanMap), 0);

    $qBayar = mysqli_query($conn, "
        SELECT kd_biaya, SUM(bayar) as total 
        FROM pembayaran_siswa 
        WHERE nis='$nis' AND thajaran='$thajaran' 
        GROUP BY kd_biaya
    ");
    while ($b = mysqli_fetch_assoc($qBayar)) {
        $bulanText = substr($b['kd_biaya'], 7, 3);
        if (isset($bulanBayar[$bulanText])) {
            $bulanBayar[$bulanText] = $b['total'];
        }
    }
    $totalSiswa = array_sum($bulanBayar);
    $totalKelas += $totalSiswa;

    $dataSiswa[] = [
        'nis' => $nis,
        'nama' => $nama,
        'bulan' => $bulanBayar,
        'total' => $totalSiswa
    ];
}

// === PDF ===
if (isset($_POST['tahun_pdf'])) {
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 7, strtoupper($nama_sekolah), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 6, $status, 0, 1, 'C');
    $pdf->Cell(0, 6, $alamat, 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "LAPORAN PEMBAYARAN SISWA TAHUN AJARAN $thajaran", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 6, "Kelas : $rombel", 0, 1, 'C');
    $pdf->Ln(5);

    // Header tabel
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 10, 'No', 1, 0, 'C');
    $pdf->Cell(15, 10, 'NIS', 1, 0, 'C');
    $pdf->Cell(35, 10, 'Nama Siswa', 1, 0, 'C');
    foreach (array_keys($bulanMap) as $bln) {
        $pdf->Cell(16, 10, $bln, 1, 0, 'C');
    }
    $pdf->Cell(25, 10, 'Total', 1, 1, 'C');

    $no = 1;
    foreach ($dataSiswa as $s) {
        $pdf->SetFont('Arial', '', 8);
        $words = explode(' ', $s['nama']);
        $namaTrimmed = count($words) > 2 ? implode(' ', array_slice($words, 0, 2)) . '...' : $s['nama'];
        $pdf->Cell(10, 6, $no++, 1);
        $pdf->Cell(15, 6, $s['nis'], 1);
        $pdf->Cell(35, 6, $namaTrimmed, 1);
        foreach (array_keys($bulanMap) as $bln) {
            $pdf->Cell(16, 6, number_format($s['bulan'][$bln]), 1, 0, 'R');
        }
        $pdf->Cell(25, 6, number_format($s['total']), 1, 1, 'R');
    }

    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(252, 6, 'Total Pembayaran Kelas', 1, 0, 'C');
    $pdf->Cell(25, 6, number_format($totalKelas), 1, 1, 'R');
    $pdf->Ln(6);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(95, 6, 'Mengetahui,', 0, 0, 'L');
    $pdf->Cell(175, 6, $nama_kota . ', ' . $tanggal_cetak, 0, 1, 'R');
    $pdf->Cell(95, 6, 'Kepala Sekolah', 0, 0, 'L');
    $pdf->Cell(175, 6, 'Dicetak oleh,', 0, 1, 'R');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(95, 6, $kepsek, 0, 0, 'L');
    $pdf->Cell(175, 6, $nama_user, 0, 1, 'R');
    $pdf->Output();
    exit;
}

// === EXCEL ===
elseif (isset($_POST['excel_tahun'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Judul
    $sheet->setCellValue('A1', 'LAPORAN PEMBAYARAN SISWA');
    $sheet->mergeCells('A1:' . chr(65 + count($bulanMap) + 2) . '1');
    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

    // Subjudul
    $sheet->setCellValue('A2', "Tahun Ajaran: $thajaran");
    $sheet->mergeCells('A2:' . chr(65 + count($bulanMap) + 2) . '2');
    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    $sheet->setCellValue('A3', "Kelas: $rombel");
    $sheet->mergeCells('A3:' . chr(65 + count($bulanMap) + 2) . '3');
    $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

    // Header Tabel
    $header = ['No', 'NIS', 'Nama Siswa'];
    $header = array_merge($header, array_keys($bulanMap), ['Total']);

    $col = 'A';
    foreach ($header as $h) {
        $sheet->setCellValue($col . '5', $h);
        $sheet->getStyle($col . '5')->getFont()->setBold(true);
        $sheet->getStyle($col . '5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($col . '5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD9D9D9'); // abu-abu terang
        $col++;
    }
    $rowNum = 6;
    $no = 1;
    foreach ($dataSiswa as $s) {
        $col = 'A';
        $sheet->setCellValue($col++ . $rowNum, $no++);
        $sheet->setCellValue($col++ . $rowNum, $s['nis']);
        $sheet->setCellValue($col++ . $rowNum, $s['nama']);
        foreach (array_keys($bulanMap) as $bln) {
            $sheet->setCellValue($col++ . $rowNum, $s['bulan'][$bln]);
        }
        $sheet->setCellValue($col++ . $rowNum, $s['total']);
        $rowNum++;
    }
    
    // Total Kelas
    $sheet->setCellValue('A' . $rowNum, 'TOTAL KELAS');
    $sheet->mergeCells("A$rowNum:" . chr(65 + count($bulanMap) + 1) . "$rowNum");
    $sheet->setCellValue(chr(65 + count($bulanMap) + 2) . $rowNum, $totalKelas);
    $sheet->getStyle("A$rowNum:" . chr(65 + count($bulanMap) + 2) . "$rowNum")->getFont()->setBold(true);
    
    // Border seluruh tabel
    $lastCol = chr(65 + count($bulanMap) + 2); // termasuk kolom total
    $lastRow = $rowNum;
    
    // Menambahkan border dari header sampai total kelas
    $sheet->getStyle("A5:$lastCol$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
    // Output
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"laporan_pembayaran_$rombel.xlsx\"");
    $writer = new Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}

