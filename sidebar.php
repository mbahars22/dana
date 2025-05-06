<?php
include 'koneksi.php'; // pastikan file koneksi lo ada

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
$qSekolah = mysqli_query($conn, "SELECT nama_sekolah FROM tb_profile LIMIT 1");
$dataSekolah = mysqli_fetch_assoc($qSekolah);
$nama_sekolah = $dataSekolah['nama_sekolah'];
$user=($_SESSION["user"]); 
$thajaran=($_SESSION["thajaran"]); 
$result_profile = mysqli_query($conn, "SELECT * FROM tb_profile LIMIT 1");
$profile = mysqli_fetch_assoc($result_profile);

$logo = (!empty($profile['logo']) && file_exists('uploads/' . $profile['logo'])) 
        ? 'uploads/' . $profile['logo'] 
        : 'default-logo.png'; // fallback jika belum ada
?>

<!DOCTYPE html>
<html lang="id">
 <head>
       <link rel="stylesheet" href="css/sidebar.css">
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head> 
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary fixed-top">
    <!-- <nav class="navbar navbar-dark bg-primary"> -->
    <div class="container-fluid">
        <button class="btn btn-light me-2" id="toggleSidebar">☰</button>
        <a class="navbar-brand" href="admin_dashboard2.php">Admin User : <?php echo $_SESSION['user']; ?>  ::: Tahun Ajaran : <?php echo $_SESSION['thajaran']; ?> </a>
        <a href="logout.php" class="btn btn-light"><i class="fa-solid fa-right-from-bracket"></i>
        Logout</a>
    </div>
</nav>

<!-- <div class="d-flex"> -->
    <!-- Sidebar -->
    <div class="sidebar bg-dark p-1 text-white" id="sidebar">
  <h4 class="m-1">
    <a href="admin_dashboard2.php" class="nav-link text-white d-flex align-items-center p-0">
      <img src="<?php echo $logo; ?>" alt="Logo" style="height: 50px; width: 50px; margin-right: 10px; border-radius: 50%;">
      <?php echo $nama_sekolah; ?>
    </a>
  </h4>
  <ul class="nav flex-column p-0 m-0">

<!-- Label Master (klik buat toggle) -->
<li class="nav-item ms-0">
    <span class="nav-link text-warning fw-bold" style="cursor:pointer;" onclick="toggleMenu('masterSubmenu')">
    <i class="fas fa-folder-open fa-2x"></i>   Master
    </span>
</li>

<!-- Submenu Master -->
<div id="masterSubmenu" style="display: none;">
<li class="nav-item ms-4">
        <a href="kd_biaya.php" class="nav-link text-white"> <i class="fas fa-file-invoice-dollar fa-1x"></i> Kode Biaya</a>
    </li>
    <li class="nav-item ms-4">
        <a href="tampil_mst_biaya.php" class="nav-link text-white"> <i class="fas fa-file-invoice-dollar fa-1x"></i> Data Biaya</a>
    </li>
    <li class="nav-item ms-4">
        <a href="tampil_siswa.php" class="nav-link text-white">  <i class="fas fa-user-graduate fa-1x"></i> Data Siswa</a>
    </li>
    <li class="nav-item ms-4">
        <a href="mst_kelas.php" class="nav-link text-white"> <i class="fas fa-school fa-1x"></i> Data Kelas</a>
    </li>
    <li class="nav-item ms-4">
        <a href="mst_rombel.php" class="nav-link text-white"> <i class="fas fa-users fa-1x"></i> Data Rombel</a>
    </li>
    <li class="nav-item ms-4">
        <a href="tahun_ajaran.php" class="nav-link text-white"><i class="fas fa-calendar fa-1x"> </i> Tahun Ajaran</a>
    </li>
    <li class="nav-item ms-4">
        <a href="mst_method.php" class="nav-link text-white">   <i class="fas fa-credit-card fa-1x"></i> Metode Bayar</a>
    </li>
    <li class="nav-item ms-4">
        <a href="profil.php" class="nav-link text-white"> <i class="fas fa-school"></i> Profile Sekolah</a>
    </li>
    <li class="nav-item ms-4">
        <a href="man_user.php" class="nav-link text-white"> <i class="fas fa-users fa-1x"></i> User</a>
    </li>
