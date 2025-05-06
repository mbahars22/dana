<?php
include 'koneksi.php';
// include  'sidebar.php';
include 'sidebar.php';
// include 'admin_footer.php';
$bulan_map = [
    'JAN' => 1, 'FEB' => 2, 'MAR' => 3,
    'APR' => 4, 'MAY' => 5, 'JUN' => 6,
    'JUL' => 7, 'AUG' => 8, 'SEP' => 9,
    'OCT' => 10, 'NOV' => 11, 'DEC' => 12
];

$current_month = date('n');
$current_year  = date('Y');

$query  = "SELECT kd_biaya, jumlah FROM biaya_siswa WHERE jumlah <> 0";
$result = mysqli_query($conn, $query);

$total_tagihan = 0;
$total_nominal = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $kd_biaya = $row['kd_biaya'];
    $jumlah   = $row['jumlah'];

    $kode        = strtoupper(substr($kd_biaya, -6));
    $bulan_kode  = substr($kode, 0, 3);
    $tahun_kode  = substr($kode, -2);
    $bulan       = $bulan_map[$bulan_kode] ?? 0;
    $tahun       = 2000 + intval($tahun_kode);

    if ($tahun < $current_year || ($tahun == $current_year && $bulan < $current_month)) {
        $total_tagihan++;
        $total_nominal += $jumlah;
    }
}

$query  = "SELECT COUNT(*) AS total FROM siswa";
$result = mysqli_query($conn, $query);
$data   = mysqli_fetch_assoc($result);
$total_siswa = $data['total'];

// === Data Harian ===
$labels_harian = [];
$data_harian   = [];

$sql_harian  = "SELECT DATE(tgl_trans) AS tanggal, SUM(bayar) AS total 
                FROM pembayaran_siswa 
                WHERE tgl_trans >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY tanggal ORDER BY tanggal";
$res_harian = mysqli_query($conn, $sql_harian);

while ($row = mysqli_fetch_assoc($res_harian)) {
    $labels_harian[] = $row['tanggal'];
    $data_harian[]   = (int) $row['total'];
}

// === Data Mingguan ===
$labels_mingguan = [];
$data_mingguan   = [];

$sql_mingguan = "SELECT YEARWEEK(tgl_trans, 1) AS minggu, SUM(bayar) AS total
                 FROM pembayaran_siswa
                 WHERE tgl_trans >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
                 GROUP BY minggu ORDER BY minggu";
$res_mingguan = mysqli_query($conn, $sql_mingguan);

while ($row = mysqli_fetch_assoc($res_mingguan)) {
    $labels_mingguan[] = 'Minggu ' . substr($row['minggu'], 4);
    $data_mingguan[]   = (int) $row['total'];
}

// === Data Bulanan ===
$labels_bulanan = [];
$data_bulanan   = [];

$sql_bulanan = "SELECT DATE_FORMAT(tgl_trans, '%Y-%m') AS bulan, SUM(bayar) AS total
                FROM pembayaran_siswa
                WHERE tgl_trans >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY bulan ORDER BY bulan";
$res_bulanan = mysqli_query($conn, $sql_bulanan);

while ($row = mysqli_fetch_assoc($res_bulanan)) {
    $labels_bulanan[] = $row['bulan'];
    $data_bulanan[]   = (int) $row['total'];
}

$sql    = "SELECT kd_biaya, bayar FROM pembayaran_siswa";
$result = mysqli_query($conn, $sql);

$jenis_pembayaran = [];

while ($row = mysqli_fetch_assoc($result)) {
    $kode = strtoupper(substr($row['kd_biaya'], 0, 3));
    if (!isset($jenis_pembayaran[$kode])) {
        $jenis_pembayaran[$kode] = 0;
    }
    $jenis_pembayaran[$kode] += $row['bayar'];
}

$bulan_sekarang = strtoupper(date('M'));

$query_belum_bayar = "
    SELECT COUNT(DISTINCT nis) AS total
    FROM biaya_siswa
    WHERE jumlah != 0
    AND SUBSTRING(kd_biaya, -6, 3) = '$bulan_sekarang'
";

