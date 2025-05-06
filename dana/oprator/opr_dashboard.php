<?php
include '../koneksi.php'; // Pastikan file koneksi database tersedia
include  'opr_sidebar.php';


$bulan_map = [
    'JAN' => 1, 'FEB' => 2, 'MAR' => 3,
    'APR' => 4, 'MAY' => 5, 'JUN' => 6,
    'JUL' => 7, 'AUG' => 8, 'SEP' => 9,
    'OCT' => 10, 'NOV' => 11, 'DEC' => 12
];

$current_month = date('n');
$current_year  = date('Y');

$query  = "SELECT kd_biaya, jumlah FROM biaya_siswa WHERE jumlah <> 0 AND thajaran = '$thajaran'";
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

$thajaran = $_SESSION['thajaran']; // ambil dari session

$query = "SELECT COUNT(*) AS total FROM siswa WHERE thajaran = '$thajaran'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
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

$bulan_aktif = date('Y-m');

// Inisialisasi array
$data_bulanan = [];
$labels_bulanan = [];

// Query untuk ambil total pembayaran bulan aktif
$sql_bulanan = "SELECT DATE_FORMAT(tgl_trans, '%Y-%m') AS bulan, SUM(bayar) AS total
                FROM pembayaran_siswa
                WHERE DATE_FORMAT(tgl_trans, '%Y-%m') = '$bulan_aktif'
                GROUP BY bulan
                ORDER BY bulan";
$res_bulanan = mysqli_query($conn, $sql_bulanan);

// Cek hasil query
if ($res_bulanan && mysqli_num_rows($res_bulanan) > 0) {
    while ($row = mysqli_fetch_assoc($res_bulanan)) {
        $labels_bulanan[] = $row['bulan'];
        $data_bulanan[]   = (int) $row['total'];
    }
} else {
    // Jika tidak ada data, isi default
    $labels_bulanan[] = $bulan_aktif;
    $data_bulanan[]   = 0;
}

// Hitung total bayar bulan ini
$total_bulan_ini = array_sum($data_bulanan);



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

$bulanInggris = date('M'); // contoh: May
$bulanMap = [
    'Jan' => 'JAN',
    'Feb' => 'FEB',
    'Mar' => 'MAR',
    'Apr' => 'APR',
    'May' => 'MEI',
    'Jun' => 'JUN',
    'Jul' => 'JUL',
    'Aug' => 'AGU',
    'Sep' => 'SEP',
    'Oct' => 'OKT',
    'Nov' => 'NOV',
    'Dec' => 'DES'
];

