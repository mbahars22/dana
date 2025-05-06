<?php
include '../koneksi.php';
include 'opr_sidebar.php';

// Ambil bulan & tahun sistem
$bulan_sistem = date('n'); // 1-12
$tahun_sistem = date('y'); // dua digit akhir tahun

// Mapping bulan
$bulanMap = [
    'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4,
    'MEI' => 5, 'JUN' => 6, 'JUL' => 7, 'AGU' => 8,
    'SEP' => 9, 'OKT' => 10, 'NOV' => 11, 'DES' => 12
];

// Ambil semua data jumlah > 0
$sql = "SELECT * FROM biaya_siswa WHERE jumlah > 0";
$result = mysqli_query($conn, $sql);

$data_jatuh_tempo = [];
while ($row = mysqli_fetch_assoc($result)) {
    $kd_biaya = $row['kd_biaya'];
    $bulan_str = strtoupper(substr($kd_biaya, 7, 3));
    $tahun_kd = (int)substr($kd_biaya, -2);

    if (!isset($bulanMap[$bulan_str])) continue;

    $angka_bulan_kd = $bulanMap[$bulan_str];

    if (
        ($tahun_kd < $tahun_sistem) ||
        ($tahun_kd == $tahun_sistem && $angka_bulan_kd < $bulan_sistem)
    ) {
        $data_jatuh_tempo[] = $row;
    }
}

// Ambil jumlah per halaman dari URL atau default ke 25
$perPageOptions = [25, 50, 100];
$perPage = isset($_GET['perPage']) && in_array((int)$_GET['perPage'], $perPageOptions) ? (int)$_GET['perPage'] : 25;

$totalData = count($data_jatuh_tempo);
$totalPages = ceil($totalData / $perPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($totalPages, $page));
$start = ($page - 1) * $perPage;
$data_paginated = array_slice($data_jatuh_tempo, $start, $perPage);

// Untuk URL pagination tetap bawa perPage
$baseUrl = "?perPage=$perPage";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tagihan Jatuh Tempo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css"> 
</head>
<body>
<div class="container mt-3 ml-4">
        <div class="row">
            <!-- Kolom kiri kosong -->
            <div class="col-md-2"></div>
            <div class="col-10">
               <!-- Baris judul dan tombol tambah siswa -->
               <div class="row align-items-center mb-1">
                    <div class="col-md-6">
                        <h3 class="m-0">Data Tagihan Jatuh Tempo</h3>
                    </div>
      
        <div>
            <strong>Total Data: <?= $totalData ?></strong>
        </div>
  

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kode Biaya</th>
                <th>Jumlah Belum Lunas</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = $start + 1; ?>
            <?php foreach ($data_paginated as $data): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $data['nis'] ?></td>
                    <td><?= $data['nama'] ?></td>
                    <td><?= $data['kd_biaya'] ?></td>
                    <td><?= number_format($data['jumlah'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mt-3 mb-4 flex-wrap">
    <!-- Dropdown "Tampilkan" -->
    <form method="get" class="d-flex align-items-center mb-2 mb-md-0">
        <label for="perPage" class="me-2">Tampilkan:</label>
        <select name="perPage" id="perPage" class="form-select form-select-sm w-auto me-2" onchange="this.form.submit()">
            <?php foreach ($perPageOptions as $option): ?>
                <option value="<?= $option ?>" <?= $perPage == $option ? 'selected' : '' ?>>
                    <?= $option ?>
                </option>
            <?php endforeach ?>
        </select>
        <input type="hidden" name="page" value="1">
    </form>

    <!-- Pagination Buttons -->
    <div>
        <div class="btn-group" role="group" aria-label="Pagination">
            <a class="btn btn-outline-primary <?= ($page <= 1) ? 'disabled' : '' ?>" href="<?= $baseUrl ?>&page=<?= $page - 1 ?>">Previous</a>
            <button class="btn btn-outline-secondary disabled">Halaman <?= $page ?> dari <?= $totalPages ?></button>
            <a class="btn btn-outline-primary <?= ($page >= $totalPages) ? 'disabled' : '' ?>" href="<?= $baseUrl ?>&page=<?= $page + 1 ?>">Next</a>
        </div>
    </div>
</div>
</div>
</body>
</html>
