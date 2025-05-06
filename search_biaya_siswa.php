<?php
include 'koneksi.php';

$search = $_GET['search'] ?? '';

// Menggunakan prepared statement untuk mencegah SQL Injection
$sql = "SELECT * FROM biaya_siswa WHERE nis LIKE ? OR nama LIKE ?";
$stmt = mysqli_prepare($conn, $sql);
$searchTerm = "%" . $search . "%";
mysqli_stmt_bind_param($stmt, 'ss', $searchTerm, $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

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
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo '<tr>
        <td>' . $no++ . '</td>
        <td>' . $row['nis'] . '</td>
        <td>' . $row['nama'] . '</td>
        <td>' . $row['kelas'] . '</td>
        <td>' . $row['kd_biaya'] . '</td>
        <td align="right">Rp. ' . number_format($row['jumlah'], 0, ',', '.') . '</td>
        <td>' . $row['thajaran'] . '</td>
        <td>
            <a href="hapus_biaya_persiswa.php?id=' . $row['nis'] . '" 
            onclick="return confirm(\'Yakin ingin menghapus data ini? NIS : ' . $row['nis'] . ' nama : ' . $row['nama'] . '\')" 
            class="btn btn-danger btn-sm">
                Hapus
            </a>
        </td>
    </tr>';
}

echo '</tbody></table>';
?>
