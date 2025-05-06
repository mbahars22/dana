<?php
require 'vendor/fpdf/fpdf.php';
require 'vendor/phpqrcode/qrlib.php'; // Tambahkan ini
require 'functions.php';

// Pastikan parameter tersedia
if (!isset($_GET['nis']) || !isset($_GET['kd_transaksi'])) {
    die("Parameter tidak lengkap!");
}

$nis = $_GET['nis'];
$kd_transaksi = $_GET['kd_transaksi'];

// Generate QR Code dari kd_transaksi
$qr_text = $kd_transaksi;
$qr_file = 'temp_qr_' . $kd_transaksi . '.png';
QRcode::png($qr_text, $qr_file, QR_ECLEVEL_L, 3);

$query = "
    SELECT * 
    FROM pembayaran_siswa 
    WHERE nis = ? AND kd_transaksi = ?
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $nis, $kd_transaksi);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Ambil data sekolah
$query_sekolah = "SELECT nama_sekolah, status, alamat FROM tb_profile LIMIT 1";
$result_sekolah = mysqli_query($conn, $query_sekolah);
$sekolah = mysqli_fetch_assoc($result_sekolah);

// Ambil data siswa
mysqli_data_seek($result, 0);
$siswa = mysqli_fetch_assoc($result);

function terbilang($angka) {
    $angka = abs($angka);
    $bilangan = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    if ($angka < 12) return " " . $bilangan[$angka];
    elseif ($angka < 20) return terbilang($angka - 10) . " Belas";
    elseif ($angka < 100) return terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
    elseif ($angka < 200) return " Seratus" . terbilang($angka - 100);
    elseif ($angka < 1000) return terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
    elseif ($angka < 2000) return " Seribu" . terbilang($angka - 1000);
    elseif ($angka < 1000000) return terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
    elseif ($angka < 1000000000) return terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
    elseif ($angka < 1000000000000) return terbilang($angka / 1000000000) . " Miliar" . terbilang($angka % 1000000000);
    else return "Angka terlalu besar";
}

$pdf = new FPDF('L', 'mm', array(99, 200));
$pdf->SetAutoPageBreak(true, 5); // <--- di sini bro
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);  // Arial, Bold, ukuran 12

