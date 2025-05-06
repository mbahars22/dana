<?php
include 'koneksi.php'; // Pastikan file koneksi database sudah benar

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_transaksi = $_POST['kd_transaksi'];
    $tgl_transaksi = $_POST['tanggalInput'];
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $total = $_POST['totalBayarInput'];

    // Ambil data detail transaksi dari form (array)
    $jenis_pembayaran = $_POST['textbayar']; // Array jenis biaya
    $jumlah = $_POST['jumlahText']; // Array jumlah pembayaran

    // Mulai transaksi database
    mysqli_begin_transaction($conn);
    try {
        for ($i = 0; $i < count($jenis_pembayaran); $i++) {
            $nama_bayar = $jenis_pembayaran[$i];
            $jml = $jumlah[$i];

            // Simpan langsung ke transaksi_filet
            $query = "INSERT INTO transaksi_filet (kd_transaksi, tgl_transaksi, nis, nama, kelas, jenis_transaksi, jumlah, keterangan, created_at) 
                      VALUES ('$kd_transaksi', '$tgl_transaksi', '$nis', '$nama', '$kelas', '$nama_bayar', '$jml', 'Lunas', NOW())";
            mysqli_query($conn, $query);
        }

        // Commit transaksi jika sukses
        mysqli_commit($conn);
        echo "<script>alert('Transaksi berhasil disimpan!'); window.location='index.php';</script>";
    } catch (Exception $e) {
        // Rollback jika ada error
        mysqli_rollback($conn);
        echo "<script>alert('Terjadi kesalahan saat menyimpan transaksi!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Metode tidak valid!'); window.location='index.php';</script>";
}
?>
