<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna sudah login dan memiliki role yang sesuai
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'operator') {
    // Jika tidak, redirect ke halaman login
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
       <link rel="stylesheet" href="../css/sidebar.css">
</head> 
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-success fixed-top">
    <!-- <nav class="navbar navbar-dark bg-primary"> -->
    <div class="container-fluid">
        <button class="btn btn-light me-2" id="toggleSidebar">☰</button>
        <a class="navbar-brand" href="opr_dashboard.php"> User : <?php echo $_SESSION['user']; ?>  ::: Tahun Ajaran : <?php echo $_SESSION['thajaran']; ?> </a>
        <a href="logout.php" class="btn btn-light"><i class="fa-solid fa-right-from-bracket"></i>
        Logout</a>
    </div>
</nav>

<!-- <div class="d-flex"> -->
    <!-- Sidebar -->
    <div class="sidebar bg-dark p-1 text-white" id="sidebar">
  <h4 class="m-1">
    <a href="opr_dashboard.php" class="nav-link text-white d-flex align-items-center p-0">
      <img src="<?php echo $logo; ?>" alt="Logo" style="height: 50px; width: 50px; margin-right: 10px; border-radius: 50%;">
      <?php echo $nama_sekolah; ?>
    </a>
  </h4>
           <ul class="nav flex-column p-0 m-0">
           <!-- Label Master keluar dikit -->
            <li class="nav-item ms-0">
                <span class="nav-link text-warning fw-bold">Master</span>
            </li>

            <!-- Submenu Master masuk ke dalam -->
           
            <li class="nav-item ms-4">
                <a href="opr_tampil_siswa.php" class="nav-link text-white">Data Siswa</a>
            </li>
          
            <!-- Menu lain -->
            <li class="nav-item ms-0">
                <span class="nav-link text-warning fw-bold">Proses</span>
            </li>
            <li class="nav-item ms-4">
                <a href="opr_tampil_biaya.php" class="nav-link text-white">Rincian Biaya</a>
            </li>
            
            <li class="nav-item ms-4">
                <a href="opr_transaksi.php" class="nav-link text-white">Transaksi</a>
            </li>
            <li class="nav-item ms-4">
                <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#filterModal">
                    Cetak Laporan
                </a>
            </li>
            
           
        </ul>
    </div>
<!-- </div> -->




        <!-- Konten utama -->
        <div class="content p-4 w-100">