$result_belum      = mysqli_query($conn, $query_belum_bayar);
$data_belum        = mysqli_fetch_assoc($result_belum);
$total_belum_bayar = $data_belum['total'];

function getTotalPembayaran($kategori) {
    global $conn;

    switch ($kategori) {
        case 'HARI':
            $where = "DATE(tgl_trans) = CURDATE()";
            break;
        case 'MINGGU':
            $where = "YEARWEEK(tgl_trans, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'BULAN':
            $where = "MONTH(tgl_trans) = MONTH(CURDATE()) AND YEAR(tgl_trans) = YEAR(CURDATE())";
            break;
        case 'TAHUN':
            $where = "YEAR(tgl_trans) = YEAR(CURDATE())";
            break;
        default:
            $where = "1=0"; // default kosong
    }

    $query = "SELECT SUM(bayar) AS total FROM pembayaran_siswa WHERE $where";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['total'] ?? 0;
}
?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    
</head>

<style>
    .bg-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 6rem;
        color: rgba(255, 255, 255, 0.15);
        z-index: 0;
    }
    .card-body {
        position: relative;
        z-index: 1;
        min-height: 160px;
    }
    .modal-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a); /* ungu ke hijau */
    color: white;
  }
</style>
<body>
<div class="main-container">
    <div class="main-content">
    <div class="col-md-2"> </div>
        <div class="container-fluid mt-2">
            <h2 class="text-left mb-4"> <i class="fas fa-tachometer-alt"></i> Dashboard Admin</h2>
</div>
</div>
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <div class="card text-white bg-primary position-relative overflow-hidden">
                        <div class="bg-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">Jumlah Siswa</h5>
                            <h4><p class="card-text mb-4">Total: <?= $total_siswa ?> Siswa</p></h4>
                            <p class="text-end mb-0"><small><a href="tampil_siswa.php" class="text-white text-decoration-underline">Detail</a></small></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-white bg-info position-relative overflow-hidden">
                        <div class="bg-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">Total Pembayaran</h5>
                            <h4><p class="card-text mb-4">Rp. <?= number_format(array_sum($data_bulanan), 0, ',', '.') ?></p></h4>
                            <p class="text-end mb-0">
                                    <small>
                                        <a href="#" 
                                        class="text-white text-decoration-underline" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalPembayaran">
                                        Detail
                                        </a>
                                    </small>
                                </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-white bg-warning position-relative overflow-hidden">
                        <div class="bg-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">Belum Bayar Bulan ini</h5>
                            <h4 class="card-text mb-4"><?= $total_belum_bayar ?> Siswa</h4>
                            <p class="text-end mb-0"><small><a href="#" class="text-white text-decoration-underline">Detail</a></small></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="card text-white bg-danger position-relative overflow-hidden">
                         <div class="bg-icon">
                             <i class="fas fa-calendar-times"></i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                               <h5 class="card-title">Tagihan Jatuh Tempo</h5>
                            <p class="card-text mb-4">
                                Jumlah Tagihan: <?= $total_tagihan ?><br>
                                Total: Rp. <?= number_format($total_nominal, 0, ',', '.') ?>
                            </p>
                            <div class="mt-auto text-end">
                                <small><a href="#" class="text-white text-decoration-underline">Detail</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="row g-3 mb-5">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center fw-bold">Harian</div>
                        <div class="card-body">
                            <canvas id="chartHarian" style="height:200px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center fw-bold">Mingguan</div>
                        <div class="card-body">
                            <canvas id="chartMingguan" style="height:200px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header text-center fw-bold">Bulanan</div>
                        <div class="card-body">
                            <canvas id="chartBulanan" style="height:200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- tampil detail bayar -->
<div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
     <div class="modal-content modal-gradient text-white">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalPembayaranLabel">Detail Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Kategori</th>
                <th>Total Pembayaran (Rp)</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Hari Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('HARI'), 0, ',', '.') ?></td>
              </tr>
              <tr>
                <td>Minggu Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('MINGGU'), 0, ',', '.') ?></td>
              </tr>
              <tr>
                <td>Bulan Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('BULAN'), 0, ',', '.') ?></td>
              </tr>
              <tr>
                <td>Tahun Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('TAHUN'), 0, ',', '.') ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Filter Laporan Bulanan -->
