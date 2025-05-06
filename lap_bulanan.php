<?php
session_start();
include 'koneksi.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data umum
$thajaran = $_SESSION['thajaran'];
$kategori = $_POST['kategori_bulan'];
$bulan_post = isset($_POST['bulan']) ? $_POST['bulan'] : date('Y-m');
list($tahun, $bulan) = explode('-', $bulan_post);

$profil = $conn->query("SELECT * FROM tb_profile LIMIT 1")->fetch_assoc();
$nama_sekolah = $profil['nama_sekolah'];
$status = $profil['status'];
$alamat = $profil['alamat'];
$kepsek = $profil['kep_sek'];
$nama_kota = $profil['kota'];

$sql = "SELECT 
            p.tgl_trans, 
            p.nis, 
            p.nama, 
            p.kelas, 
            p.bayar, 
            p.kd_transaksi, 
            p.kd_biaya, 
            p.method,
            k.nm_biaya
        FROM pembayaran_siswa p
        LEFT JOIN tb_kd_biaya k ON LEFT(p.kd_biaya, 3) = k.kd_biaya
        WHERE DATE_FORMAT(p.tgl_trans, '%Y-%m') = '$tahun-$bulan'
          AND p.thajaran = '$thajaran'
          AND LEFT(p.kd_biaya, 3) = '$kategori'
        ORDER BY p.tgl_trans ASC";
$result = $conn->query($sql);

// === Style Definitions ===

// Definisikan gaya untuk judul
$judulStyle = [
    'font' => [
        'bold' => true,
        'size' => 14,
        'name' => 'Arial'
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    ]
];

// Definisikan gaya untuk header tabel
$headerStyle = [
    'font' => [
        'bold' => true,
        'size' => 12,
        'name' => 'Arial'
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        ]
    ]
];

// Cek tombol yang ditekan
if (isset($_POST['excel_bulan'])) {
    // === EXPORT EXCEL ===
   
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

   // Menambahkan Judul Laporan
   $sheet->mergeCells('A1:I1');
   $sheet->setCellValue('A1', strtoupper($nama_sekolah));
   $sheet->mergeCells('A2:I2');
   $sheet->setCellValue('A2', $status);
   $sheet->mergeCells('A3:I3');
   $sheet->setCellValue('A3', $alamat);
   $sheet->mergeCells('A4:I4');
   $sheet->setCellValue('A4', 'LAPORAN PEMBAYARAN SISWA BULANAN');
   $sheet->mergeCells('A5:I5');
   $sheet->setCellValue('A5', 'Tahun Ajaran: ' . $thajaran);
   $sheet->mergeCells('A6:I6');
   $sheet->setCellValue('A6', 'Bulan: ' . $bulan_post . ' ' . $tahun);

   // Mengatur gaya header
   $sheet->getStyle('A1')->applyFromArray($judulStyle);
   $sheet->getStyle('A2')->applyFromArray($judulStyle);
   $sheet->getStyle('A3')->applyFromArray($judulStyle);
   $sheet->getStyle('A4')->applyFromArray($judulStyle);
   $sheet->getStyle('A5')->applyFromArray($judulStyle);
   $sheet->getStyle('A6')->applyFromArray($judulStyle);

   // Header kolom
   $sheet->setCellValue('A7', 'No');
   $sheet->setCellValue('B7', 'Tanggal');
   $sheet->setCellValue('C7', 'Kd Trans');
   $sheet->setCellValue('D7', 'NIS');
   $sheet->setCellValue('E7', 'Nama Siswa');
   $sheet->setCellValue('F7', 'Kelas');
   $sheet->setCellValue('G7', 'Kd Tagihan');
   $sheet->setCellValue('H7', 'Keterangan');
   $sheet->setCellValue('I7', 'Metode');
   $sheet->setCellValue('J7', 'Jumlah Bayar');

   // Mengatur gaya header tabel
   $sheet->getStyle('A7:J7')->applyFromArray($headerStyle);

   // Mengisi data dari DB
   $row = 8;
   $no = 1;
   $total_all = 0; // Inisialisasi total semua pembayaran

   while ($d = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $d['tgl_trans']);
    $sheet->setCellValue('C' . $row, $d['kd_transaksi']);
    $sheet->setCellValue('D' . $row, $d['nis']);
    $sheet->setCellValue('E' . $row, $d['nama']);
    $sheet->setCellValue('F' . $row, $d['kelas']);
    $sheet->setCellValue('G' . $row, $d['kd_biaya']);
    $sheet->setCellValue('H' . $row, $d['nm_biaya']);
    $sheet->setCellValue('I' . $row, $d['method']);
    $sheet->setCellValue('J' . $row, $d['bayar']);

    // Format angka
    $sheet->getStyle('J' . $row)->getNumberFormat()
        ->setFormatCode('#,##0');

    $total_all += $d['bayar'];
    $row++;
}

