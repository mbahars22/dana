<?php
include 'koneksi.php'; // Pastikan ini berisi $conn

$nis = isset($_GET['nis']) ? mysqli_real_escape_string($conn, $_GET['nis']) : '';

if (empty($nis)) {
    echo "<p class='text-danger'>NIS tidak diberikan.</p>";
    exit;
}

// Ambil nama siswa
$sql_siswa = "SELECT nama FROM biaya_siswa WHERE nis='$nis' LIMIT 1";
$result_siswa = mysqli_query($conn, $sql_siswa);

if ($result_siswa && mysqli_num_rows($result_siswa) > 0) {
    $row_siswa = mysqli_fetch_assoc($result_siswa);
    $nama = $row_siswa['nama'];
} else {
    echo "<p class='text-danger'>Data siswa tidak ditemukan.</p>";
    exit;
}

// Ambil total biaya yang belum terbayarkan
$sql_total = "SELECT SUM(jumlah) AS total_biaya FROM biaya_siswa WHERE nis='$nis'";
$result_total = mysqli_query($conn, $sql_total);
$total_biaya = 0;

if ($result_total && mysqli_num_rows($result_total) > 0) {
    $row_total = mysqli_fetch_assoc($result_total);
    $total_biaya = $row_total['total_biaya'];
}

// Ambil detail biaya siswa
$sql = "SELECT kd_biaya, jumlah, thajaran FROM biaya_siswa WHERE nis='$nis'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo "<p class='text-danger'>Gagal mengambil data detail biaya: " . mysqli_error($conn) . "</p>";
    exit;
}

// Menyusun data biaya dan menambahkan bulan serta tahun
$data_biaya = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Ambil bulan dari digit ke-8, 9, dan 10
    $bulan_code = substr($row['kd_biaya'], 7, 3); // Ambil 3 digit bulan
    $tahun_code = substr($row['kd_biaya'], -2); // Ambil 2 digit tahun dari belakang

    // Mapping bulan ke nama bulan
    $bulan_map = [
        'JUL' => 'Juli', 'AGU' => 'Agustus', 'SEP' => 'September', 'OKT' => 'Oktober',
        'NOV' => 'November', 'DES' => 'Desember', 'JAN' => 'Januari', 'FEB' => 'Februari',
        'MAR' => 'Maret', 'APR' => 'April', 'MEI' => 'Mei', 'JUN' => 'Juni'
    ];

    $bulan = isset($bulan_map[$bulan_code]) ? $bulan_map[$bulan_code] : null;
    
    // Mapping tahun ke tahun penuh
    $tahun = '20' . $tahun_code; // Misalnya 24 menjadi 2024, 25 menjadi 2025

    $data_biaya[] = [
        'kd_biaya' => $row['kd_biaya'],
        'jumlah' => $row['jumlah'],
        'thajaran' => $row['thajaran'],
        'bulan' => $bulan,
        'tahun' => $tahun
    ];
}

// Urutkan berdasarkan bulan (Juli hingga Juni)
$urutan_bulan = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];

usort($data_biaya, function($a, $b) use ($urutan_bulan) {
    $posA = $a['bulan'] ? array_search($a['bulan'], $urutan_bulan) : 999;
    $posB = $b['bulan'] ? array_search($b['bulan'], $urutan_bulan) : 999;
    return $posA - $posB;
});

// Output tampilan
?>

<h5>Detail Biaya Siswa</h5>
<p><strong>NIS:</strong> <?= htmlspecialchars($nis) ?></p>
<p><strong>Nama:</strong> <?= htmlspecialchars($nama) ?></p>
<p><strong>Total Biaya Belum Terbayarkan:</strong> Rp. <?= number_format($total_biaya, 0, ',', '.') ?></p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Biaya</th>
            <th>Jumlah</th>
            <th>Tahun Ajaran</th>
          
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        foreach ($data_biaya as $row) {
            // Menampilkan bulan dan tahun
            $bulan_nama = $row['bulan'] ? $row['bulan'] : '-';
            $tahun_nama = $row['tahun'] ? $row['tahun'] : '-';

            echo "<tr>
                    <td>{$no}</td>
                    <td>{$row['kd_biaya']}</td>
                    <td>Rp. " . number_format($row['jumlah'], 0, ',', '.') . "</td>
                    <td>{$row['thajaran']}</td>
                   
                  </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>
