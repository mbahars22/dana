<?php
include 'koneksi.php'; // Sesuaikan dengan koneksi database

$bulan = $_GET['bulan'];
$tahun = $_GET['tahun'];

// Ambil kode transaksi terakhir di bulan & tahun yang sama
$query = "SELECT kd_transaksi FROM transaksi_filet 
          WHERE kd_transaksi LIKE '$bulan.$tahun.%' 
          ORDER BY kd_transaksi DESC LIMIT 1";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $kodeTerakhir = explode('.', $row['kd_transaksi'])[2]; // Ambil bagian nomor transaksi
    echo intval($kodeTerakhir); // Kirim sebagai angka
} else {
    echo 0; // Jika belum ada transaksi, mulai dari 0
}
?>
