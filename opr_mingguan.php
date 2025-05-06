<?php
include 'koneksi.php';


$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$query = "SELECT nis, nama, kelas, kd_biaya, bayar, kd_transaksi, tgl_trans 
          FROM pembayaran_siswa 
          WHERE DATE(tgl_trans) BETWEEN '$start_date' AND '$end_date'";

$result = mysqli_query($conn, $query);

echo "<table class='table table-bordered table-striped'>
        <thead class='table-dark'>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Kode Biaya</th>
                <th>Bayar</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['nis']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['kelas']}</td>
            <td>{$row['kd_biaya']}</td>
            <td>Rp " . number_format($row['bayar'], 0, ',', '.') . "</td>
            <td>{$row['kd_transaksi']}</td>
            <td>" . date('d-m-Y', strtotime($row['tgl_trans'])) . "</td>
          </tr>";
}

echo "</tbody></table>";
?>
