<?php
include 'koneksi.php';
$nis = $_GET['nis'];

$sql = mysqli_query($conn, "
    SELECT * FROM pembayaran_siswa 
    WHERE nis='$nis'
    ORDER BY 
        CASE SUBSTRING(kd_biaya, 8, 3)
            WHEN 'JUL' THEN 1
            WHEN 'AGU' THEN 2
            WHEN 'SEP' THEN 3
            WHEN 'OKT' THEN 4
            WHEN 'NOV' THEN 5
            WHEN 'DES' THEN 6
            WHEN 'JAN' THEN 7
            WHEN 'FEB' THEN 8
            WHEN 'MAR' THEN 9
            WHEN 'APR' THEN 10
            WHEN 'MEI' THEN 11
            WHEN 'JUN' THEN 12
            ELSE 13
        END
");

echo "<table class='table table-bordered table-sm'>
        <thead class='table-light'>
            <tr>
                <th>Kode Transaksi</th>
                <th>Kode Biaya</th>
                <th>Tahun Ajaran</th>
                <th>Tanggal Bayar</th>
                <th>Jumlah Bayar</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>";

while($r = mysqli_fetch_assoc($sql)) {
    echo "<tr>
            <td>{$r['kd_transaksi']}</td>
            <td>{$r['kd_biaya']}</td>
            <td>{$r['thajaran']}</td>
            <td>" . date('d-m-Y', strtotime($r['tgl_trans'])) . "</td>
            <td>Rp " . number_format($r['bayar'], 0, ',', '.') . "</td>
            <td>{$r['method']}</td>
          </tr>";
}

echo "</tbody></table>";

?>
