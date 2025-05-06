<?php
include 'koneksi.php'; // Pastikan file koneksi tersedia

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kd_biaya = $_POST['kd_biaya'];
    $volume = $_POST['volume'];
    $kelas = $_POST['kelas'];
    // Konversi format Rupiah ke angka sebelum disimpan
    $jumlah = str_replace(['Rp.', '.', ','], '', $_POST['jumlah']);
    $th_ajaran = $_POST['th_ajaran'];
    // Query INSERT dengan prepared statement
    $query = "INSERT INTO jenis_transaksi (kd_biaya, volume, kelas, jumlah, th_ajaran) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sisis", $kd_biaya, $volume, $kelas, $jumlah, $th_ajaran);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('Data berhasil ditambahkan!');
                window.location.href = 'tampil_mst_biaya.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan data!');
                window.location.href = 'tampil_mst_biaya.php';
              </script>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
