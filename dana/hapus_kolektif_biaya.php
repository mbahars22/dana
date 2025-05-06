<?php
include 'koneksi.php';

if (isset($_POST['thajaran'])) {
    $thajaran = mysqli_real_escape_string($conn, $_POST['thajaran']);

    $query = "DELETE FROM biaya_siswa WHERE thajaran = '$thajaran'";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Data tahun ajaran $thajaran berhasil dihapus!'); window.location.href='tampil_biaya.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Tahun ajaran belum dipilih!'); window.history.back();</script>";
}
?>