// Header$pdf->Cell(190, 35, '', 0, 1, 'C');
$pdf->SetXY(15, 8);
$pdf->Cell(50, 5, strtoupper($sekolah['nama_sekolah']), 0, 2, 'L');
$pdf->Cell(50, 5, strtoupper($sekolah['status']), 0, 2, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(50, 5, ucwords(strtolower($sekolah['alamat'])), 0, 'L');

$pdf->SetXY(70, 10);
$pdf->SetFont('Arial', '', 11);
// $pdf->MultiCell(40, 10, "KWITANSI\nPEMBAYARAN", 0, 0, 'C');
$pdf->MultiCell(30, 5, "KWITANSI\nPEMBAYARAN", 0, 'C');
$pdf->SetXY(120, 8);
$pdf->SetFont('Arial','',  10);
$pdf->Cell(20, 5, "No. Trn", 0, 0, 'L');
$pdf->Cell(30, 5, ': '.$siswa['kd_transaksi'], 0, 1, 'L');
$pdf->SetX(120);
$pdf->Cell(20, 5, "N I S", 0, 0, 'L');
$pdf->Cell(30, 5,': '. $siswa['nis'], 0, 1, 'L');
$pdf->SetX(120);
$pdf->SetFont('Arial', '',  8);
$pdf->Cell(20, 5, "Nama ", 0, 0, 'L');
$pdf->Cell(30, 5, ': '.$siswa['nama'], 0, 1, 'L');
$pdf->SetFont('Arial', '',  10);
$pdf->SetX(120);
$pdf->Cell(20, 5, "Kelas", 0, 0, 'L');
$pdf->Cell(30, 5, ': '.$siswa['kelas'], 0, 1, 'L');

$pdf->Ln(3);

// Table header
$pdf->SetFont('Arial', '',  8);
$pdf->Cell(10, 6, "NO", 'TB', 0, 'C');
$pdf->Cell(32, 6, "PEMBAYARAN", 'TB', 0, 'C');
$pdf->Cell(25, 6, "TH AJARAN", 'TB', 0, 'C');
$pdf->Cell(30, 6, "TAGIHAN", 'TB', 0, 'C');
$pdf->Cell(30, 6, "JML BAYAR", 'TB', 0, 'C');
$pdf->Cell(30, 6, "SISA / KET", 'TB', 0, 'C');
$pdf->Cell(20, 6, "METHOD", 'TB', 1, 'L');
$pdf->SetFont('Arial', '', 8);

$total_bayar = 0;
$no = 1;
mysqli_data_seek($result, 0);

while ($row = mysqli_fetch_assoc($result)) {
    $query_sisa = "SELECT jumlah FROM biaya_siswa WHERE nis = ? AND kd_biaya = ?";
    $stmt_sisa = mysqli_prepare($conn, $query_sisa);
    mysqli_stmt_bind_param($stmt_sisa, "ss", $nis, $row['kd_biaya']);
    mysqli_stmt_execute($stmt_sisa);
    $result_sisa = mysqli_stmt_get_result($stmt_sisa);
    $biaya = mysqli_fetch_assoc($result_sisa);
    $sisa = $biaya['jumlah'] ?? 0;

    $pdf->Cell(10, 5, $no++, '0', 0, 'C');
    $pdf->Cell(32, 5, $row['kd_biaya'], '0', 0, 'C');
    $pdf->Cell(26, 5, $row['thajaran'], '0', 0, 'C');
    $pdf->Cell(28, 5, number_format($row['jumlah'], 0, ',', '.'), '0', 0, 'R');
    $pdf->Cell(28, 5, number_format($row['bayar'], 0, ',', '.'), '0', 0, 'R');
    $pdf->Cell(28, 5, ($sisa == 0) ? "Lunas" : number_format($sisa, 0, ',', '.'), '0', 0, 'R');
    $pdf->Cell(28, 5, $row['method'], '0', 1, 'C');
    $total_bayar += $row['bayar'];
}

// Tambah baris kosong
$row_count = mysqli_num_rows($result);
while ($row_count < 5) {
    $pdf->Cell(10, 5, '', '0', 0);
    $pdf->Cell(60, 5, '', '0', 0);
    $pdf->Cell(50, 5, '', '0', 0);
    $pdf->Cell(50, 5, '', '0', 0);
    $pdf->Cell(40, 5, '', '0', 0);
    $pdf->Cell(40, 5, '', '0', 1);
    $row_count++;
}

$pdf->SetFont('Arial','',  10);
$pdf->Cell(126, 7, "TOTAL BAYAR", 'TB', 0, 'R');
$pdf->Cell(50, 7, number_format($total_bayar, 0, ',', '.'), 'TB', 1, 'R');

$pdf->Ln(2);

// Terbilang
$xTerbilang = $pdf->GetX();
$yTerbilang = $pdf->GetY();
$pdf->MultiCell(100, 5, "Terbilang: " . trim(terbilang($total_bayar)) . " Rupiah", 1, 'L');
$yAkhirTerbilang = $pdf->GetY();

// Tanggal dan user
$pdf->SetXY($xTerbilang + 100, $yTerbilang);
$pdf->Cell(90, 5, "Tgl: " . date('d-m-Y', strtotime($siswa['tgl_trans'])), 0, 1, 'C');
$pdf->Cell(155, 3, "Penerima", 0, 1, 'R');
$pdf->Ln(5);
$pdf->Cell(100, 5, " ", 0, 0);
$pdf->Cell(90, 5, $siswa['user'], 0, 1, 'C');

// Tambahkan QR Code
// $pdf->Image($qr_file, 10, $yAkhirTerbilang + 5, 10, 10);
$qr_x = 120; // Adjust sesuai kebutuhan, agar sejajar tanda tangan
$qr_y = $yTerbilang + 1; // Biar posisinya sejajar horizontal
$pdf->Image($qr_file, $qr_x, $qr_y, 15, 15);
// Hapus file QR sementara
if (file_exists($qr_file)) {
    unlink($qr_file);
}

// Output
$namsis = $siswa['nama'];
$nis = $siswa['nis'];
$kd_transaksi = $siswa['kd_transaksi'];
$nama_file = "struk_{$namsis}_{$nis}_{$kd_transaksi}.pdf";

$pdf->Output("I", $nama_file);
// $pdf->Output("I", "struk_pembayaran.pdf");
?>
