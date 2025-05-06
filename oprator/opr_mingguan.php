<?php
// Koneksi database
include '../koneksi.php';

// Ambil tanggal start_date dan end_date dari URL
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d'); 
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); 

// Query untuk mendapatkan transaksi antara tanggal start_date dan end_date
$query = "SELECT ps.nis, s.nama, s.rombel, ps.kd_biaya, ps.bayar, ps.kd_transaksi, ps.tgl_trans 
          FROM pembayaran_siswa ps
          JOIN siswa s ON ps.nis = s.nis
          WHERE DATE(ps.tgl_trans) BETWEEN '$start_date' AND '$end_date'";

// Eksekusi query
$result = mysqli_query($conn, $query);

// Tampilkan data dalam bentuk tabel di dalam modal
echo "<h3>Detail Pembayaran Minggu Ini</h3>";
echo "<table class='table table-bordered table-striped'>
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