// Baris total langsung menyambung
$sheet->setCellValue('I' . $row, 'TOTAL BULAN INI');
$sheet->setCellValue('J' . $row, $total_all);
$sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');

$sheet->getStyle("A$row:J$row")->applyFromArray([
  'borders' => [
      'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
  ],
  'font' => ['bold' => true]
]);
   // Set lebar kolom otomatis
   foreach (range('A', 'J') as $col) {
       $sheet->getColumnDimension($col)->setAutoSize(true);
   }

   // Header tambahan untuk download Excel
   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
   header('Content-Disposition: attachment;filename="laporan_pembayaran_bulanan.xlsx"');
   header('Cache-Control: max-age=0');

   $writer = new Xlsx($spreadsheet);
   $writer->save('php://output');
   exit;


} elseif (isset($_POST['cetak_bulan'])) {
    // === CETAK PDF ===
    require('vendor/fpdf/fpdf.php');

    $profil = $conn->query("SELECT * FROM tb_profile LIMIT 1")->fetch_assoc();
    $nama_sekolah = $profil['nama_sekolah'];
    $status = $profil['status'];
    $alamat = $profil['alamat'];
    $kepsek = $profil['kep_sek'];
    $nama_kota = $profil['kota'];

    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, strtoupper($nama_sekolah), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, $status, 0, 1, 'C');
    $pdf->Cell(0, 5, $alamat, 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 7, 'LAPORAN PEMBAYARAN SISWA BULANAN', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Tahun Ajaran: ' . $thajaran, 0, 1, 'C');

    $bulanArray = [
      '01' => 'JANUARI', '02' => 'FEBRUARI', '03' => 'MARET', '04' => 'APRIL',
      '05' => 'MEI', '06' => 'JUNI', '07' => 'JULI', '08' => 'AGUSTUS',
      '09' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DESEMBER'
    ];
    $nama_bulan = isset($bulanArray[$bulan]) ? $bulanArray[$bulan] : $bulan;

    $pdf->Cell(0, 5, 'Bulan: ' . $nama_bulan . ' ' . $tahun, 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 8, 'No', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Kd Trans', 1, 0, 'C');
    $pdf->Cell(20, 8, 'NIS', 1, 0, 'C');
    $pdf->Cell(50, 8, 'Nama Siswa', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Kelas', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Kd Tagihan', 1, 0, 'C');
    $pdf->Cell(35, 8, 'Keterangan', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Metode', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Jumlah Bayar', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 8);
    $no = 1;
    $total_all = 0;
    $result->data_seek(0); // ulangi pointer karena sudah dipakai di atas
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(15, 8, $no++, 1, 0, 'C');
        $pdf->Cell(20, 8, date('d-m-Y', strtotime($row['tgl_trans'])), 1, 0, 'C');
        $pdf->Cell(30, 8, $row['kd_transaksi'], 1, 0, 'C');
        $pdf->Cell(20, 8, $row['nis'], 1, 0, 'C');
        $nama = $row['nama'];
        if (mb_strlen($nama) > 25) {
            $nama = mb_substr($nama, 0, 20) . '...';
        }
        $pdf->Cell(50, 8, $nama, 1, 0);
        $pdf->Cell(25, 8, $row['kelas'], 1, 0, 'C');
        $pdf->Cell(30, 8, $row['kd_biaya'], 1, 0, 'C');
        $pdf->Cell(35, 8, $row['nm_biaya'], 1, 0, 'C');
        $pdf->Cell(20, 8, $row['method'], 1, 0, 'C');
        $pdf->Cell(30, 8, 'Rp ' . number_format($row['bayar'], 0, ',', '.'), 1, 1, 'R');
        $total_all += $row['bayar'];
    }

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(245, 8, 'TOTAL BULAN INI', 1, 0, 'C');
    $pdf->Cell(30, 8, 'Rp ' . number_format($total_all, 0, ',', '.'), 1, 1, 'R');

    $nama_user = isset($_SESSION['user']) ? $_SESSION['user'] : 'User';
    $tanggal_cetak = date('d-m-Y');
    $pdf->Ln(5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(120, 6, 'Mengetahui,', 0, 0, 'L');
    $pdf->Cell(140, 6, $nama_kota . ', ' . $tanggal_cetak, 0, 1, 'R');
    $pdf->Cell(120, 6, 'Kepala Sekolah', 0, 0, 'L');
    $pdf->Cell(140, 6, 'Dicetak oleh,', 0, 1, 'R');
    $pdf->Ln(18);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(120, 6, $kepsek, 0, 0, 'L');
    $pdf->Cell(140, 6, $nama_user, 0, 1, 'R');

    $pdf->Output('I', 'laporan_pembayaran_bulanan.pdf');
}
?>
