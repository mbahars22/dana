<?php
include 'koneksi.php'; // Sesuaikan dengan koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $kd_biaya = $_POST['kd_biaya'];
    $volume = $_POST['volume'];
    $kelas = $_POST['kelas'];
    $nama_biaya = $_POST['nama_biaya'];
    $jumlah = $_POST['jumlah'];
    $th_ajaran = $_POST['th_ajaran'];

    $sql = "UPDATE jenis_transaksi SET kd_biaya=?, volume=?, kelas=?, nama_biaya=?, jumlah=?, th_ajaran=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    // $stmt->bind_param("ssssis", $kd_biaya, $volume, $kelas, $nama_biaya, $jumlah, $th_ajaran, $id);
    $stmt->bind_param("ssssisi", $kd_biaya, $volume, $kelas, $nama_biaya, $jumlah, $th_ajaran, $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>