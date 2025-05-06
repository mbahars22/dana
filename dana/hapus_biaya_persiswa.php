<?php
include 'koneksi.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM biaya_siswa WHERE nis='$id'");
header("Location: tampil_biaya.php"); // Ganti sesuai nama halaman lo
?>