<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang dapat menghapus
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "error";
    exit();
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Hapus data siswa
    $query = "DELETE FROM jenis_transaksi WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        echo "error";
    }

    mysqli_stmt_close($stmt);
}
?>
