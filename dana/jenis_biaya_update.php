<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $kd_biaya = $_POST['kd_biaya'];
    $volume = $_POST['volume'];
     $jumlah = $_POST['jumlah'];
    $thajaran = $_POST['thajaran'];

    $query = "UPDATE jenis_transaksi SET kd_biaya=?, volume=?, jumlah=?, thajaran=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssisi", $kd_biaya, $volume, $jumlah, $thajaran, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error: " . mysqli_error($conn);
    }
}
?>
