<?php
include '../koneksi.php';

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$query = "SELECT ps.nis, s.nama, s.rombel, ps.kd_biaya, ps.bayar, ps.kd_transaksi, ps.tgl_trans 
          FROM pembayaran_siswa ps
          JOIN siswa s ON ps.nis = s.nis
          WHERE DATE(ps.tgl_trans) = '$tanggal'";
$result = mysqli_query($conn, $query);

echo "<h5 class='mb-3'>Detail Pembayaran Hari Ini (" . date('d-m-Y') . ")</h5>";

echo "<div class='table-responsive'>
        <table class='table table-bordered table-striped'>
            <thead class='table-dark'>
                <tr>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Rombel</th>
                    <th>Kode Biaya</th>
                    <th>Bayar</th>
                    <th>Kode Transaksi</th>
                    <th>Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>";

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['nis']}</td>
                <td>{$row['nama']}</td>
                <td>{$row['rombel']}</td>
                <td>{$row['kd_biaya']}</td>
                <td>Rp " . number_format($row['bayar'], 0, ',', '.') . "</td>
                <td>{$row['kd_transaksi']}</td>
                <td>" . date('d-m-Y', strtotime($row['tgl_trans'])) . "</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>Tidak ada data pembayaran hari ini.</td></tr>";
}

echo "  </tbody>
        </table>
      </div>";
?>
