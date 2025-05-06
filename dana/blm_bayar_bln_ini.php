<?php
include 'koneksi.php';
include 'sidebar.php';

$bulanIndo = [
    1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
    5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
    9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES'
];

$bulanSekarang = (int)date('n'); // angka bulan 1–12
$bulan = $bulanIndo[$bulanSekarang];

// Ambil bulan dan tahun dari sistem komputer
// $bulan = strtoupper(date('M')); // APR, MEI, dst
$tahun = date('y'); // 24, 25, dst

// Ambil jumlah data per halaman dari dropdown (default 25)
$perPageOptions = [25, 50, 100, 500];
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $perPageOptions) ? (int)$_GET['limit'] : 25;

// Ambil halaman aktif dari URL (default 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$countQuery = "
    SELECT COUNT(*) AS total
    FROM biaya_siswa b
    WHERE RIGHT(b.kd_biaya, 2) = '$tahun'
      AND SUBSTRING(b.kd_biaya, 7, 3) = '$bulan'
      AND NOT EXISTS (
        SELECT 1 FROM pembayaran_siswa p 
        WHERE p.nis = b.nis AND p.kd_biaya = b.kd_biaya
      )
";
$totalResult = mysqli_query($conn, $countQuery);
$totalData = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalData / $limit);

// Query data siswa belum bayar
$query = "
    SELECT b.nis, b.nama, b.kelas, b.kd_biaya, b.jumlah, b.thajaran
    FROM biaya_siswa b
    WHERE RIGHT(b.kd_biaya, 2) = '$tahun'
      AND SUBSTRING(b.kd_biaya, 8, 3) = '$bulan'
      AND NOT EXISTS (
        SELECT 1 FROM pembayaran_siswa p 
        WHERE p.nis = b.nis AND p.kd_biaya = b.kd_biaya
      )
    ORDER BY b.kelas, b.nama
    LIMIT $offset, $limit
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Belum Bayar Bulan <?= $bulan ?> 20<?= $tahun ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS tambahan -->
</head>
<body>
<div class="container mt-3 ml-4">
        <div class="row">
            <!-- Kolom kiri kosong -->
            <div class="col-md-1"></div>
            <div class="col-11">
               <!-- Baris judul dan tombol tambah siswa -->
               <div class="row align-items-center mb-1">
                    <div class="col-md-6">
                     <h3>Data Siswa Belum Bayar Bulan <?= $bulan ?> 20<?= $tahun ?></h3>
                    </div>
           <div class="col-md-6 text-end">
            <form method="get" class="form-inline">
                <label for="limit" class="form-label me-2">Tampilkan</label>
                <select name="limit" id="limit" onchange="this.form.submit()" class="form-select d-inline w-auto">
                    <?php foreach ($perPageOptions as $opt): ?>
                        <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="ms-2">data / halaman</span>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Kode Biaya</th>
                <th>Jumlah</th>
                <th>Tahun Ajaran</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = $offset + 1;
            if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nis'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['kelas'] ?></td>
                <td><?= $row['kd_biaya'] ?></td>
                <td class="text-end">Rp. <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                <td><?= $row['thajaran'] ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
                <td colspan="7" class="text-center">Semua siswa sudah membayar bulan ini</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
   <!-- Pagination dengan Previous dan Next saja -->
   <?php if ($totalPages > 1): ?>
<nav>
    <div class="d-flex justify-content-center align-items-center gap-2">
        <a class="btn btn-primary <?= $page <= 1 ? 'disabled' : '' ?>" 
           href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>">
            <i class="fas fa-chevron-left"></i> Prev
        </a>

        <span class="badge bg-light text-dark">Halaman <?= $page ?> dari <?= $totalPages ?></span>

        <a class="btn btn-primary <?= $page >= $totalPages ? 'disabled' : '' ?>" 
           href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>">
            Next <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</nav>
<?php endif; ?>

</div>
</body>
</html>
