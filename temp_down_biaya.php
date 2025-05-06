<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'vendor/autoload.php';
require 'koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Ambil tahun ajaran dari session
$full_thajaran = $_SESSION['thajaran'] ?? '';
$thajaran_singkat = substr($full_thajaran, 2, 2) . '/' . substr($full_thajaran, -2);

// Ambil data siswa
$sql_siswa = mysqli_query($conn, "SELECT nis, nama, kelas, rombel FROM siswa WHERE thajaran = '$full_thajaran'");
$data_siswa = [];
while ($row = mysqli_fetch_assoc($sql_siswa)) {
    $data_siswa[] = $row;
}

// Ambil data jenis transaksi
$sql_transaksi = mysqli_query($conn, "SELECT kd_biaya, volume, kelas, jumlah FROM jenis_transaksi WHERE th_ajaran = '$thajaran_singkat'");
$data_transaksi = [];
while ($row = mysqli_fetch_assoc($sql_transaksi)) {
    $data_transaksi[] = $row;
}

// Inisialisasi Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = ['NIS', 'Nama', 'rombel', 'Kode Biaya', 'Jumlah', 'Tahun Ajaran'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Gaya header
$headerStyle = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Freeze header
$sheet->freezePane('A2');

// Daftar bulan akademik
$bulanList = ['JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES', 'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN'];
$tahunAwal = substr($full_thajaran, 2, 2);  // contoh: 24
$tahunAkhir = substr($full_thajaran, -2);  // contoh: 25

$rowIndex = 2;
foreach ($data_siswa as $siswa) {
    foreach ($data_transaksi as $transaksi) {
        if ($siswa['kelas'] == $transaksi['kelas']) {
            for ($i = 0; $i < $transaksi['volume']; $i++) {
                $bulan = $bulanList[$i];
                $tahun = ($i < 6) ? $tahunAwal : $tahunAkhir;

                $kd_biaya_final = $transaksi['kd_biaya'];
                if ($transaksi['volume'] > 1) {
                    $kd_biaya_final .= '-' . $bulan . '-' . $tahun;
                }

                $sheet->setCellValue("A{$rowIndex}", $siswa['nis']);
                $sheet->setCellValue("B{$rowIndex}", $siswa['nama']);
                $sheet->setCellValue("C{$rowIndex}", $siswa['rombel']);
                $sheet->setCellValue("D{$rowIndex}", $kd_biaya_final);
                $sheet->setCellValue("E{$rowIndex}", $transaksi['jumlah']);
                $sheet->setCellValue("F{$rowIndex}", $full_thajaran);
                $rowIndex++;
            }
        }
    }
}

// Otomatis lebar kolom
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Tambahkan border ke semua isi
$dataStyle = [
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
];
$sheet->getStyle("A2:F" . ($rowIndex - 1))->applyFromArray($dataStyle);

// Rata tengah untuk kolom tertentu
$sheet->getStyle("A2:A{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("C2:C{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("D2:D{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("F2:F{$rowIndex}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Output
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="template_biaya_siswa.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
