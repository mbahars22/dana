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

        $firstRow = true; // Untuk melewati header di Excel
        foreach ($data as $row) {
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            $nis = $row[0];
            $nama = $row[1];
            $tempat = $row[2];
            $tgl_lahir = date('Y-m-d', strtotime($row[3])); // Pastikan formatnya benar
            $kelas = $row[4];
            $alamat = $row[5];

            $query = "INSERT INTO siswa (nis, nama, t_lahir, tgl_lahir, kelas, alamat) 
                      VALUES ('$nis', '$nama', '$tempat', '$tgl_lahir', '$kelas', '$alamat')";
            mysqli_query($conn, $query);
        }

        $_SESSION['message'] = "Impor data berhasil!";
    } else {
        $_SESSION['message'] = "Gagal mengunggah file.";
    }
}

header("Location: tampil_siswa.php");
exit();
?>
