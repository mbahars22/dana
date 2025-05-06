<?php

include 'koneksi.php';
include  'sidebar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi</title>
    <!-- Tambahin link Bootstrap kalau belum ada -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS tambahan -->
</head>
<body>

<div class="container mt-3 ml-4">
    <div class="row">
        <!-- Kolom kiri kosong -->
        <div class="col-md-2"></div>

        <!-- Kolom utama -->
        <div class="col-10">
            <!-- Baris judul -->
            <!-- <div class="row align-items-center mb-1">
                <div class="col-md-6">
                    <h3 class="m-0">Data Transakai</h3>
                </div>
            </div> -->

            <!-- Konten detail transaksi -->
            <div class="row">
                <div class="col-md-12">

<?php
if (isset($_GET['kd_transaksi'])) {
    $kd = $_GET['kd_transaksi'];

    $q = mysqli_query($conn, "SELECT * FROM pembayaran_siswa WHERE kd_transaksi='$kd'");
    if (mysqli_num_rows($q) > 0) {
        echo "<h4>Data Transaksi Yang Akan Dihapus</h4>";
        echo "<form method='POST' action='proses_batal.php'>";
        echo "<input type='hidden' name='kd_transaksi' value='$kd'>";
        echo "<table class='table table-bordered'>";
        echo "<thead class='thead-dark'><tr>
        <th>NIS</th>
        <th>Nama</th>
        <th>Kelas</th>
        <th>Kode Biaya</th>
        <th>Tahun Ajaran</th>
        <th>Jumlah Bayar</th>
        <th>Tanggal</th>
      </tr></thead><tbody>";


        while ($data = mysqli_fetch_assoc($q)) {
            echo "<tr>
                    <td>{$data['nis']}</td>
                    <td>{$data['nama']}</td>
                    <td>{$data['kelas']}</td>
                    <td>{$data['kd_biaya']}</td>
                    <td>{$data['thajaran']}</td>
                    <td>Rp " . number_format($data['bayar'], 0, ',', '.') . "</td>
                    <td>{$data['tgl_trans']}</td>
                  </tr>";
        }

        echo "</tbody></table>";
        echo "<button type='submit' class='btn btn-danger' onclick='return confirm(\"Yakin ingin membatalkan transaksi ini?\")'>Batalkan Transaksi</button>";
        echo "</form>";
    } else {
        echo "<div class='alert alert-warning'>Data transaksi tidak ditemukan.</div>";
    }
}
?>

                </div>
            </div> <!-- Akhir row konten -->
        </div> <!-- Akhir kolom utama -->
    </div> <!-- Akhir row -->
</div> <!-- Akhir container -->

</body>
</html>