</div>

<!-- Label Proses (klik buat toggle) -->
<li class="nav-item ms-0">
    <span class="nav-link text-warning fw-bold" style="cursor:pointer;" onclick="toggleMenu('prosesSubmenu')">
    <i class="fas fa-cogs fa-2x"></i> Proses
    </span>
</li>


<!-- Submenu Master -->
<div id="masterSubmenu" style="display: none;">
   
    <li class="nav-item ms-4">
        <a href="tampil_mst_biaya.php" class="nav-link text-white"> <i class="fas fa-file-invoice-dollar fa-1x"></i> Data Biaya</a>
    </li>
    <li class="nav-item ms-4">
        <a href="tampil_siswa.php" class="nav-link text-white"> <i class="fas fa-user-graduate fa-1x"></i> Data Siswa</a>
    </li>
    <li class="nav-item ms-4">
        <a href="mst_kelas.php" class="nav-link text-white"> <i class="fas fa-school fa-1x"></i> Data Kelas</a>
    </li>
    <li class="nav-item ms-4">
        <a href="mst_rombel.php" class="nav-link text-white"> <i class="fas fa-users fa-1x"></i> Data Rombel</a>
    </li>
    <li class="nav-item ms-4">
        <a href="tahun_ajaran.php" class="nav-link text-white"><i class="fas fa-calendar fa-1x"> </i> Tahun Ajaran</a>
    </li>
    <li class="nav-item ms-4">
        <a href="mst_method.php" class="nav-link text-white"> <i class="fas fa-credit-card fa-1x"></i> Metode Bayar</a>
    </li>
    <li class="nav-item ms-4">
        <a href="profil.php" class="nav-link text-white"> <i class="fas fa-school"></i> Profile Sekolah</a>
    </li>
    <li class="nav-item ms-4">
        <a href="man_user.php" class="nav-link text-white"> <i class="fas fa-users fa-1x"></i> User</a>
    </li>
</div>


<!-- Submenu Proses -->
<div id="prosesSubmenu" style="display: none;">
    <li class="nav-item ms-4">
        <a href="tampil_biaya.php" class="nav-link text-white"> <i class="fas fa-file-invoice-dollar"></i> Rincian Biaya</a>
    </li>
    <li class="nav-item ms-4">
        <a href="transaksi2.php" class="nav-link text-white"> <i class="fas fa-receipt"></i>  Transaksi</a>
    </li>
    <li class="nav-item ms-4">
        <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#filterModal"> <i class="fas fa-print"></i> Cetak Laporan</a>
    </li>
    <li class="nav-item ms-4">
        <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#batalTransaksi"> <i class="fas fa-times"></i>  Batal Transaksi</a>
    </li>
</div>

<!-- Label Backup & Restore (klik untuk toggle submenu) -->
<li class="nav-item ms-0">
    <span class="nav-link text-warning fw-bold" style="cursor:pointer;" onclick="toggleMenu('backupRestoreSubmenu')">
        <i class="fas fa-database fa-2x"></i>  Backup & Restore
    </span>
</li>

<!-- Submenu Backup & Restore -->
<div id="backupRestoreSubmenu" style="display: none;">
    <li class="nav-item ms-4">
        <a href="backupdb.php" class="nav-link text-white"><i class="fas fa-download"></i> Backup Data</a>
    </li>
    <li class="nav-item ms-4">
        <a href="restore.php" class="nav-link text-white"><i class="fas fa-upload"></i> Restore Data</a>
    </li>
</div>
</div>
<!-- </div> -->
<!-- Script untuk toggle -->
<script>
  // Fungsi untuk toggle visibilitas submenu
  function toggleMenu(id) {
    var menu = document.getElementById(id);
    if (menu.style.display === "none" || menu.style.display === "") {
        menu.style.display = "block"; // Menampilkan submenu
    } else {
        menu.style.display = "none"; // Menyembunyikan submenu
    }
  }
</script>
        <!-- Konten utama -->
        <div class="content p-4 w-100">