$bulan_sekarang = isset($bulanMap[$bulanInggris]) ? $bulanMap[$bulanInggris] : strtoupper($bulanInggris);

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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-TQ6AU5nQs2mBv1xxoEh0fv8kj8F+eZxFv3LcoGeaRjYbROyrmC+HR90B1o8hzlqfWv0dDJ5VQwQtw80nIYKwVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css"> <!-- CSS tambahan -->
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
<div class="container mt-2">
        <div class="row">
            <!-- Kolom kiri kosong -->
            <div class="col-md-1"></div>
            <div class="col-11">
           
               <div class="row align-items-center mb-1">
                    <div class="col-md-11">
                    <h2 class="text-left mb-4"> <i class="fas fa-user"></i> Dashboard Operator</h2>
                    </div>
                   
                    <div class="row g-1 mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-success position-relative overflow-hidden">
                        <div class="bg-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">Jumlah Siswa</h5>
                            <h4><p class="card-text mb-4">Total: <?= $total_siswa ?> Siswa</p></h4>
                            <p class="text-end mb-0"><small><a href="opr_tampil_siswa.php" class="text-white text-decoration-underline">Detail</a></small></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-info position-relative overflow-hidden">
                        <div class="bg-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">Transaksi Hari Ini</h5>
                            
                              <h4>
                                <p class="card-text mb-4">Rp. <?= number_format(getTotalPembayaran('HARI'), 0, ',', '.') ?></p>
                              </h4>
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

                <div class="col-md-3">
                    <div class="card text-white bg-warning position-relative overflow-hidden">
                        <div class="bg-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title">Belum Bayar Bulan ini</h5>
                            <h4 class="card-text mb-4"><?= $total_belum_bayar ?> Siswa</h4>
                            <p class="text-end mb-0"><small><a href="opr_blm_bayar_bln_ini.php" class="text-white text-decoration-underline">Detail</a></small></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
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
                                <small><a href="opr_jatuh_temp.php" class="text-white text-decoration-underline">Detail</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="row g-3 mb-5">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header text-center fw-bold">Harian</div>
                        <div class="card-body">
                            <canvas id="chartHarian" style="height:200px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header text-center fw-bold">Mingguan</div>
                        <div class="card-body">
                            <canvas id="chartMingguan" style="height:200px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
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
                <th>Aksi</th>

              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Hari Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('HARI'), 0, ',', '.') ?></td>
                <td>
                <a href="#" class="btn btn-warning btn-sm detailBtn"
                    data-bs-toggle="modal" 
                    data-bs-target="#detailModal"
                    data-url="opr_detail_hari_ini.php?tanggal=<?= date('Y-m-d') ?>">
                    Detail
                  </a>
                </td>

              </tr>
              <tr>
                <td>Minggu Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('MINGGU'), 0, ',', '.') ?></td>
                <td> <?php
                $today = date('Y-m-d');
                    $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($today)));
                    $endOfWeek   = date('Y-m-d', strtotime('sunday this week', strtotime($today)));
                    ?>
                    <a href="#" class="btn btn-warning btn-sm detailBtn"
                      data-bs-toggle="modal" 
                      data-bs-target="#detailModalMingguan" 
                      data-url="opr_mingguan.php?start_date=<?= $startOfWeek ?>&end_date=<?= $endOfWeek ?>">
                      Detail
                    </a>
                </td>

              


              </tr>
              <tr>
                <td>Bulan Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('BULAN'), 0, ',', '.') ?></td>
                <td>
                    <a href="#" class="btn btn-warning btn-sm detailBtn"
                        data-bs-toggle="modal" 
                        data-bs-target="#detailModalBulanan" 
                        data-url="opr_bulanan.php?bulan=<?= date('Y-m') ?>">
                        Detail
                    </a>
                </td>

              </tr>
              <tr>
                <td>Tahun Ini</td>
                <td align="right"><?= number_format(getTotalPembayaran('TAHUN'), 0, ',', '.') ?></td>
                <td>
                      <a href="#" class="btn btn-warning btn-sm detailBtnTahunan"
                          data-bs-toggle="modal" 
                          data-bs-target="#detailModalTahunan" 
                          data-url="opr_tahunan.php?tahun=<?= $_SESSION['thajaran'] ?>">
                          Detail
                      </a>
                  </td>

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
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="struk-tab" data-bs-toggle="tab" data-bs-target="#struk" type="button" role="tab" aria-controls="tahun" aria-selected="false">Cetak Struk</button>
          </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content pt-3" id="filterTabContent">

          <!-- Tab Mingguan -->
          <div class="tab-pane fade show active" id="minggu" role="tabpanel" aria-labelledby="minggu-tab">
            <form action="opr_lap_mingguan.php" method="POST">
              <div class="mb-3">
                <label for="awal" class="form-label">Tanggal Awal</label>
                <input type="date" name="awal" id="awal" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="akhir" class="form-label">Tanggal Akhir</label>
                <input type="date" name="akhir" id="akhir" class="form-control" required>
              </div>

              <div class="mb-3">
              <select name="kategori_hari" id="kategori_hari" class="form-control" required>
                <option value="">- Pilih Kategori -</option>
                <?php
                include 'koneksi.php';

                // Query hanya ambil 3 digit pertama dari kd_biaya tanpa volume
                $query = "
                  SELECT DISTINCT 
                    LEFT(kd_biaya, 3) AS kategori
                  FROM jenis_transaksi
                  ORDER BY kategori
                ";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['kategori']}'>{$row['kategori']}</option>";
                }
                ?>
              </select>
              </div>
              <div class="mb-3">
              <button type="submit" name="cetak_pdf" class="btn btn-success w-100">Tampilkan Laporan</button>
              </div>
              <!-- <div class="mb-3">
              <button type="submit" name="download_excel" class="btn btn-success w-100">Download Excel</button>
              </div> -->
            </form>
          </div>

          <!-- Tab Bulanan -->
          <div class="tab-pane fade" id="bulan" role="tabpanel" aria-labelledby="bulan-tab">
            <form action="opr_lap_bulanan.php" method="POST">
              <div class="mb-3">
                <label for="bulan" class="form-label">Pilih Bulan</label>
                <select name="bulan" id="bulan" class="form-control" required>
                    <?php
                      include "../koneksi.php"; // Pastikan sudah konek database
                      
                      // Ambil DISTINCT bulan-tahun dari transaksi
                      $query = mysqli_query($conn, "SELECT DISTINCT DATE_FORMAT(tgl_trans, '%Y-%m') as bulan_trans FROM pembayaran_siswa ORDER BY bulan_trans ASC");
                      
                      // Array nama bulan
                      $bulanArray = [
                        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                        '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                        '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
                      ];
                      
                      while ($data = mysqli_fetch_assoc($query)) {
                        $bulan_tahun = $data['bulan_trans']; // contoh: 2025-04
                        list($tahun, $bulan) = explode('-', $bulan_tahun); // pisah tahun dan bulan
                        
                        $nama_bulan = isset($bulanArray[$bulan]) ? $bulanArray[$bulan] : $bulan;
                        $tahun2digit = substr($tahun, -2); // contoh: 2025 -> 25
                        
                        echo "<option value='$bulan_tahun'>$nama_bulan-$tahun2digit</option>";
                      }
                    ?>
                  </select>
                 </div>
                 <div class="mb-3">
              <select name="kategori_bulan" id="kategori_bulam" class="form-control" required>
                <option value="">- Pilih Kategori -</option>
                <?php
                include 'koneksi.php';

                // Query hanya ambil 3 digit pertama dari kd_biaya tanpa volume
                $query = "
                  SELECT DISTINCT 
                    LEFT(kd_biaya, 3) AS kategori
                  FROM jenis_transaksi
                  ORDER BY kategori
                ";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['kategori']}'>{$row['kategori']}</option>";
                }
                ?>
              </select>
              </div>
                <div class="mn-3">
                  <button type="submit" name="cetak_bulan" class="btn btn-success w-100">Tampilkan Laporan</button>
               </div>
               <!-- <div class="mn-3">
                  <button type="submit" name="excel_bulan" class="btn btn-success w-100">Download Excel</button>
               </div> -->


                </form>
          </div>


                <!-- Tab Tahunan -->
                <div class="tab-pane fade" id="tahun" role="tabpanel" aria-labelledby="tahun-tab">
                  <form id="laporanForm" action="lap_lain.php" method="POST">
                    
                    <!-- Tahun Ajaran -->
                    <div class="mb-3">
                    <label for="thajaran" class="form-label">Pilih Tahun Ajaran</label>
                    <select name="thajaran" id="thajaran" class="form-control" required>
                      <option value="">-- Pilih Tahun Ajaran --</option>
                      <?php
                      include 'koneksi.php'; // pastikan koneksi database sudah benar

                      $query = mysqli_query($conn, "SELECT th_ajaran FROM tb_ajaran ORDER BY th_ajaran DESC");
                      while ($data = mysqli_fetch_assoc($query)) {
                          echo "<option value='{$data['th_ajaran']}'>{$data['th_ajaran']}</option>";
                      }
                      ?>
                      </select>
                    </div>

                    <!-- Rombel -->
                    <div class="mb-3">
                      <label for="rombel" class="form-label">Pilih Rombel</label>
                      <select name="rombel" id="rombel" class="form-control">
                        <option value="">- Semua Rombel -</option>
                        <?php
                        $rombel_query = "
                          SELECT DISTINCT rombel
                          FROM mst_rombel
                          WHERE rombel IS NOT NULL AND rombel != ''
                          ORDER BY rombel
                        ";
                        $rombel_result = $conn->query($rombel_query);
                        while ($rbl = $rombel_result->fetch_assoc()) {
                            echo "<option value='{$rbl['rombel']}'>{$rbl['rombel']}</option>";
                        }
                        ?>
                      </select>
                    </div>

                    <!-- Kategori dan Volume -->
                    <div class="mb-3">
                      <label for="kategori" class="form-label">Pilih Kategori dan Volume</label>
                      <select name="kategori_volume" id="kategori_volume" class="form-control" required>
                        <option value="">- Pilih Kategori dan Volume -</option>
                        <?php
                        include 'koneksi.php';

                        // Query untuk mengambil kategori dan volume yang digabungkan
                        $query = "
                          SELECT DISTINCT 
                            CONCAT(LEFT(kd_biaya, 3), ' ', volume) AS kategori_volume
                          FROM jenis_transaksi
                          ORDER BY kategori_volume
                        ";
                        $result = $conn->query($query);

                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['kategori_volume']}'>{$row['kategori_volume']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div class="mb-3">
                      <button type="submit" name="tahun_pdf" class="btn btn-success w-100">Tampilkan Laporan</button>
                    </div>
                    <!-- <div class="mb-3">
                      <button type="submit" name="excel_tahun" class="btn btn-success w-100">Download Excel</button>
                    </div> -->
                  </form>
                </div>


          <div class="tab-pane fade" id="struk" role="tabpanel" aria-labelledby="struk-tab">
          <form action="cetak_struk_besar.php" method="GET">
              <div class="mb-3">
                <label for="nis" class="form-label">Input NIS</label>
                <input type="text" class="form-control" id="nis" name="nis" placeholder="Masukkan NIS" required>
              </div>
              <div class="mb-3">
                <label for="kd_transaksi" class="form-label">Kode Transaksi</label>
                <input type="text" class="form-control" id="kd_transaksi" name="kd_transaksi" placeholder="Masukkan Kode Transaksi" required>
              </div>
              <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-print mr-2"></i>Cetak Kwitansi</button>
            </form>
          </div>
        </div> <!-- tab-content -->

      </div>
    </div>
  </div>
