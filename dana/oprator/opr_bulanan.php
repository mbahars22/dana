<?php
include '../koneksi.php';

// Ambil parameter bulan dari URL, misal "2025-04"
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

// Query ambil semua transaksi pada bulan tersebut
$query = "SELECT ps.nis, s.nama, s.rombel, ps.kd_biaya, ps.bayar, ps.kd_transaksi, ps.tgl_trans 
          FROM pembayaran_siswa ps
          JOIN siswa s ON ps.nis = s.nis
          WHERE DATE_FORMAT(ps.tgl_trans, '%Y-%m') = '$bulan'
          ORDER BY ps.tgl_trans ASC";

$result = mysqli_query($conn, $query);

// Tampilkan tabel
echo "<h5>Daftar Transaksi Bulan " . date('F Y', strtotime($bulan . "-01")) . "</h5>";
echo "<table class='table table-bordered table-striped'>
        <thead class='table-dark'>
            <tr>
                <th>No</th>
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

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$no}</td>
            <td>{$row['nis']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['rombel']}</td>
            <td>{$row['kd_biaya']}</td>
            <td>Rp " . number_format($row['bayar'], 0, ',', '.') . "</td>
            <td>{$row['kd_transaksi']}</td>
            <td>" . date('d-m-Y', strtotime($row['tgl_trans'])) . "</td>
          </tr>";
    $no++;
}

echo "</tbody></table>";
?>
