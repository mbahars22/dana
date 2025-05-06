<?php
include 'koneksi.php';

if (isset($_POST['kd_transaksi'])) {
    $kd_transaksi = $_POST['kd_transaksi'];

    // Ambil semua data transaksi yang sesuai dengan kd_transaksi
    $query = mysqli_query($conn, "SELECT * FROM pembayaran_siswa WHERE kd_transaksi = '$kd_transaksi'");

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            $nis = $row['nis'];
            $kd_biaya = $row['kd_biaya'];
            $thajaran = $row['thajaran'];
            $jumlah_bayar = $row['bayar'];

            // 1. Kembalikan dana ke biaya_siswa
            $update = mysqli_query($conn, "UPDATE biaya_siswa 
                                            SET jumlah = jumlah + $jumlah_bayar 
                                            WHERE nis = '$nis' 
                                              AND kd_biaya = '$kd_biaya' 
                                              AND thajaran = '$thajaran'");

            if (!$update) {
                echo "Gagal mengembalikan dana untuk NIS $nis";
                exit;
            }
        }

        // 2. Hapus data dari pembayaran_siswa
        $hapus = mysqli_query($conn, "DELETE FROM pembayaran_siswa WHERE kd_transaksi = '$kd_transaksi'");

        if ($hapus) {
            echo "<script>
                alert('Transaksi berhasil dibatalkan!');
                window.location.href='admin_dashboard2.php';
            </script>";
        } else {
            echo "Gagal menghapus transaksi.";
        }

    } else {
        echo "Data transaksi tidak ditemukan.";
    }

} else {
    echo "Kode transaksi tidak dikirim.";
}
?>