</div>
   
<!-- Modal Batal Transaksi -->
<div class="modal fade" id="batalTransaksi" tabindex="-1" aria-labelledby="batalTransaksiLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="batal_transaksi.php" method="GET">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="batalTransaksiLabel">Batalkan Transaksi</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="kd_transaksi" class="form-label">Kode Transaksi</label>
            <input type="text" class="form-control" id="kd_transaksi" name="kd_transaksi" placeholder="Masukkan Kode Transaksi" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Lanjutkan</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Modal detail harian-->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width: 80%;"> <!-- Ganti modal-lg ke modal-xl atau custom size -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Detail Pembayaran Hari Ini</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" id="modalDetailContent">
        <!-- Konten akan dimuat di sini -->
      </div>
    </div>
  </div>
</div>


<!-- Modal Mingguan -->
<div class="modal fade" id="detailModalMingguan" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable custom-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Detail Pembayaran Minggu Ini</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" id="modalDetailMingguan" style="max-height: 70vh; overflow-y: auto;">
        <!-- Konten panjang di sini -->
      </div>
    </div>
  </div>
</div>

<!-- Modal Bulanan -->
<div class="modal fade" id="detailModalBulanan" tabindex="-1" aria-labelledby="modalLabelBulanan" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable custom-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabelBulanan">Detail Pembayaran Bulan Ini</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" id="modalDetailBulanan" style="max-height: 70vh; overflow-y: auto;">
        <!-- Data transaksi bulan ini akan dimuat di sini -->
      </div>
    </div>
  </div>
