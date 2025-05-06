-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 12:46 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `biaya_siswa`
--

CREATE TABLE `biaya_siswa` (
  `id` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `kd_biaya` varchar(20) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `thajaran` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_transaksi`
--

CREATE TABLE `jenis_transaksi` (
  `id` int(11) NOT NULL,
  `kd_biaya` varchar(20) NOT NULL,
  `volume` varchar(2) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `th_ajaran` varchar(5) NOT NULL,
  `jumlah` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `jenis_transaksi`
--

INSERT INTO `jenis_transaksi` (`id`, `kd_biaya`, `volume`, `kelas`, `th_ajaran`, `jumlah`) VALUES
(159, 'SPP_01', '12', 'X', '24/25', 500000),
(160, 'SPP_02', '12', 'XI', '24/25', 500000),
(161, 'SPP_03', '12', 'XII', '24/25', 400000),
(162, 'KOP_01', '1', 'X', '24/25', 700000),
(163, 'SAT_24', '1', 'X', '24/25', 700000);

-- --------------------------------------------------------

--
-- Table structure for table `mst_kelas`
--

CREATE TABLE `mst_kelas` (
  `id` int(3) NOT NULL,
  `kelas` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mst_kelas`
--

INSERT INTO `mst_kelas` (`id`, `kelas`) VALUES
(2, 'XI'),
(3, 'XII'),
(5, 'X');

-- --------------------------------------------------------

--
-- Table structure for table `mst_rombel`
--

CREATE TABLE `mst_rombel` (
  `id` int(3) NOT NULL,
  `rombel` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mst_rombel`
--

INSERT INTO `mst_rombel` (`id`, `rombel`) VALUES
(1, 'X-1'),
(2, 'X-2'),
(15, 'X-3'),
(16, 'X-4'),
(17, 'X-5'),
(18, 'X-6'),
(19, 'XI-1'),
(20, 'XI-2'),
(21, 'XI-3'),
(22, 'XI-4'),
(23, 'XI-5'),
(24, 'XI-6'),
(25, 'XII MIPA 1'),
(26, 'XII MIPA 2'),
(27, 'XII MIPA 3'),
(28, 'XII MIPA 4'),
(29, 'XII IPS 1'),
(30, 'XII IPS 2'),
(31, 'XII IPS 3'),
(32, 'XII IPS 4'),
(33, 'XII IPS 5');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran_siswa`
--

CREATE TABLE `pembayaran_siswa` (
  `id` int(11) NOT NULL,
  `user` varchar(50) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `kd_biaya` varchar(20) NOT NULL,
  `thajaran` varchar(9) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `bayar` decimal(10,2) NOT NULL,
  `kd_transaksi` varchar(20) NOT NULL,
  `tgl_trans` datetime NOT NULL DEFAULT current_timestamp(),
  `method` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id` int(11) NOT NULL,
  `thajaran` varchar(9) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `t_lahir` varchar(25) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `kelas` varchar(20) NOT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `rombel` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id`, `thajaran`, `nis`, `nama`, `t_lahir`, `tgl_lahir`, `kelas`, `alamat`, `rombel`) VALUES
(5120, '2025/2026', '34567', 'Budi', 'Jakarta', '2006-07-28', 'X', 'Jl. Merdeka No. 11', 'X-1'),
(5121, '2025/2026', '34568', 'Santoso', 'Bandung', '2007-02-02', 'X', 'Jl. Sudirman No. 26', 'X-2'),
(5122, '2025/2026', '34569', 'Jarwo', 'Jakarta', '2007-08-10', 'XI', 'Jl. Merdeka No. 12', 'XI-1'),
(5123, '2025/2026', '34570', 'Mulyono', 'Bandung', '2008-02-15', 'XI', 'Jl. Sudirman No. 27', 'XI-2');

-- --------------------------------------------------------

--
-- Table structure for table `tb_ajaran`
--

CREATE TABLE `tb_ajaran` (
  `id` int(2) NOT NULL,
  `th_ajaran` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_ajaran`
--

INSERT INTO `tb_ajaran` (`id`, `th_ajaran`) VALUES
(1, '2024/2025'),
(3, '2025/2026');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kd_biaya`
--

CREATE TABLE `tb_kd_biaya` (
  `kd_biaya` varchar(3) NOT NULL,
  `id` int(11) NOT NULL,
  `nm_biaya` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_kd_biaya`
--

INSERT INTO `tb_kd_biaya` (`kd_biaya`, `id`, `nm_biaya`) VALUES
('SPP', 1, 'SPP'),
('SAT', 3, 'Sumbangan Awal Tahun'),
('KOP', 4, 'Koperasi');

-- --------------------------------------------------------

--
-- Table structure for table `tb_method`
--

CREATE TABLE `tb_method` (
  `id` int(2) NOT NULL,
  `method` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_method`
--

INSERT INTO `tb_method` (`id`, `method`) VALUES
(1, 'Tunai'),
(2, 'Transfer'),
(4, 'KJP');

-- --------------------------------------------------------

--
-- Table structure for table `tb_profile`
--

CREATE TABLE `tb_profile` (
  `id` int(11) NOT NULL,
  `nama_sekolah` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `kep_sek` varchar(50) NOT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `kota` varchar(50) NOT NULL,
  `logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_profile`
--

INSERT INTO `tb_profile` (`id`, `nama_sekolah`, `status`, `kep_sek`, `alamat`, `kota`, `logo`) VALUES
(1, 'SMA PGRI 4 JAKARTA', 'Terakreditasi A', 'Ade Syamsudin, M.Pd', 'Jl. Raya Cipayung No.8 Telp. 021.22876501', 'Jakarta', 'logo_1745289921.png');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_filet`
--

CREATE TABLE `transaksi_filet` (
  `id` int(11) NOT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `kd_transaksi` varchar(50) NOT NULL,
  `jenis_transaksi` enum('pembayaran','refund') NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaksi_filet`
--

INSERT INTO `transaksi_filet` (`id`, `tgl_transaksi`, `nis`, `nama`, `kelas`, `kd_transaksi`, `jenis_transaksi`, `jumlah`, `keterangan`, `created_at`) VALUES
(1, '2025-03-20', '1234', 'Rafie', 'XII', '03.25.0001', '', '0.00', 'Lunas', '2025-03-20 03:43:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL,
  `thajaran` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user`, `username`, `password`, `role`, `thajaran`) VALUES
(10, 'admin', 'admin', '$2y$10$crB2Auk63rkgvGfEg90Hl.S1LhzRX1Bum8jFiw9BgdsaVn6pcOt6K', 'admin', '2024/2025'),
(13, 'Oprator', 'oprator', '$2y$10$zxj7jJ22UtIOZYRE1VMR5uUr/0fGmapUQSpRPQAJ8YRshugFjUpDm', 'operator', '2024/2025');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `biaya_siswa`
--
ALTER TABLE `biaya_siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`,`kd_biaya`,`thajaran`);

--
-- Indexes for table `jenis_transaksi`
--
ALTER TABLE `jenis_transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mst_kelas`
--
ALTER TABLE `mst_kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mst_rombel`
--
ALTER TABLE `mst_rombel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran_siswa`
--
ALTER TABLE `pembayaran_siswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD UNIQUE KEY `nis_2` (`nis`,`thajaran`);

--
-- Indexes for table `tb_ajaran`
--
ALTER TABLE `tb_ajaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_kd_biaya`
--
ALTER TABLE `tb_kd_biaya`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_method`
--
ALTER TABLE `tb_method`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_profile`
--
ALTER TABLE `tb_profile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_filet`
--
ALTER TABLE `transaksi_filet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `biaya_siswa`
--
ALTER TABLE `biaya_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76579;

--
-- AUTO_INCREMENT for table `jenis_transaksi`
--
ALTER TABLE `jenis_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `mst_kelas`
--
ALTER TABLE `mst_kelas`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mst_rombel`
--
ALTER TABLE `mst_rombel`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `pembayaran_siswa`
--
ALTER TABLE `pembayaran_siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=356;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5124;

--
-- AUTO_INCREMENT for table `tb_ajaran`
--
ALTER TABLE `tb_ajaran`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_kd_biaya`
--
ALTER TABLE `tb_kd_biaya`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_method`
--
ALTER TABLE `tb_method`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_profile`
--
ALTER TABLE `tb_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transaksi_filet`
--
ALTER TABLE `transaksi_filet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
