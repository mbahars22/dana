<?php
require '../vendor/fpdf/fpdf.php';
require '../vendor/phpqrcode/qrlib.php'; // Tambahkan ini
require '../functions.php';

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

// Ambil data pembayaran siswa
$query = "SELECT * FROM pembayaran_siswa WHERE nis = ? AND kd_transaksi = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $nis, $kd_transaksi);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Ambil data sekolah
$query_sekolah = "SELECT nama_sekolah, status, alamat FROM tb_profile LIMIT 1";
$result_sekolah = mysqli_query($conn, $query_sekolah);
$sekolah = mysqli_fetch_assoc($result_sekolah);
function terbilang($angka) {
    $angka = abs($angka);
    $bilangan = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    
    if ($angka < 12) {
        return " " . $bilangan[$angka];
    } elseif ($angka < 20) {
        return terbilang($angka - 10) . " Belas";
    } elseif ($angka < 100) {
        return terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
    } elseif ($angka < 200) {
        return " Seratus" . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        return terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        return " Seribu" . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        return terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        return terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
    } elseif ($angka < 1000000000000) {
        return terbilang($angka / 1000000000) . " Miliar" . terbilang($angka % 1000000000);
    } else {
        return "Angka terlalu besar";
    }
}
// Ambil data siswa
$siswa = mysqli_fetch_assoc($result);

$pdf = new FPDF('P', 'mm', array(95, 140));
$pdf->SetMargins(5, 5, 5); // Margin kiri, atas, kanan
$pdf->SetAutoPageBreak(true, 5); // Otomatis pindah halaman dengan margin bawah 5mm
$pdf->AddPage();
$pdf->SetFont('Courier', 'B', 10);

$pdf->Cell(95, 4, strtoupper($sekolah['nama_sekolah']), 0, 1, 'C');
$pdf->Cell(95, 4, strtoupper($sekolah['status']), 0, 1, 'C');
$pdf->SetFont('Courier', '', 8);
$pdf->MultiCell(95, 4, ucwords(strtolower($sekolah['alamat'])), 0, 'C');
$pdf->Ln(2);

$pdf->SetFont('Courier', 'B', 10);
$pdf->Cell(95, 6, "KWITANSI PEMBAYARAN", 0, 1, 'C');
$pdf->Ln(2);


$pdf->SetFont('Courier', '', 8);
$pdf->Cell(30, 5, "No. Trans", 0, 0, 'L');
$pdf->Cell(65, 4, $siswa['kd_transaksi'], 0, 1, 'L');
$pdf->Cell(30, 5, "NIS", 0, 0, 'L');
$pdf->Cell(65, 4, $siswa['nis'], 0, 1, 'L');
$pdf->Cell(30, 5, "Nama", 0, 0, 'L');
$pdf->Cell(65, 4, $siswa['nama'], 0, 1, 'L');
$pdf->Cell(30, 5, "Kelas", 0, 0, 'L');
$pdf->Cell(65, 4, $siswa['kelas'], 0, 1, 'L');
$pdf->Ln(3);

$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(8, 6, "No", 'TB', 0, 'C', false);
$pdf->Cell(18, 6, "Kd Biaya", 'TB', 0, 'C', false);
$pdf->Cell(16, 6, "Jumlah", 'TB', 0, 'C', false);
$pdf->Cell(16, 6, "Bayar", 'TB', 0, 'C', false);
$pdf->Cell(16, 6, "Sisa", 'TB', 0, 'C', false);
$pdf->Cell(10, 6, "Meth", 'TB', 1, 'L', false);
$pdf->SetFont('Courier', '', 10);
$total_bayar = 0;
$no = 1;

$pdf->SetFont('Courier', '', 8);
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

    $pdf->Cell(8, 5, $no++, '0', 0, 'C', false);
    $pdf->Cell(18, 5, $row['kd_biaya'], '0', 0, 'C', false);
    $pdf->Cell(16, 5, number_format($row['jumlah'], 0, ',', '.'), '0', 0, 'R', false);
    $pdf->Cell(16, 5, number_format($row['bayar'], 0, ',', '.'), '0', 0, 'R', false);
    $pdf->Cell(16, 5, number_format($sisa, 0, ',', '.'), '0', 0, 'R', false); 
    $pdf->Cell(10, 5, $row['method'],  '0', 1, 'C', false); // Ini kolom terakhir jadi pakai 1
    $total_bayar += $row['bayar'];
}
    $row_count = mysqli_num_rows($result); // Hitung jumlah baris hasil query
    while ($row_count < 5) {
        $pdf->Cell(10, 5, '', '0', 0, 'C', false);  
        $pdf->Cell(60, 5, '', '0', 0, 'C', false);  
        $pdf->Cell(50, 5, '', '0', 0, 'R', false);  
        $pdf->Cell(50, 5, '', '0', 0, 'R', false);  
        $pdf->Cell(40, 5, '', '0', 0, 'R', false); 
        $pdf->Cell(40, 5, '', '0', 1, 'R', false); 
        $row_count++;
    }
    $x = $pdf->GetX(); 
    $y = $pdf->GetY(); 
    
    $pdf->SetFont('Courier', 'B', 10);
    $pdf->Cell(50, 6, "TOTAL BAYAR", 'TB', 0, 'R', false);
    $pdf->Cell(35, 6, number_format($total_bayar, 0, ',', '.'), 'TB', 1, 'R', false);
    
    $pdf->Ln(2);
    
    // Simpan posisi X sebelum MultiCell
    $xTerbilang = $pdf->GetX();
    $yTerbilang = $pdf->GetY();
    $pdf->SetFont('Courier', 'i', 8);
    // MultiCell untuk teks panjang
    $pdf->MultiCell(45, 5, "Terbilang: " . trim(terbilang($total_bayar)) . " Rupiah", 1, 'L');
    
    // Dapatkan posisi Y terbaru setelah MultiCell
    $yAkhirTerbilang = $pdf->GetY();
    
    // Pindah ke posisi yang benar untuk tanggal
    $pdf->SetXY($xTerbilang + 50, $yTerbilang);
    $pdf->Cell(16, 5, "Tgl: " . date('d-m-Y', strtotime($siswa['tgl_trans'])), 0, 1, 'L');
    $pdf->Cell(70, 3, "Bendahara", 0, 1, 'R');
    
    // Tambah sedikit jarak
    $pdf->Ln(5);
    
    $pdf->Cell(50, 5, " ", 0, 0, 'C');
    $pdf->Cell(35, 5, $siswa['user'], 0, 1, 'L');
    
    // Tambahkan QR Code
        // $pdf->Image($qr_file, 10, $yAkhirTerbilang + 5, 10, 10);
        $qr_x = 80; // Adjust sesuai kebutuhan, agar sejajar tanda tangan
        $qr_y = $yTerbilang + 4; // Biar posisinya sejajar horizontal
        $pdf->Image($qr_file, $qr_x, $qr_y, 10, 10);
        // Hapus file QR sementara
        if (file_exists($qr_file)) {
            unlink($qr_file);
        }

    // Output PDF langsung dan buka di tab baru
    // $pdf->Output("I", "struk_pembayaran_kecil.pdf");
    $namsis = $siswa['nama'];
    $nis = $siswa['nis'];
    $kd_transaksi = $siswa['kd_transaksi'];
    $nama_file = "struk_{$namsis}_{$nis}_{$kd_transaksi}.pdf";

    $pdf->Output("I", $nama_file);
?>
