<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}
$user=($_SESSION["user"]); 
$thajaran=($_SESSION["thajaran"]); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-primary fixed-top">
    <!-- <nav class="navbar navbar-dark bg-primary"> -->
    <div class="container-fluid">
        <button class="btn btn-light me-2" id="toggleSidebar">☰</button>
        <a class="navbar-brand" href="admin_dashboard2.php">Admin User : ::: Tahun Ajaran : <?php echo $_SESSION['thajaran']; ?> </a>
        <a href="logout.php" class="btn btn-light">Logout</a>
    </div>
</nav>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar bg-dark p-1 text-white" id="sidebar">
        <h4>Menu Admin</h4>
        <ul class="nav flex-column p-0 m-0">
    <li class="nav-item"><a href="admin_dashboard2.php" class="nav-link text-white">Dashboard</a></li>
    <li class="nav-item"><a href="tampil_mst_biaya.php" class="nav-link text-white">Master Biaya</a></li>
    <li class="nav-item"><a href="tampil_siswa.php" class="nav-link text-white">Data Siswa</a></li>
    <li class="nav-item"><a href="transaksi2.php" class="nav-link text-white">Transaksi</a></li>
    <li class="nav-item"><a href="input_biaya.php" class="nav-link text-white">Proses Biaya</a></li>
    <li class="nav-item">
        <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#filterModal">
            Buka Modal Filter
        </a>
    </li>
</ul>
    </div>

        <!-- Konten utama -->
        <div class="content p-4 w-100">