</div>

<!-- Modal Tahunan -->
<div class="modal fade" id="detailModalTahunan" tabindex="-1" aria-labelledby="modalLabelTahunan" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable custom-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabelTahunan">Detail Transaksi Tahun Ajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body" id="modalDetailContentTahunan">
        <!-- Konten akan di-load di sini -->
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

<!-- Tambahkan script untuk menangani perubahan kategori_volume -->
<script>
  document.getElementById('kategori_volume').addEventListener('change', function() {
    var form = document.getElementById('laporanForm');
    var kategoriVolume = this.value;
    
    // Cek jika volume 12, arahkan ke lap_tahunan.php
    if (kategoriVolume === 'SPP 12') {
      form.action = 'opr_lap_tahunan.php';  // Set action form ke lap_tahunan.php
    } else {
      form.action = 'opr_lap_lain.php';     // Set action form ke lap_lain.php
    }
  });

  // harian
  $(document).on('click', '.detailBtn', function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    
    $('#modalDetailContent').html('<p class="text-center">Loading...</p>');
    
    $.get(url, function(data) {
        $('#modalDetailContent').html(data);
    });
});

// mingguan
document.querySelectorAll('.detailBtn').forEach(btn => {
    btn.addEventListener('click', function () {
        let url = this.getAttribute('data-url');
        let modalBody = document.querySelector('#modalDetailMingguan');
        
        // Bersihkan konten modal sebelum diisi
        modalBody.innerHTML = '<div class="text-center">Loading...</div>';
        
        // Lakukan request AJAX untuk memuat konten
        fetch(url)
            .then(response => response.text())
            .then(data => {
                modalBody.innerHTML = data;  // Isi modal dengan data yang diterima
            })
            .catch(error => {
                modalBody.innerHTML = '<div class="text-center text-danger">Error loading content.</div>';
                console.error('Error loading data: ', error);
            });
    });
});

// bulanan
$(document).ready(function() {
    $('.detailBtn').on('click', function() {
        var url = $(this).data('url');
        var targetModal = $(this).data('bs-target'); // dapetin id modal yang mau diisi
        var modalBody = $(targetModal).find('.modal-body');

        // Set loading dulu
        modalBody.html('<div class="text-center">Loading...</div>');

        // Load konten
        $.get(url, function(data) {
            modalBody.html(data);
        }).fail(function() {
            modalBody.html('<div class="text-center text-danger">Gagal memuat data.</div>');
        });
    });
});

// tahunan
$(document).ready(function() {
    // Untuk tombol tahunan
    $('.detailBtnTahunan').on('click', function(e) {
        e.preventDefault(); // cegah reload
        var url = $(this).data('url'); // ambil URL dari data-url
        $('#modalDetailContentTahunan').load(url); // load ke modal
    });
});

</script>
</body>
</html>
