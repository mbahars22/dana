<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_transaksi = $_POST['kd_transaksi'];
    $kd_biaya = $_POST['kd_biaya'];
    $nis = $_POST['nis'];
    $thajaran = $_POST['thajaran'];

    // Ambil data yang akan dihapus
    $cek = mysqli_query($conn, "SELECT * FROM pembayaran_siswa WHERE kd_biaya = '$kd_biaya' AND nis = '$nis' LIMIT 1");
    $data = mysqli_fetch_assoc($cek);

    if ($data) {
        $thajaran = $data['thajaran'];
        $bayar = $data['bayar'];

        // Hapus data dari pembayaran_siswa
        $hapus = mysqli_query($conn, "DELETE FROM pembayaran_siswa WHERE kd_biaya = '$kd_biaya' AND nis = '$nis'");

        if ($hapus) {
            // Kembalikan dana ke biaya_siswa
            $update = mysqli_query($conn, "UPDATE biaya_siswa 
                                           SET jumlah = jumlah + $bayar 
                                           WHERE nis = '$nis' AND kd_biaya = '$kd_biaya' AND thajaran = '$thajaran'");

            if ($update) {
                echo "Data berhasil dihapus dan dana berhasil dikembalikan.";
            } else {
                echo "Data dihapus, tapi gagal mengembalikan dana.";
            }
        } else {
            echo "Gagal menghapus data.";
        }
    } else {
        echo "Data tidak ditemukan.";
    }
}
?>
