<?php
session_start();
include 'koneksi.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['fileExcel']) && $_FILES['fileExcel']['error'] == 0) {
        $file = $_FILES['fileExcel']['tmp_name'];
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        $firstRow = true; // Skip header
        $stmt = $conn->prepare("REPLACE INTO siswa (thajaran, nis, nama, t_lahir, tgl_lahir, kelas, alamat, rombel) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($data as $row) {
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            $thajaran   = trim($row[0]);
            $nis        = trim($row[1]);
            $nama       = trim($row[2]);
            $t_lahir    = trim($row[3]);
            $tgl_lahir  = date('Y-m-d', strtotime($row[4]));
            $kelas      = trim($row[5]);
            $alamat     = trim($row[6]);
            $rombel     = trim($row[7]);

            $stmt->bind_param("ssssssss", $thajaran, $nis, $nama, $t_lahir, $tgl_lahir, $kelas, $alamat, $rombel);
            $stmt->execute();
        }

        $stmt->close();
        $_SESSION['message'] = "Impor data siswa berhasil!";
    } else {
        $_SESSION['message'] = "Gagal mengunggah file.";
    }
}

header("Location: tampil_siswa.php");
exit();
?>
