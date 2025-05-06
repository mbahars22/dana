<?php
include 'koneksi.php';

$kelas = $_GET['kelas'];
$query = mysqli_query($conn, "
    SELECT bs.id, jt.nama_biaya, bs.jumlah 
    FROM biaya_siswa bs 
    JOIN jenis_transaksi jt ON bs.kd_biaya = jt.kd_biaya
    WHERE bs.nis='$nis'
");

while ($biaya = mysqli_fetch_assoc($query)) {
    echo '<label>' . $biaya['nama_biaya'] . ':</label>';
    echo '<input type="hidden" name="id_biaya[]" value="' . $biaya['id'] . '">';
    echo '<input type="text" name="jumlah_bayar[]" placeholder="Jumlah bayar (Rp)" required><br><br>';
}
?>
