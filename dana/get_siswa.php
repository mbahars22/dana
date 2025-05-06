<?php
include 'koneksi.php';

$nis = $_GET['nis'] ?? '';

$query = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
$data = mysqli_fetch_assoc($query);

if ($data) {
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Siswa tidak ditemukan"]);
}
?>
