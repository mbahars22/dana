<?php
include '../koneksi.php';
$nis = $_GET['nis'];

$sql = mysqli_query($conn, "
    SELECT * FROM biaya_siswa 
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

echo "<table class='table table-bordered table-sm' style='width:100%; font-size: 14px;'>";
echo "<thead class='table-light'>
        <tr>
            <th style='width: 60%; text-align: center;'>Kd Biaya</th>
            <th style='width: 40%; text-align: center;'>Nominal</th>
        </tr>
      </thead>";
echo "<tbody>";
while($r = mysqli_fetch_assoc($sql)) {
    echo "<tr>
            <td style='text-align: center;'>{$r['kd_biaya']}</td>
            <td style='text-align: right;'>Rp " . number_format($r['jumlah'], 0, ',', '.') . "</td>
          </tr>";
}
echo "</tbody></table>";

