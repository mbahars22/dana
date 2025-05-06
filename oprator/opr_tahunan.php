<?php
// koneksi database
include '../koneksi.php';

// ambil tahun ajaran dari URL
$tahun_ajaran = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// query ambil data pembayaran berdasarkan thajaran
$query = "SELECT ps.nis, s.nama, s.rombel, ps.kd_biaya, ps.bayar, ps.kd_transaksi, ps.tgl_trans
          FROM pembayaran_siswa ps
          JOIN siswa s ON ps.nis = s.nis
          WHERE ps.thajaran = '$tahun_ajaran'
          ORDER BY ps.tgl_trans ASC";

$result = mysqli_query($conn, $query);

// tampilkan tabel
echo "<h3>Detail Transaksi Tahun Ajaran $tahun_ajaran</h3>";
echo "<table class='table table-bordered table-striped'>
        <thead>
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

echo "</tbody></table>";
?>
