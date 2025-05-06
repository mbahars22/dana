<?php
include '../koneksi.php';

$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM biaya_siswa WHERE nis LIKE '%$search%' OR nama LIKE '%$search%'";
$data = mysqli_query($conn, $sql);

echo '<table class="table table-striped  mt-3">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Kd Kelas</th>
            <th>Kode Biaya</th>
            <th>Jumlah</th>
            <th>Th Ajaran</th>
           
        </tr>
    </thead>
    <tbody>';
    
$no = 1;
while ($row = mysqli_fetch_assoc($data)) {
    echo '<tr>
        <td>' . $no++ . '</td>
        <td>' . $row['nis'] . '</td>
        <td text-left>' . $row['nama'] . '</td>
        <td>' . $row['kelas'] . '</td>
        <td>' . $row['kd_biaya'] . '</td>
        <td align="right">Rp. ' . number_format($row['jumlah'], 0, ',', '.') . '</td>
        <td>' . $row['thajaran'] . '</td>
       
    </tr>';
}

echo '</tbody></table>';
?>