<!-- Modal -->
<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Filter Laporan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="filterTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="minggu-tab" data-bs-toggle="tab" data-bs-target="#minggu" type="button" role="tab" aria-controls="minggu" aria-selected="true">Mingguan</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="bulan-tab" data-bs-toggle="tab" data-bs-target="#bulan" type="button" role="tab" aria-controls="bulan" aria-selected="false">Bulanan</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tahun-tab" data-bs-toggle="tab" data-bs-target="#tahun" type="button" role="tab" aria-controls="tahun" aria-selected="false">Tahunan</button>
          </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content pt-3" id="filterTabContent">

          <!-- Tab Mingguan -->
          <div class="tab-pane fade show active" id="minggu" role="tabpanel" aria-labelledby="minggu-tab">
            <form action="lap_mingguan.php" method="POST">
              <div class="mb-3">
                <label for="awal" class="form-label">Tanggal Awal</label>
                <input type="date" name="awal" id="awal" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="akhir" class="form-label">Tanggal Akhir</label>
                <input type="date" name="akhir" id="akhir" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-success w-100">Tampilkan Laporan</button>
            </form>
          </div>

          <!-- Tab Bulanan -->
          <div class="tab-pane fade" id="bulan" role="tabpanel" aria-labelledby="bulan-tab">
            <form action="lap_bulanan.php" method="POST">
              <div class="mb-3">
                <label for="bulan" class="form-label">Pilih Bulan</label>
                  <select name="bulan" id="bulan" class="form-control" required>
                    <?php
                      $bulanArray = [
                        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                        '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                        '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
                      ];
                      $tahun = date('y'); // Tahun dua digit, contoh 2025 -> 25
                      foreach ($bulanArray as $val => $bln) {
                        echo "<option value='$val'>$bln-$tahun</option>";
                      }
                    ?>
                  </select>

              </div>
              <button type="submit" class="btn btn-success w-100">Tampilkan Laporan</button>
            </form>
          </div>

          <!-- Tab Tahunan -->
          <div class="tab-pane fade" id="tahun" role="tabpanel" aria-labelledby="tahun-tab">
            <form action="lap_lain.php" method="POST">
              <div class="mb-3">
                <label for="thajaran" class="form-label">Pilih Tahun Ajaran</label>
                <select name="thajaran" id="thajaran" class="form-control" required>
                  <?php
                  for ($i = 2022; $i <= date('Y')+1; $i++) {
                      $th = $i . "/" . ($i + 1);
                      echo "<option value='$th'>$th</option>";
                  }
                  ?>
                </select>
              </div>
              <button type="submit" class="btn btn-success w-100">Tampilkan Laporan</button>
            </form>
          </div>

        </div> <!-- tab-content -->

      </div>
    </div>
  </div>
</div>



<?php if (isset($_GET['showModal'])): ?>
<script>
    var modalFilter = new bootstrap.Modal(document.getElementById('modalFilter'));
    modalFilter.show();
</script>
<?php endif; ?>


<script>
    const formatRupiah = value => 'Rp ' + value.toLocaleString('id-ID');

    const chartHarian = new Chart(document.getElementById('chartHarian'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labels_harian); ?>,
            datasets: [{
                label: 'Pembayaran',
                data: <?= json_encode($data_harian); ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: formatRupiah
                    }
                }
            }
        }
    });

    const chartMingguan = new Chart(document.getElementById('chartMingguan'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labels_mingguan); ?>,
            datasets: [{
                label: 'Pembayaran',
                data: <?= json_encode($data_mingguan); ?>,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: formatRupiah
                    }
                }
            }
        }
    });

    const chartBulanan = new Chart(document.getElementById('chartBulanan'), {
        type: 'line',
        data: {
            labels: <?= json_encode($labels_bulanan); ?>,
            datasets: [{
                label: 'Pembayaran',
                data: <?= json_encode($data_bulanan); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: formatRupiah
                    }
                }
            }
        }
    });
</script>

<?php include 'admin_footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
