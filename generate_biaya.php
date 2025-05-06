<?php
include 'koneksi.php';

if(isset($_POST['nis'])) {
    $nis = $_POST['nis'];

    // Cek apakah siswa sudah punya biaya
    $cek = mysqli_query($conn, "SELECT * FROM biaya_siswa WHERE nis = '$nis'");
    
    if(mysqli_num_rows($cek) == 0) {
        // Jika belum ada, tambahkan biaya berdasarkan jenis_biaya
        $insertQuery = "INSERT INTO biaya_siswa (nis, kd_biaya, jumlah)
                        SELECT '$nis', kd_biaya, jumlah FROM jenis_transaksi";
        mysqli_query($conn, $insertQuery);
    }

    // Ambil kembali data biaya siswa
    $result = mysqli_query($conn, "SELECT jb.nama_biaya, bs.jumlah 
                                   FROM biaya_siswa bs 
                                   JOIN jenis_transaksi jb ON bs.kd_biaya = jb.kd_biaya 
                                   WHERE bs.nis = '$nis'");

    if(mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>{$row['nama_biaya']}</td><td>Rp. ".number_format($row['jumlah'])."</td></tr>";
        }
    } else {
        echo "<tr><td colspan='2' class='text-center'>Biaya tidak ditemukan</td></tr>";
    }
}
?>
