<pre>Command: "C:\xampp\mysql\bin\mysqldump.exe" --user=root --password= --host=localhost db_pos > "db_pos_2025-04-16_05-31-36.sql"
Array
(
)
</pre>-- MariaDB dump 10.19  Distrib 10.4.22-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_pos
-- ------------------------------------------------------
-- Server version	10.4.22-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `biaya_siswa`
--

DROP TABLE IF EXISTS `biaya_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `biaya_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `kd_biaya` varchar(20) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `thajaran` varchar(9) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis` (`nis`,`kd_biaya`,`thajaran`)
) ENGINE=InnoDB AUTO_INCREMENT=555 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `biaya_siswa`
--

LOCK TABLES `biaya_siswa` WRITE;
/*!40000 ALTER TABLE `biaya_siswa` DISABLE KEYS */;
INSERT INTO `biaya_siswa` VALUES (481,'34567','Budi','X','SPP-01-JUL-24',0,'2024/2025'),(482,'34567','Budi','X','SPP-01-AGU-24',1000000,'2024/2025'),(483,'34567','Budi','X','SPP-01-SEP-24',1500000,'2024/2025'),(484,'34567','Budi','X','SPP-01-OKT-24',1500000,'2024/2025'),(485,'34567','Budi','X','SPP-01-NOV-24',1500000,'2024/2025'),(486,'34567','Budi','X','SPP-01-DES-24',1500000,'2024/2025'),(487,'34567','Budi','X','SPP-01-JAN-25',1500000,'2024/2025'),(488,'34567','Budi','X','SPP-01-FEB-25',1500000,'2024/2025'),(489,'34567','Budi','X','SPP-01-MAR-25',1500000,'2024/2025'),(490,'34567','Budi','X','SPP-01-APR-25',1500000,'2024/2025'),(491,'34567','Budi','X','SPP-01-MEI-25',1500000,'2024/2025'),(492,'34567','Budi','X','SPP-01-JUN-25',1500000,'2024/2025'),(493,'34567','Budi','X','SAT-25',700000,'2024/2025'),(494,'34567','Budi','X','KJP-01-JUL-24',280000,'2024/2025'),(495,'34567','Budi','X','KJP-01-AGU-24',280000,'2024/2025'),(496,'34567','Budi','X','KJP-01-SEP-24',280000,'2024/2025'),(497,'34567','Budi','X','KJP-01-OKT-24',280000,'2024/2025'),(498,'34567','Budi','X','KJP-01-NOV-24',280000,'2024/2025'),(499,'34567','Budi','X','KJP-01-DES-24',280000,'2024/2025'),(500,'34567','Budi','X','KJP-01-JAN-25',280000,'2024/2025'),(501,'34567','Budi','X','KJP-01-FEB-25',280000,'2024/2025'),(502,'34567','Budi','X','KJP-01-MAR-25',280000,'2024/2025'),(503,'34567','Budi','X','KJP-01-APR-25',280000,'2024/2025'),(504,'34567','Budi','X','KJP-01-MEI-25',280000,'2024/2025'),(505,'34567','Budi','X','KJP-01-JUN-25',280000,'2024/2025'),(506,'34568','Santoso','X','SPP-01-JUL-24',1500000,'2024/2025'),(507,'34568','Santoso','X','SPP-01-AGU-24',1500000,'2024/2025'),(508,'34568','Santoso','X','SPP-01-SEP-24',1500000,'2024/2025'),(509,'34568','Santoso','X','SPP-01-OKT-24',1500000,'2024/2025'),(510,'34568','Santoso','X','SPP-01-NOV-24',1500000,'2024/2025'),(511,'34568','Santoso','X','SPP-01-DES-24',1500000,'2024/2025'),(512,'34568','Santoso','X','SPP-01-JAN-25',1500000,'2024/2025'),(513,'34568','Santoso','X','SPP-01-FEB-25',1500000,'2024/2025'),(514,'34568','Santoso','X','SPP-01-MAR-25',1500000,'2024/2025'),(515,'34568','Santoso','X','SPP-01-APR-25',1500000,'2024/2025'),(516,'34568','Santoso','X','SPP-01-MEI-25',1500000,'2024/2025'),(517,'34568','Santoso','X','SPP-01-JUN-25',1500000,'2024/2025'),(518,'34568','Santoso','X','SAT-25',700000,'2024/2025'),(519,'34568','Santoso','X','KJP-01-JUL-24',280000,'2024/2025'),(520,'34568','Santoso','X','KJP-01-AGU-24',280000,'2024/2025'),(521,'34568','Santoso','X','KJP-01-SEP-24',280000,'2024/2025'),(522,'34568','Santoso','X','KJP-01-OKT-24',280000,'2024/2025'),(523,'34568','Santoso','X','KJP-01-NOV-24',280000,'2024/2025'),(524,'34568','Santoso','X','KJP-01-DES-24',280000,'2024/2025'),(525,'34568','Santoso','X','KJP-01-JAN-25',280000,'2024/2025'),(526,'34568','Santoso','X','KJP-01-FEB-25',280000,'2024/2025'),(527,'34568','Santoso','X','KJP-01-MAR-25',280000,'2024/2025'),(528,'34568','Santoso','X','KJP-01-APR-25',280000,'2024/2025'),(529,'34568','Santoso','X','KJP-01-MEI-25',280000,'2024/2025'),(530,'34568','Santoso','X','KJP-01-JUN-25',280000,'2024/2025'),(531,'34569','Jarwo','XI','SPP-02-JUL-24',600000,'2024/2025'),(532,'34569','Jarwo','XI','SPP-02-AGU-24',600000,'2024/2025'),(533,'34569','Jarwo','XI','SPP-02-SEP-24',600000,'2024/2025'),(534,'34569','Jarwo','XI','SPP-02-OKT-24',600000,'2024/2025'),(535,'34569','Jarwo','XI','SPP-02-NOV-24',600000,'2024/2025'),(536,'34569','Jarwo','XI','SPP-02-DES-24',600000,'2024/2025'),(537,'34569','Jarwo','XI','SPP-02-JAN-25',600000,'2024/2025'),(538,'34569','Jarwo','XI','SPP-02-FEB-25',600000,'2024/2025'),(539,'34569','Jarwo','XI','SPP-02-MAR-25',600000,'2024/2025'),(540,'34569','Jarwo','XI','SPP-02-APR-25',600000,'2024/2025'),(541,'34569','Jarwo','XI','SPP-02-MEI-25',600000,'2024/2025'),(542,'34569','Jarwo','XI','SPP-02-JUN-25',600000,'2024/2025'),(543,'34570','Mulyono','XI','SPP-02-JUL-24',600000,'2024/2025'),(544,'34570','Mulyono','XI','SPP-02-AGU-24',600000,'2024/2025'),(545,'34570','Mulyono','XI','SPP-02-SEP-24',600000,'2024/2025'),(546,'34570','Mulyono','XI','SPP-02-OKT-24',600000,'2024/2025'),(547,'34570','Mulyono','XI','SPP-02-NOV-24',600000,'2024/2025'),(548,'34570','Mulyono','XI','SPP-02-DES-24',600000,'2024/2025'),(549,'34570','Mulyono','XI','SPP-02-JAN-25',600000,'2024/2025'),(550,'34570','Mulyono','XI','SPP-02-FEB-25',600000,'2024/2025'),(551,'34570','Mulyono','XI','SPP-02-MAR-25',600000,'2024/2025'),(552,'34570','Mulyono','XI','SPP-02-APR-25',600000,'2024/2025'),(553,'34570','Mulyono','XI','SPP-02-MEI-25',600000,'2024/2025'),(554,'34570','Mulyono','XI','SPP-02-JUN-25',600000,'2024/2025');
/*!40000 ALTER TABLE `biaya_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenis_transaksi`
--

DROP TABLE IF EXISTS `jenis_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jenis_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kd_biaya` varchar(20) NOT NULL,
  `volume` varchar(2) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `nama_biaya` varchar(100) NOT NULL,
  `th_ajaran` varchar(5) NOT NULL,
  `jumlah` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_transaksi`
