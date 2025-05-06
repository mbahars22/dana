<?php
include "koneksi.php"; // Pastikan file koneksi terhubung

if (isset($_POST['thajaran'])) {
    $th_ajaran = $_POST['thajaran'];

    // Eksekusi query hapus
    $query = "DELETE FROM siswa WHERE thajaran = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $th_ajaran);

    if ($stmt->execute()) {
        echo "<script>alert('Data siswa dengan tahun ajaran $th_ajaran berhasil dihapus!'); window.location.href='tampil_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='tampil_siswa.php';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Tahun ajaran tidak ditemukan!'); window.location.href='tampil_siswa.php';</script>";
}