--

LOCK TABLES `jenis_transaksi` WRITE;
/*!40000 ALTER TABLE `jenis_transaksi` DISABLE KEYS */;
INSERT INTO `jenis_transaksi` VALUES (25,'SPP-01','12','X','SPP 10','24/25',1500000),(26,'SPP-02','12','XI','SPP11','24/25',600000),(27,'SPP-03','12','XII','SPP12','24/25',700000),(33,'SAT-23','1','XII','SAT THN 2023','22/23',500000),(34,'SAT-24','1','XI','SAT THN 2024','23/24',600000),(35,'SAT-25','1','X','SAT THN 2025','24/25',700000),(86,'KJP-01','12','X','SPP KJP','24/25',280000);
/*!40000 ALTER TABLE `jenis_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_kelas`
--

DROP TABLE IF EXISTS `mst_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mst_kelas` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `kelas` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_kelas`
--

LOCK TABLES `mst_kelas` WRITE;
/*!40000 ALTER TABLE `mst_kelas` DISABLE KEYS */;
INSERT INTO `mst_kelas` VALUES (2,'XI'),(3,'XII'),(5,'X');
/*!40000 ALTER TABLE `mst_kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mst_rombel`
--

DROP TABLE IF EXISTS `mst_rombel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mst_rombel` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `rombel` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mst_rombel`
--

LOCK TABLES `mst_rombel` WRITE;
/*!40000 ALTER TABLE `mst_rombel` DISABLE KEYS */;
INSERT INTO `mst_rombel` VALUES (1,'X-1'),(2,'X-2'),(3,'XI-1'),(4,'XI-2'),(5,'XII-1'),(6,'XII-2'),(7,'XII-3');
/*!40000 ALTER TABLE `mst_rombel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran_siswa`
--

DROP TABLE IF EXISTS `pembayaran_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembayaran_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `method` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran_siswa`
--

LOCK TABLES `pembayaran_siswa` WRITE;
/*!40000 ALTER TABLE `pembayaran_siswa` DISABLE KEYS */;
INSERT INTO `pembayaran_siswa` VALUES (241,'Rafie Dwi','121','Risky adi','X','SPP-01-01_Jan_25','2024/2025',1500000.00,1500000.00,'04.25.0001','2025-04-04 00:00:00','Tunai'),(242,'Rafie Dwi','121','Risky adi','X','SPP-01-02_Feb_25','2024/2025',1500000.00,1000000.00,'04.25.0002','2025-04-05 00:00:00','Tunai'),(243,'Rafie Dwi','121','Risky adi','X','SPP-01-03_Mar_25','2024/2025',1500000.00,500000.00,'04.25.0002','2025-04-05 00:00:00','Transfer'),(244,'Rafie Dwi','121','Risky adi','X','SPP-01-02_Feb_25','2024/2025',500000.00,500000.00,'04.25.0003','2025-04-03 00:00:00','Tunai'),(245,'Rafie Dwi','121','Risky adi','X','SPP-01-04_Apr_25','2024/2025',1500000.00,1000000.00,'04.25.0003','2025-03-30 00:00:00','Tunai'),(246,'Rafie Dwi','121','Risky adi','X','SAT-25','2024/2025',700000.00,700000.00,'04.25.0004','2025-04-05 00:00:00','Tunai'),(247,'Rafie Dwi','34568','Siti Aminah','XI','SPP-02-01_Jan_25','2024/2025',600000.00,500000.00,'04.25.0005','2025-04-06 00:00:00','Tunai'),(248,'Rafie Dwi','34568','Siti Aminah','XI','SPP-02-02_Feb_25','2024/2025',600000.00,600000.00,'04.25.0005','2025-04-06 00:00:00','Tunai'),(249,'Rafie Dwi','121','Risky adi','X','SPP-01-01_Jan_25','2024/2025',1500000.00,1500000.00,'04.25.0006','2025-04-06 00:00:00','Tunai'),(250,'Rafie Dwi','121','Risky adi','X','SPP-01-02_Feb_25','2024/2025',1500000.00,1500000.00,'04.25.0006','2025-04-06 00:00:00','Tunai'),(251,'Rafie Dwi','121','Risky adi','X','SPP-01-03_Mar_25','2024/2025',1500000.00,1500000.00,'04.25.0006','2025-04-06 00:00:00','Tunai'),(252,'Rafie Dwi','121','Risky adi','X','SPP-01-04_Apr_25','2024/2025',1500000.00,1500000.00,'04.25.0006','2025-04-06 00:00:00','Tunai'),(253,'Rafie Dwi','121','Risky adi','X','SPP-01-05_Mei_25','2024/2025',1500000.00,1500000.00,'04.25.0006','2025-04-06 00:00:00','Tunai'),(254,'Rafie Dwi','34568','Siti Aminah','XI','SAT-24','2024/2025',600000.00,600000.00,'04.25.0007','2025-04-06 00:00:00','Tunai'),(255,'Rafie Dwi','34568','Siti Aminah','XI','SPP-02-01_Jan_25','2024/2025',100000.00,100000.00,'04.25.0008','2025-04-07 00:00:00','Tunai'),(256,'Rafie Dwi','121','Risky adi','X','SPP-01-JUL-24','2024/2025',1500000.00,1000000.00,'04.25.0009','2025-04-08 00:00:00','Tunai'),(257,'','','','','','',0.00,0.00,'','0000-00-00 00:00:00',''),(258,'Rafie Dwi','121','Risky adi','X','SPP-01-JUL-24','2024/2025',500000.00,500000.00,'04.25.0010','2025-04-12 00:00:00','Tunai'),(259,'Rafie Dwi','34567','Budi','X','SPP-01-JUL-24','2024/2025',1500000.00,1000000.00,'04.25.0011','2025-04-12 00:00:00','Tunai'),(260,'Rafie Dwi','34567','Budi','X','SPP-01-AGU-24','2024/2025',1500000.00,500000.00,'04.25.0011','2025-04-12 00:00:00','Tunai'),(261,'Rafie Dwi','34567','Budi','X','SPP-01-JUL-24','2024/2025',500000.00,500000.00,'04.25.0012','2025-04-14 00:00:00','Tunai');
/*!40000 ALTER TABLE `pembayaran_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thajaran` varchar(9) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `t_lahir` varchar(25) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `kelas` varchar(20) NOT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `rombel` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis` (`nis`),
  UNIQUE KEY `nis_2` (`nis`,`thajaran`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
INSERT INTO `siswa` VALUES (56,'2024/2025','34568','Santoso','Bandung','2007-02-02','X','Jl. Sudirman No. 26','X-2'),(57,'2024/2025','34569','Jarwo','Jakarta','2007-08-10','XI','Jl. Merdeka No. 12','XI-1'),(58,'2024/2025','34570','Mulyono','Bandung','2008-02-15','XI','Jl. Sudirman No. 27','XI-2');
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_ajaran`
--

DROP TABLE IF EXISTS `tb_ajaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_ajaran` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `th_ajaran` varchar(9) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_ajaran`
--

LOCK TABLES `tb_ajaran` WRITE;
/*!40000 ALTER TABLE `tb_ajaran` DISABLE KEYS */;
INSERT INTO `tb_ajaran` VALUES (1,'2024/2025'),(2,'2023/2024');
/*!40000 ALTER TABLE `tb_ajaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_method`
--

DROP TABLE IF EXISTS `tb_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_method` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `method` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_method`
--

LOCK TABLES `tb_method` WRITE;
/*!40000 ALTER TABLE `tb_method` DISABLE KEYS */;
INSERT INTO `tb_method` VALUES (1,'Tunai'),(2,'Transfer');
/*!40000 ALTER TABLE `tb_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tb_profile`
--

DROP TABLE IF EXISTS `tb_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tb_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_sekolah` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `kep_sek` varchar(50) NOT NULL,
  `alamat` varchar(150) DEFAULT NULL,
  `logo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tb_profile`
--

LOCK TABLES `tb_profile` WRITE;
/*!40000 ALTER TABLE `tb_profile` DISABLE KEYS */;
INSERT INTO `tb_profile` VALUES (1,'SMA SIAGA SAJA','Terakreditasi ','Siapa Saja, M.M','Jl. Aja Terus Saja Jangan Belok Telp. 021.xxxxxx','logo_1744689526.PNG');
/*!40000 ALTER TABLE `tb_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi_filet`
--

DROP TABLE IF EXISTS `transaksi_filet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaksi_filet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tgl_transaksi` date DEFAULT NULL,
  `nis` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `kd_transaksi` varchar(50) NOT NULL,
  `jenis_transaksi` enum('pembayaran','refund') NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi_filet`
--

LOCK TABLES `transaksi_filet` WRITE;
/*!40000 ALTER TABLE `transaksi_filet` DISABLE KEYS */;
INSERT INTO `transaksi_filet` VALUES (1,'2025-03-20','1234','Rafie','XII','03.25.0001','',0.00,'Lunas','2025-03-20 03:43:12');
/*!40000 ALTER TABLE `transaksi_filet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','siswa') NOT NULL,
  `thajaran` varchar(9) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Rafie Dwi','Admin','$2y$10$MbGSuzBpTtI.eK84t343vuaGcDeogEX2VXWqnhw6htLIBcK9ExPqO','admin','2024/2025'),(6,'Rafie Dwi','Admin2','$2y$10$MbGSuzBpTtI.eK84t343vuaGcDeogEX2VXWqnhw6htLIBcK9ExPqO','admin','2023/2024');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!4